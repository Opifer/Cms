<?php

namespace Opifer\MediaBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Opifer\MediaBundle\Model\MediaManagerInterface;

/**
 * Transforms an array of ids to an ArrayCollection of Media items.
 */
class IdsToMediaTransformer implements DataTransformerInterface
{
    /**
     * @var MediaManagerInterface
     */
    private $mediaManager;

    /**
     * @param MediaManagerInterface $mm
     */
    public function __construct(MediaManagerInterface $mm)
    {
        $this->mediaManager = $mm;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($media)
    {
        if (null === $media) {
            return '';
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
            return;
        }

        $repo = $this->mediaManager->getRepository();
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
