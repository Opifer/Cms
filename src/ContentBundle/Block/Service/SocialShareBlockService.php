<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\SocialShareBlock;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Social Share Block Service
 */
class SocialShareBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var Request */
    protected $request;

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $propertiesForm = $builder->create('properties', FormType::class);

        if (count($this->config['templates'])) {
            // Default panel
            $builder->add(
                $propertiesForm->add('template', ChoiceType::class, [
                    'label' => 'label.template',
                    'placeholder' => 'placeholder.choice_optional',
                    'attr' => ['help_text' => 'help.block_template'],
                    'choices' => $this->config['templates'],
                    'required' => false,
                ])
            );
        }
    }

    public function setRequest(RequestStack $request)
    {
        $this->request = $request->getCurrentRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function getViewParameters(BlockInterface $block)
    {
        $parameters = [
            'block_service' => $this,
            'block'         => $block,
            'request_url'  => $this->getRequestUrl(),
            'facebook_url'  => $this->getFacebookShareUrl(),
            'twitter_url'   => $this->getTwitterShareUrl(),
            'linkedin_url'      => $this->getLinkedInShareUrl(),
            'google_url'    => $this->getGoogleShareUrl(),
            'whatsapp_url'  => $this->getWhatsappShareUrl(),
            'email_url'     => $this->getEmailShareUrl(),
            'facebook_count'  => $this->getFacebookShareUrl(),
            'twitter_count'   => $this->getTwitterShareUrl(),
            'linkedin_count'      => $this->getLinkedInShareUrl(),
            'google_count'    => $this->getGoogleShareUrl(),
        ];

        return $parameters;
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new SocialShareBlock();
    }

    /**
     * {@inheritDoc}
     */
    public function getTool()
    {
        $tool = new Tool('Social share', 'socialshare');

        $tool->setIcon('share')
            ->setDescription('Adds buttons to share content on social media');

        return $tool;
    }


    public function getRequestUrl()
    {
        return sprintf('%s%s', $this->request->getSchemeAndHttpHost(), $this->request->getRequestUri());
    }

    public function getFacebookShareUrl()
    {
        return sprintf('http://www.facebook.com/sharer.php?u=%s', urlencode($this->getRequestUrl()));
    }

    public function getFacebookShareCount()
    {
        return 0;
    }

    public function getTwitterShareUrl()
    {
        return sprintf('http://www.facebook.com/sharer.php?u=%s', urlencode($this->getRequestUrl()));
    }

    public function getTwitterShareCount()
    {
        return 0;
    }

    public function getGoogleShareUrl()
    {
        return sprintf('https://plus.google.com/share?url=%s', urlencode($this->getRequestUrl()));
    }

    public function getGoogleShareCount()
    {
        return 0;
    }

    public function getLinkedInShareUrl()
    {
        return sprintf('http://www.linkedin.com/shareArticle?mini=true&amp;url=%s', urlencode($this->getRequestUrl()));
    }

    public function getLinkedInShareCount()
    {
        return 0;
    }

    public function getWhatsappShareUrl()
    {
        return sprintf('whatsapp://send?text=%s', urlencode($this->getRequestUrl()));
    }

    public function getEmailShareUrl()
    {
        return sprintf('mailto:?subject=&amp;body=%s', urlencode($this->getRequestUrl()));
    }
}
