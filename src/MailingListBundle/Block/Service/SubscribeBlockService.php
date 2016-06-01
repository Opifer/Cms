<?php

namespace Opifer\MailingListBundle\Block\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Guzzle\Tests\Service\Mock\Command\Sub\Sub;
use Opifer\CmsBundle\Form\Type\CKEditorType;
use Opifer\ContentBundle\Block\Service\AbstractBlockService;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\Block;
use Opifer\ContentBundle\Form\Type\ContentPickerType;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Model\ContentInterface;
use Opifer\ContentBundle\Model\ContentManager;
use Opifer\MailingListBundle\Entity\SubscribeBlock;
use Opifer\MailingListBundle\Entity\Subscription;
use Opifer\MailingListBundle\Form\DataTransformer\MailingListToArrayTransformer;
use Opifer\MailingListBundle\Form\Type\SubscribeType;
use Opifer\MailingListBundle\Manager\SubscriptionManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Subscribe Block Service.
 */
class SubscribeBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var FormFactory */
    protected $formFactory;

    /** @var Request */
    protected $request;

    /** @var RouterInterface */
    protected $router;

    /** @var ObjectManager */
    protected $em;

    /** @var ContentManager */
    protected $contentManager;

    /** @var SubscriptionManager */
    protected $subscriptionManager;

    /** @var Subscription */
    protected $subscription;

    /** @var Form */
    protected $form;

    /** @var bool */
    protected $subscribed;

    /**
     * SubscribeBlockService constructor.
     *
     * @param EngineInterface     $templating
     * @param array               $config
     * @param FormFactory         $formFactory
     * @param Router              $router
     * @param ObjectManager       $em
     * @param ContentManager      $contentManager
     * @param SubscriptionManager $subscriptionManager
     */
    public function __construct(EngineInterface $templating, array $config, FormFactory $formFactory, RouterInterface $router, ObjectManager $em, ContentManager $contentManager, SubscriptionManager $subscriptionManager)
    {
        $this->templating = $templating;
        $this->config = $config;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->em = $em;
        $this->contentManager = $contentManager;
        $this->subscription = new Subscription();
        $this->subscriptionManager = $subscriptionManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        // Default panel
        $builder->add(
            $builder->create('properties', FormType::class)
                ->add('mailingLists', EntityType::class, [
                    'required' => false,
                    'label' => 'label.mailinglist',
                    'class' => 'OpiferMailingListBundle:MailingList',
                    'choice_label' => 'displayName',
                    'expanded' => true,
                    'multiple' => true,
                    'query_builder' => function (EntityRepository $repository) {
                        return $repository->createQueryBuilder('m')
                            ->add('orderBy', 'm.displayName ASC');
                    },
                    'attr' => ['help_text' => 'help.subscribe_mailinglist'],
                ])
                ->add('responseType', ChoiceType::class, [
                    'choices' => [
                        'message' => 'label.subscribe_choice_response_message',
                        'redirect' => 'label.subscribe_choice_response_page',
                    ],
                    'required' => true,
                    'label' => 'label.subscribe_response_message',
                    'expanded' => true,
                    'multiple' => false,
                ])
                ->add('responseMessage', CKEditorType::class, [
                    'label' => 'label.subscribe_response_message',
                ])
                ->add('responseContent', ContentPickerType::class, [
                    'label' => 'label.subscribe_response_page',
                ])
                ->add('id', TextType::class, ['attr' => ['help_text' => 'help.html_id']])
                ->add('extra_classes', TextType::class, ['attr' => ['help_text' => 'help.extra_classes']])
        );

        $builder->get('properties')->get('mailingLists')
            ->addModelTransformer(new MailingListToArrayTransformer($this->em));

        $contentManager = $this->contentManager;
        $builder->get('properties')->get('responseContent')
            ->addModelTransformer(new CallbackTransformer(
                function ($contentId) use ($contentManager) {
                    if (!$contentId || empty($contentId) || !is_int($contentId)) {
                        return;
                    }

                    return $contentManager->getRepository()->find($contentId);
                },
                function ($content) {
                    if ($content && $content instanceof ContentInterface) {
                        return $content->getId();
                    }

                    return;
                }
            ));
    }

    public function setRequest(RequestStack $request)
    {
        $this->request = $request->getCurrentRequest();
    }

    /**
     * @param BlockInterface $block
     */
    public function load(BlockInterface $block)
    {
        $properties = $block->getProperties();
        $opts = array();

        if (isset($properties['responseType']) && $properties['responseType'] == 'redirect') {
            $opts['action'] = $this->router->generate('opifer_mailing_list_subscribe_block', ['id' => $block->getId()]);
        }

        $this->form = $this->formFactory->create(SubscribeType::class, $this->subscription, $opts);
        $this->form->handleRequest($this->request);

        if ($this->form->isValid()) {
            foreach ($this->getMailingLists($block) as $list) {
                $subscription = $this->subscriptionManager->findOrCreate($list, $this->subscription->getEmail());
                $this->subscriptionManager->save($subscription);
            }

            $this->subscribed = true;
        }
    }

    public function getViewParameters(BlockInterface $block)
    {
        $parameters = parent::getViewParameters($block);

        return array_merge($parameters, ['form' => $this->form->createView(), 'subscribed' => $this->subscribed]);
    }

    /**
     * Processes a POST request to subscribe.
     *
     * @param Block $block
     *
     * @return Response
     */
    public function subscribeAction(Block $block)
    {
        $response = $this->execute($block);
        $properties = $block->getProperties();

        if ($this->subscribed && isset($properties['responseType']) && $properties['responseType'] == 'redirect') {
            $content = $this->contentManager->getRepository()->find($properties['responseContent']);
            $response = new RedirectResponse($this->router->generate('_content', ['slug' => $content->getSlug()]));
        }
        
        return $response;
    }

    protected function getMailingLists(Block $block)
    {
        $properties = $block->getProperties();

        if (!isset($properties['mailingLists']) || !count($properties['mailingLists'])) {
            return;
        }

        $mailingLists = $this->em->getRepository('OpiferMailingListBundle:MailingList')
            ->findById($properties['mailingLists']);

        return $mailingLists;
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new SubscribeBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Subscribe form', 'subscribe');

        $tool->setIcon('contact_mail')
            ->setDescription('Subscribe with email address to designated mailing lists');

        return $tool;
    }
}
