<?php

namespace Opifer\MediaBundle\Form\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Transforms an array of ids to an ArrayCollection of Media items.
 */
class IdsToMediaTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($media)
    {
        if (null === $media) {
            return "";
        }

        $ids = [];
        foreach ($media as $item) {
            $ids[] = $item->getId();
        }

        return $ids;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($ids)
    {
        if (!$ids) {
            return null;
        }

        $repo = $this->om->getRepository('OpiferMediaBundle:Media');
        $media = $repo->createQueryBuilder('m')
            ->where('m.id IN(:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult()
        ;

        if (null === $media) {
            throw new TransformationFailedException(sprintf(
                'A media item with id "%s" does not exist!',
                $id
            ));
        }

        return $media;
    }
}
