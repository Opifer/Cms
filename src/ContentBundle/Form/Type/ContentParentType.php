<?php

namespace Opifer\ContentBundle\Form\Type;

use Doctrine\ORM\Query;
use Opifer\ContentBundle\Model\ContentManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Content Parent Form Type
 */
class ContentParentType extends AbstractType
{
    /** @var ContentManagerInterface */
    protected $contentManager;

    /**
     * Constructor.
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
        $builder->addModelTransformer(new CallbackTransformer(
            function ($original) {
                if (is_null($original)) {
                    return null;
                }

                return $original->getId();
            },
            function ($submitted) {
                if (null == $submitted) {
                    return null;
                }

                $content = $this->contentManager->getRepository()->find($submitted);

                return $content;
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setRequired('site');
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($options['query_builder'])) {
            //if custom query build custom tree
            $qb = $options['query_builder'];
            $results = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
            $tree = $this->contentManager->getRepository()->buildTree($results);
        } else {
            //no custom query build tree of all children
            $tree = $this->contentManager->getRepository()->childrenHierarchy();
        }


        if (is_int($view->vars['data'])) {
            $view->vars['value'] = $view->vars['data'];
        }
        $view->vars['tree'] = $tree;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return EntityType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'opifer_content_parent';
    }
}
