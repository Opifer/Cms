<?php

namespace Opifer\ContentBundle\Handler;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Gedmo\Sluggable\Handler\SlugHandlerInterface;
use Gedmo\Sluggable\Mapping\Event\SluggableAdapter;
use Gedmo\Sluggable\SluggableListener;

/**
* Class AliasHandler
*/
class AliasHandler implements SlugHandlerInterface
{

    /**
     * @var SluggableListener
     */
    protected $sluggable;


    /**
     * $options = array(
     *     'separator' => '-',
     *     'field' => 'slug'
     * )
     * {@inheritDoc}
     */
    public function __construct(SluggableListener $sluggable)
    {
        $this->sluggable = $sluggable;
    }

    /**
     * {@inheritDoc}
     */
    public function onChangeDecision(SluggableAdapter $ea, array &$config, $object, &$slug, &$needToChangeSlug)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function postSlugBuild(SluggableAdapter $ea, array &$config, $object, &$slug)
    {
    }

    /**
     * {@inheritDoc}
     */
    public static function validate(array $options, ClassMetadata $meta)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function onSlugCompletion(SluggableAdapter $ea, array &$config, $object, &$slug)
    {
        if(!$slug) {
            return;
        }
        
        $newSlug = $slug;
        $repository = $ea->getObjectManager()->getRepository(get_class($object));
        $usedOptions = $config['handlers'][get_called_class()];
        
        $results = $repository->createQueryBuilder('c')
            ->where("c.".$usedOptions['field']." LIKE :term")
            ->andWhere("c.id != :id")
            ->orderBy('c.'.$usedOptions['field'], 'ASC')
            ->setParameter('term', $slug.'%')
            ->setParameter('id', $object->getId())
            ->getQuery()
            ->getResult();
        
        $i = 1;
        
        foreach ($results as $content) {
            if($content->getSlug() == $newSlug) {
                $newSlug = $slug . $usedOptions['separator'] . $i;
                $i++;
            }
        }
        
        $slug = $newSlug;
    }

    /**
     * {@inheritDoc}
     */
    public function handlesUrlization()
    {
        return true;
    }
}
