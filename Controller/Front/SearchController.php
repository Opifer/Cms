<?php

namespace Opifer\CmsBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Content;
use Opifer\CmsBundle\Entity\Layout;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\TwitterBootstrap3View;

/**
 * class SearchController
 */
class SearchController extends Controller
{
    /**
     * Perform search action with pagination
     *
     * @param Layout $layout
     * @param Content $content
     *
     * @return Response
     */
    public function indexAction(Layout $layout, Content $content)
    {
        $request = $this->get('request_stack')->getMasterRequest();
        $term = $request->get('term');
        $contentRepository = $this->getDoctrine()->getRepository('OpiferCmsBundle:Content');
        $results = $contentRepository->searchNested($term);

        $contentResults = [];

        $valueSetIds = [];

        foreach($results as $k => $result) {
            if($result->getNestedIn()) {
                $id = $result->getNestedIn()->getValueset()->getId();
                $valueSetIds[] = $id;
            } else {
                $contentResults[$result->getId()] = $result;
            }
        }

        $content_data = $contentRepository->findBy(['valueSet' => $valueSetIds]);
        foreach($content_data as $result) {
            if($result->getSearchable()) {
                $contentResults[$result->getId()] = $result;
            }
        }

        $results = $contentResults;

        $adapter = new ArrayAdapter($results);

        $page = (int) $request->query->get('page', 1);

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(25);
        $pagerfanta->setCurrentPage($page);

        $view = new TwitterBootstrap3View();

        $options = [
            'prev_message' => $this->get('translator')->trans('pagination.prev'),
            'next_message' => $this->get('translator')->trans('pagination.next'),
        ];

        $routeGenerator = function ($page) use ($term) {
            return '?term='.$term.'&page='.$page;
        };

        $pagination = $view->render($pagerfanta, $routeGenerator, $options);

        return $this->render($layout->getFilename(), [
            'results' => $results,
            'term'    => $term,
            'content' => $content,
            "pagination" => $pagination,
            "pagerfanta" => $pagerfanta,
        ]);
    }
}
