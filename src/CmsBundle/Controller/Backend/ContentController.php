<?php
/**
 * Created by PhpStorm.
 * User: dylan
 * Date: 25/01/16
 * Time: 11:57
 */

namespace Opifer\CmsBundle\Controller\Backend;
use Symfony\Component\HttpFoundation\Request;

use Opifer\ContentBundle\Controller\Backend\ContentController as BaseContentController;

class ContentController extends BaseContentController
{

    public function historyAction(Request $request, $type, $id, $version = 0)
    {
        $this->getDoctrine()->getManager()->getFilters()->disable('draftversion');

        /** @var Environment $environment */
        $environment = $this->get(sprintf('opifer.content.block_%s_environment', $type));
        $environment->load($id, $version);
        $environment->setBlockMode('manage');

        /** @var AbstractDesignSuite $suite */
        $suite = $this->get(sprintf('opifer.content.%s_design_suite', $type));
        $suite->load($id, $version);

        $parameters = array(
            'environment' => $environment,
            'suite' => $suite,
        );

        return $this->render($this->getParameter('opifer_content.content_history_view'), $parameters);
    }
}