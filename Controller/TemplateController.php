<?php

namespace Opifer\CmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Template Controller
 */
class TemplateController extends Controller
{
    /**
     * Remove a template
     *
     * @param int $id
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $template = $this->get('opifer.eav.template_manager')->getRepository()->find($id);
        $slug = 'templates';

        if (!$template) {
            return $this->createNotFoundException();
        }

        $relatedContent = $em->getRepository('OpiferCmsBundle:Content')
            ->createValuedQueryBuilder('c')
            ->innerJoin('vs.template', 't')
            ->select('COUNT(c)')
            ->where('t.id = :template')
            ->setParameter('template', $id)
            ->getQuery()
            ->getSingleScalarResult();

        if ($relatedContent > 0) {
            $this->addFlash('error', 'template.delete.warning');

            return $this->redirectToRoute('opifer.crud.index', [
                'slug' => $slug
            ]);
        }

        return $this->forward('OpiferCrudBundle:Crud:delete',[
            'entity' => $template,
            'slug' => $slug,
            'id' => $id
        ]);
    }
}
