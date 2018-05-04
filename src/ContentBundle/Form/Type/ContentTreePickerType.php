<?php

namespace Opifer\ContentBundle\Form\Type;

use Opifer\ContentBundle\Block\Service\NavigationBlockService;
use Opifer\ContentBundle\Model\ContentManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Multi Content Picker Form Type
 */
class ContentTreePickerType extends AbstractType
{
    /** @var ContentManager */
    protected $contentManager;

    /** @var NavigationBlockService */
    protected $blockService;

    /**
     * Constructor
     *
     * @param ContentManager         $contentManager
     * @param NavigationBlockService $blockService
     */
    public function __construct(ContentManager $contentManager, NavigationBlockService $blockService)
    {
        $this->contentManager = $contentManager;
        $this->blockService = $blockService;
    }

    /**
     * Builds the form and transforms the model data.
     *
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CallbackTransformer(
            function ($original) {
                $tree = $this->blockService->getTree($original);

                return json_encode($tree);
            },
            function ($submitted) {
                $array = json_decode($submitted, true);

                $stripped = $this->stripMetadata($array);

                return trim(json_encode($stripped));
            }
        ));
    }

    /**
     * Strips metadata that should not be stored
     *
     * @param array $array
     * @param array $stripped
     * @return array
     */
    protected function stripMetadata(array $array, $stripped = [])
    {
        $allowed = ['id', '__children'];
        foreach ($array as $item) {
            if (count($item['__children'])) {
                $item['__children'] = $this->stripMetadata($item['__children'], $stripped);
            }

            $stripped[] = array_intersect_key($item, array_flip($allowed));
        }

        return $stripped;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextareaType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'content_tree_picker';
    }
}
