<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\CmsBundle\Form\Type\CKEditorType;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\SocialShareBlock;
use Opifer\ContentBundle\Helper\SocialShareHelper;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Social Share Block Service.
 */
class SocialShareBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var RequestStack */
    protected $requestStack;

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->get('properties')
            ->add('title', TextType::class, [
                'label' => 'label.social_title',
                'attr' => [
                    'help_text' => 'help.block_social_title',
                    'tag' => 'general'
                ],
                'required' => false,
            ])
            ->add('text', CKEditorType::class, [
                'label' => 'label.social_text',
                'attr' => [
                    'help_text' => 'help.block_social_text',
                    'label_col' => 12,
                    'widget_col' => 12,
                    'tag' => 'general'
                ],
                'required' => false,
            ]
        );

        if (count($this->config['templates'])) {
            // Default panel
            $builder->get('properties')
                ->add('template', ChoiceType::class, [
                'label' => 'label.template',
                'placeholder' => 'placeholder.choice_optional',
                'attr' => ['help_text' => 'help.block_template'],
                'choices' => $this->config['templates'],
                'required' => false,
            ]);
        }
    }

    public function setRequestStack(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    protected function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function getViewParameters(BlockInterface $block)
    {
        $socialShareHelper = new SocialShareHelper();

        $url = $this->getRequestUrl();

        $parameters = [
            'block_service' => $this,
            'block' => $block,
            'request_url' => $url,
            'facebook_url' => $socialShareHelper->getFacebookShareUrl($url),
            'twitter_url' => $socialShareHelper->getTwitterShareUrl($url),
            'linkedin_url' => $socialShareHelper->getLinkedInShareUrl($url),
            'google_url' => $socialShareHelper->getGoogleShareUrl($url),
            'whatsapp_url' => $socialShareHelper->getWhatsappShareUrl($url),
            'email_url' => $socialShareHelper->getEmailShareUrl($url),
            'facebook_count' => 0,
            'twitter_count' => 0,
            'linkedin_count' => 0,
            'google_count' => 0,
        ];

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function createBlock()
    {
        return new SocialShareBlock();
    }

    /**
     * {@inheritdoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Social share', 'socialshare');

        $tool->setIcon('share')
            ->setDescription('Adds buttons to share content on social media');

        return $tool;
    }

    public function getRequestUrl()
    {
        return sprintf('%s%s', $this->getRequest()->getSchemeAndHttpHost(), $this->getRequest()->getRequestUri());
    }

    /**
     * @param BlockInterface $block
     * @return string
     */
    public function getDescription(BlockInterface $block = null)
    {
        return 'This adds buttons to share content on social media';
    }
}
