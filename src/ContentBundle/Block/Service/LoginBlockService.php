<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Entity\LoginBlock;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Opifer\ContentBundle\Form\Type\ContentPickerType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Opifer\CmsBundle\Form\Type\CKEditorType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Security\Core\Security;

/**
 * Login Block Service
 */
class LoginBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var Session */
    protected $session;

    /** @var Request */
    protected $request;

    public function __construct(EngineInterface $templating, Container $container, Session $session, array $config)
    {
        $this->templating = $templating;
        $this->container = $container;
        $this->session = $session;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->add(
            $builder->create('default', FormType::class, ['virtual' => true])
                ->add('loginRedirectContentItem', ContentPickerType::class, [
                            'label' => 'label.login_redirect_content_item',
                        ])
                ->add('registrationContentItem', ContentPickerType::class, [
                            'label' => 'label.register_content_item',
                        ])
                ->add('value', CKEditorType::class, [
                    'label' => 'label.message',
                ])
        );

        $builder->add(
            $builder->create('properties', FormType::class)
                ->add('loginRedirectUrl', TextType::class, [
                    'label' => 'label.login_redirect_url',
                ])
                ->add('registrationUrl', TextType::class, [
                    'label' => 'label.register_url',
                ])
        );
    }

    public function setRequest(RequestStack $request)
    {
        $this->request = $request->getCurrentRequest();
    }

    public function getViewParameters(BlockInterface $block)
    {
        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;
        
        // get the error if any (works with forward and redirect -- see below)
        if ($this->request->attributes->has($authErrorKey)) {
            $error = $this->request->attributes->get($authErrorKey);
        } elseif (null !== $this->session && $this->session->has($authErrorKey)) {
            $error = $this->session->get($authErrorKey);
            $this->session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        // last username entered by the user
        $lastUsername = (null === $this->session) ? '' : $this->session->get($lastUsernameKey);

        $csrfToken = $this->container->get('security.csrf.token_manager')->getToken('authenticate')->getValue();

        $parameters = [
            'block_service' => $this,
            'block'         => $block,
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $csrfToken
        ];
        
        return $parameters;
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new LoginBlock;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Login', 'login');

        $tool->setIcon('receipt')
            ->setDescription('Shows login form');

        return $tool;
    }
}
