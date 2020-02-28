<?php

namespace Opifer\CmsBundle\Block\Service;

use Opifer\CmsBundle\Entity\LoginBlock;
use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Block\Service\AbstractBlockService;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Opifer\ContentBundle\Form\Type\ContentPickerType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Opifer\CmsBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

/**
 * Login Block Service.
 */
class LoginBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var Session */
    protected $session;

    /** @var RequestStack */
    protected $requestStack;

    /** @var CsrfTokenManager */
    protected $csrfTokenManager;

    /**
     * LoginBlockService constructor.
     *
     * @param BlockRenderer    $blockRenderer
     * @param CsrfTokenManager $csrfTokenManager
     * @param Session          $session
     * @param array            $config
     */
    public function __construct(BlockRenderer $blockRenderer, CsrfTokenManager $csrfTokenManager, Session $session, array $config)
    {
        parent::__construct($blockRenderer, $config);

        $this->csrfTokenManager = $csrfTokenManager;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->get('default')
            ->add('value', CKEditorType::class, [
                'label' => 'label.message',
            ]);

        $builder->get('properties')
            ->add('redirect_content', ContentPickerType::class, [
                'label' => 'label.login_redirect_content_item',
                'as_object' => false,
                'attr' => [
                    'help_text' => 'help.login_redirect'
                ]
            ])
            ->add('register_content', ContentPickerType::class, [
                'label' => 'label.register_content_item',
                'as_object' => false,
                'attr' => [
                    'help_text' => 'help.register_content_item'
                ]
            ])
            ->add('allowEmail', CheckboxType::class, [
                'label' => 'label.allow_email',
                'attr' => [
                    'align_with_widget'     => true,
                    'help_text'             => 'help_text.allow_email',
                ],
            ])
            ->add('allowFacebook', CheckboxType::class, [
                'label' => 'label.allow_facebook',
                'attr' => [
                    'align_with_widget'     => true,
                    'help_text'             => 'help_text.allow_facebook',
                ],
            ])
            ->add('facebookId', TextType::class, [
                'label' => 'label.facebook_id',
                'attr' => [
                    'help_text'             => 'help_text.facebook_id',
                ],
            ])
            ->add('allowLinkedIn', CheckboxType::class, [
                'label' => 'label.allow_linkedin',
                'attr' => [
                    'align_with_widget'     => true,
                    'help_text'             => 'help_text.allow_linkedin',
                ],
            ])
            ->add('linkedInKey', TextType::class, [
                'label' => 'label.linkedin_key',
                'attr' => [
                    'help_text'             => 'help_text.linkedin_key',
                ],
            ]);
    }

    /**
     * @param BlockInterface $block
     *
     * @return array
     */
    public function getViewParameters(BlockInterface $block)
    {
        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;

        // get the error if any (works with forward and redirect -- see below)
        if ($this->getRequest()->attributes->has($authErrorKey)) {
            $error = $this->getRequest()->attributes->get($authErrorKey);
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
        $csrfToken = $this->csrfTokenManager->getToken('authenticate')->getValue();
        $parameters = [
            'block_service' => $this,
            'block' => $block,
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $csrfToken
        ];

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new LoginBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Login', 'login');
        $tool->setIcon('receipt')
            ->setDescription('Shows login form');

        return $tool;
    }

    /**
     * @param RequestStack $requestStack
     */
    public function setRequest(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return null|Request
     */
    protected function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }
}
