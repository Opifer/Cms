<?php

namespace Opifer\ContentBundle\Form\Type;

use Opifer\ContentBundle\Model\ContentManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * NavLink Form Type.
 */
class NavLinkType extends AbstractType
{
    /** @var ContentManagerInterface */
    protected $contentManager;

    /**
     * Constructor
     *
     * @param ContentManagerInterface $contentManager
     */
    public function __construct(ContentManagerInterface $contentManager)
    {
        $this->contentManager = $contentManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', ContentPickerType::class, [
                'required' => false,
            ])
            ->add('url', TextType::class, [
                'required' => false,
            ])
            ->addModelTransformer(new CallbackTransformer(
                function ($original) {
                    if (!$original) {
                        return [];
                    }

                    if (substr($original, 0, 4) == 'http' || substr($original, 0, 1) == '/') {
                        return [
                            'url' => $original,
                        ];
                    }

                    $content = $this->contentManager->getRepository()->findOneBySlug($original);

                    return [
                        'content' => $content,
                    ];
                },
                function ($submitted) {
                    if (isset($submitted['content']) && $submitted['content']) {
                        return $submitted['content']->getSlug();
                    }

                    return $submitted['url'];
                }
            ))
        ;
    }
}
