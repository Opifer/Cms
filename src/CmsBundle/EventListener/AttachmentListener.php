<?php

namespace Opifer\CmsBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Opifer\CmsBundle\Entity\AttachmentValue;
use Opifer\FormBundle\Event\Events;
use Opifer\FormBundle\Event\FormSubmitEvent;
use Opifer\MediaBundle\Model\MediaManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AttachmentListener implements EventSubscriberInterface
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var MediaManagerInterface */
    protected $mediaManager;

    public function __construct(EntityManagerInterface $entityManager, MediaManagerInterface $mediaManager)
    {
        $this->entityManager = $entityManager;
        $this->mediaManager = $mediaManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::POST_FORM_SUBMIT => 'postFormSubmit',
        ];
    }

    /**
     * @param FormSubmitEvent $event
     */
    public function postFormSubmit(FormSubmitEvent $event)
    {
        $post = $event->getPost();
        $values = $post->getValueSet()->getValues();

        $this->entityManager->refresh($post);

        foreach ($values as $value) {
            if ($value instanceof AttachmentValue) {
                // Avoid saving the attachment when it's empty and not required
                if (null === $value->getFile() && !$value->getAttribute()->getRequired()) {
                    continue;
                }

                $media = $this->mediaManager->createMedia();
                $media->setFile($value->getFile());
                $media->setProvider('file');

                $form = $post->getForm();
                if ($uploadDir = $form->getUploadDirectory()) {
                    $media->setDirectory($uploadDir);
                }

                $value->setAttachment($media);
            }
        }

        $this->entityManager->persist($post);
        $this->entityManager->flush();
    }
}
