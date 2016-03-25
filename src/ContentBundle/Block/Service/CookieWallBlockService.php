<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Entity\CookieWallBlock;
use Opifer\ContentBundle\Block\Service\AbstractBlockService;
use Opifer\ContentBundle\Block\Service\BlockServiceInterface;
use Opifer\ContentBundle\Block\Tool\Tool;
use Opifer\ContentBundle\Block\Tool\ToolsetMemberInterface;
use Opifer\ContentBundle\Model\BlockInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Opifer\CmsBundle\Form\Type\CKEditorType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * CookieWall Block Service
 */
class CookieWallBlockService extends AbstractBlockService implements BlockServiceInterface, ToolsetMemberInterface
{
    /** @var Session */
    protected $session;

    /** @var array */
    protected $blockIds = [];

    const SESSION_KEY = 'cookiewall-blocks';

    public function __construct(EngineInterface $templating, Session $session, array $config)
    {
        $this->templating = $templating;
        $this->session = $session;
        $this->config = $config;

        if ($session->has(self::SESSION_KEY)) {
            $this->blockIds = $session->get(self::SESSION_KEY);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildManageForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildManageForm($builder, $options);

        $builder->add(
            $builder->create('default', FormType::class, ['virtual' => true])
                ->add('value', CKEditorType::class, [
                    'label' => 'label.message',
                ])
        );
    }

    public function acceptCookiesAction($id)
    {
        array_push($this->blockIds, $id);

        $this->session->set(self::SESSION_KEY, $this->blockIds);

        $response = new JsonResponse;
        $response->setData(['message' => 'Cookiewall block added to session']);
        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function createBlock()
    {
        return new CookieWallBlock;
    }

    /**
     * {@inheritDoc}
     */
    public function getTool(BlockInterface $block = null)
    {
        $tool = new Tool('CookieWall', 'cookiewall');

        $tool->setIcon('info')
            ->setDescription('Cookiewall');

        return $tool;
    }

    public function getViewParameters(BlockInterface $block)
    {
        $parameters = [
            'block_service' => $this,
            'block'         => $block,
        ];

        if (in_array($parameters['block']->getId(), $this->blockIds)) {
            $parameters['closed'] = true;
        }

        return $parameters;
    }
}
