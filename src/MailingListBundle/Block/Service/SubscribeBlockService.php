<?php

namespace Opifer\MailingListBundle\Block\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Opifer\CmsBundle\Form\Type\CKEditorType;
use Opifer\ContentBundle\Block\Service\AbstractBlockService;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Form\Type\ContentPickerType;
use Opifer\ContentBundle\Model\ContentInterface;
use Opifer\ContentBundle\Model\ContentManager;
use Opifer\MailingListBundle\Entity\SubscribeBlock;
use Opifer\MailingListBundle\Form\DataTransformer\MailingListToArrayTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Subscribe Block Service
 */
class SubscribeBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var Request */
    protected $request;

    /** @var ObjectManager */
    protected $em;

    /** @var ContentManager */
    protected $contentManager;

    /**
     * @param EngineInterface $templating
     * @param array $config
     * @param ObjectManager $em
     */
    public function __construct(EngineInterface $templating, array $config, ObjectManager $em, ContentManager $contentManager)
    {
        $this->templating = $templating;
        $this->config = $config;
        $this->em = $em;
        $this->contentManager = $contentManager;
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
                    'required'      => false,
                    'label'         => 'label.mailinglist',
                    'class'         => 'OpiferMailingListBundle:MailingList',
                    'choice_label'  => 'displayName',
                    'expanded'      => true,
                    'multiple'      => true,
                    'query_builder' => function (EntityRepository $repository) {
                        return $repository->createQueryBuilder('m')
                            ->add('orderBy', 'm.displayName ASC');
                    },
                    'attr'          => ['help_text' => 'help.subscribe_mailinglist']
                ])
                ->add('responseType', ChoiceType::class, [
                    'choices'       => [
                        'message'      => 'label.subscribe_choice_response_message',
                        'redirect'     => 'label.subscribe_choice_response_page',
                    ],
                    'required'      => true,
                    'label'         => 'label.subscribe_response_message',
                    'expanded'      => true,
                    'multiple'      => false,
                ])
                ->add('responseMessage', CKEditorType::class, [
                    'label'         => 'label.subscribe_response_message',
                ])
                ->add('responseContent', ContentPickerType::class, [
                    'label'         => 'label.subscribe_response_page',
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
                    if (! $contentId || empty($contentId) || ! is_int($contentId)) {
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

//    /**
//     * {@inheritdoc}
//     */
//    public function getViewParameters(BlockInterface $block)
//    {
//        $parameters = [
//            'block_service' => $this,
//            'block'         => $block,
//            'request_url'  => $this->getRequestUrl(),
//            'facebook_url'  => $this->getFacebookShareUrl(),
//            'twitter_url'   => $this->getTwitterShareUrl(),
//            'linkedin_url'      => $this->getLinkedInShareUrl(),
//            'google_url'    => $this->getGoogleShareUrl(),
//            'whatsapp_url'  => $this->getWhatsappShareUrl(),
//            'email_url'     => $this->getEmailShareUrl(),
//            'facebook_count'  => $this->getFacebookShareUrl(),
//            'twitter_count'   => $this->getTwitterShareUrl(),
//            'linkedin_count'      => $this->getLinkedInShareUrl(),
//            'google_count'    => $this->getGoogleShareUrl(),
//        ];
//
//        return $parameters;
//    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new SubscribeBlock();
    }

    /**
     * {@inheritDoc}
     */
    public function getTool()
    {
        $tool = new Tool('Subscribe form', 'subscribe');

        $tool->setIcon('contact_mail')
            ->setDescription('Subscribe with email address to designated mailing lists');

        return $tool;
    }
}
