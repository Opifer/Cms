<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\CmsBundle\Entity\Content;
use Opifer\ContentBundle\Block\BlockRenderer;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Entity\NavBarBlock;
use Opifer\ContentBundle\Form\Type\ContentListPickerType;
use Opifer\ContentBundle\Model\BlockInterface;
use Opifer\ContentBundle\Model\ContentManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * NavBar Block Service
 */
class NavBarBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    protected $requestStack;

    public function __construct(BlockRenderer $blockRenderer, array $config, RequestStack $requestStack)
    {
        parent::__construct($blockRenderer, $config);
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->get('properties')
            ->add('template', ChoiceType::class, [
                'label' => 'label.template',
                'placeholder' => 'placeholder.choice_optional',
                'attr' => [
                    'help_text' => 'help.block_template',
                    'widget_col' => 9,
                    'tag' => 'styles'
                ],
                'choices' => $this->config['templates'],
                'required' => false,
            ])
        ;
    }

    public function getViewParameters(BlockInterface $block)
    {
        $parameters = parent::getViewParameters($block);

        $request = $this->requestStack->getCurrentRequest();
        $content = $request->attributes->get('content');

        $translationRoutes = [];
        $defaultTranslationRoutes = [
            'nl' => $request->getBaseUrl() . '/',
            'en' => $request->getBaseUrl() . '/en',
            'fr' => $request->getBaseUrl() . '/fr',
            'de' => $request->getBaseUrl() . '/de',
        ];

        if ($content->getTranslationGroup() !== null) {
            $contentTranslations = $this->getEnvironment()->getEntityManager()->getRepository(Content::class)
                ->findBy(['translationGroup' => $content->getTranslationGroup()]);

            foreach ($contentTranslations as $contentTranslation) {
                $translationRoutes[$contentTranslation->getLocale()->getLocale()] = $request->getBaseUrl() . '/' . $contentTranslation->getSlug();
            }
        }

        $translationRoutes[$request->getLocale()] = $request->getRequestUri();

        return array_merge($parameters, $defaultTranslationRoutes, $translationRoutes);
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new NavBarBlock;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Nav bar', 'navbar');

        $tool->setIcon('menu')
            ->setGroup('navigation')
            ->setDescription('A navigation bar');

        return $tool;
    }

    /**
     * @param BlockInterface $block
     * @return string
     */
    public function getDescription(BlockInterface $block = null)
    {
        return 'A navigation bar';
    }
}
