<?php

namespace Opifer\ContentBundle\Block\Service;

use Opifer\ContentBundle\Block\BlockRenderer;
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
    protected $siteIds = [];

    protected $esiEnabled = true;

    const SESSION_KEY = 'cookiewall-blocks';

    public function __construct(BlockRenderer $blockRenderer, Session $session, array $config)
    {
        parent::__construct($blockRenderer, $config);

        $this->session = $session;

        if ($session->has(self::SESSION_KEY)) {
            $this->siteIds = $session->get(self::SESSION_KEY);
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
        $this->setCookie($id);

        $response = new JsonResponse;
        $response->setData(['message' => 'Cookiewall block added to session']);

        return $response;
    }

    /**
     * @param int $id The site ID
     *
     * Note: Uses a fixed ID for now, since multi-site is not supported yet.
     */
    public function setCookie($id)
    {
        array_push($this->siteIds, 1);

        $this->session->set(self::SESSION_KEY, $this->siteIds);
    }

    /**
     * {@inheritdoc}
     */
    public function load(BlockInterface $block)
    {
        parent::load($block);

        if (in_array(1, $this->siteIds)) {
            $block->setAccepted(true);
        }
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
        $tool = new Tool('Cookie wall', 'cookiewall');

        $tool->setIcon('info')
            ->setDescription('Dismissable message regarding EU cookies regulation');

        return $tool;
    }

    public function getViewParameters(BlockInterface $block)
    {
        $parameters = [
            'block_service' => $this,
            'block'         => $block,
        ];

        if (in_array(1, $this->siteIds)) {
            $parameters['closed'] = true;
        }

        return $parameters;
    }
}
