<?php

namespace Opifer\CmsBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AutoCompleteController extends Controller
{
    /**
     * @Route("/autocomplete/{alias}", name="opifer.cms.autocomplete", options={"expose"=true})
     */
    public function queryAction(Request $request, $alias)
    {
        $config = $this->container->getParameter('opifer_cms.autocomplete');

        if (false == isset($config[$alias])) {
            throw new \Exception(sprintf('No config found for autocomplete alias: %s'), $alias);
        }

        $class = $config[$alias]['class'];
        $property = $config[$alias]['property'];

        $em = $this->get('doctrine.orm.default_entity_manager');

        $qb = $em->createQueryBuilder('e');
        $qb->select('e.email AS property')
            ->from($class, 'e')
            ->where('e.'.$property.' LIKE :query')
            ->setMaxResults(10)
            ->setParameter('query', '%'.$request->get('q').'%');

        $results = $qb->getQuery()->getArrayResult();

        $ids = array();
        foreach ($results as $result) {
            $ids[] = $result['property'];
        }

        return new JsonResponse($ids);
    }
}
