<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Entity\VideoBlock;
use Opifer\ContentBundle\Block\Service\AbstractBlockService;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Opifer\CmsBundle\Entity\Form;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Opifer\MediaBundle\Form\Type\MediaPickerType;
use Opifer\CmsBundle\Form\Type\CKEditorType;
use Opifer\MediaBundle\Provider\YoutubeProvider;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Video Block Service
 */
class VideoBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{

    /**
     * @param EngineInterface $templating
     * @param ContainerInterface $container
     * @param array $config
     */
    public function __construct(EngineInterface $templating, Container $container, array $config)
    {
        $this->templating = $templating;
        $this->config = $config;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->add(
            $builder->create('default', FormType::class, ['virtual' => true])
                ->add('title', TextType::class, [
                    'label' => 'Title',
                ])
                ->add('value', CKEditorType::class, [
                    'label' => 'Caption',
                ])
                ->add('media', MediaPickerType::class, [
                    'required'  => false,
                    'multiple' => false,
                    'attr' => array('label_col' => 12, 'widget_col' => 12),
                ])
        );

        $builder->add(
            $builder->create('properties', FormType::class)
                ->add('width', TextType::class, [
                    'label' => 'Width',
                ])
                ->add('heigth', TextType::class, [
                    'label' => 'Height',
                ])
        );
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new VideoBlock;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('Video', 'video');

        $tool->setIcon('link')
            ->setDescription('Adds video');

        return $tool;
    }

    public function getView(BlockInterface $block)
    {
        return 'OpiferContentBundle:Block:Content/video.html.twig';
    }

    /**
     * Returns a Response object that can be cache-able.
     *
     * @param string   $view
     * @param array    $parameters
     * @param Response $response
     *
     * @return Response
     */
    public function renderResponse($view, array $parameters = array(), Response $response = null)
    {
        //if ($parameters['block']->getMedia()->getProvider() == 'youtube') {
            //$mediaManager = $this->container->get('opifer.media.media_manager');
            //$youtubeProvider = $this->container->get('opifer.media.provider.pool')->getProvider('youtube');
            //$videoUrl = $youtubeProvider->getUrl($parameters['block']->getMedia());
            //$parameters['block']->getMedia()->setReference($videoUrl);
        //}
        //dump($parameters['block']->getMedia()->getProvider()); exit;
        //dump($rep->findOneByThumb($parameters['block']->getMedia()));exit;
        return $this->getTemplating()->renderResponse($view, $parameters, $response);
    }

}
