<?php

namespace Opifer\ContentBundle\Handler;

use Gedmo\Sluggable\Handler\RelativeSlugHandler as BaseRelativeSlugHandler;
use Gedmo\Sluggable\Mapping\Event\SluggableAdapter;

class RelativeSlugHandler extends BaseRelativeSlugHandler
{
    /**
     * Used options
     *
     * @var array
     */
    private $usedOptions;

    /**
     * {@inheritdoc}
     */
    public function onChangeDecision(SluggableAdapter $ea, array &$config, $object, &$slug, &$needToChangeSlug)
    {
        if ($object->getSlug()) {
            // Since we set `updateable` to true, we need to force the existing slug,
            // otherwise it will try to re-generate the slug from the content title
            $parts = explode('/', $object->getSlug());
            $slug = array_pop($parts);
        }

        parent::onChangeDecision($ea, $config, $object, $slug, $needToChangeSlug);

        $isInsert = $this->om->getUnitOfWork()->isScheduledForInsert($object);
        $this->usedOptions = $config['handlers'][get_called_class()];
        if (!$isInsert && !$needToChangeSlug) {
            // If the parent check did not set `needToChangeSlug` to true, we check the relationfield's relationField recursively
            // to see if any of the parent's changed.
            $getter = 'get'.ucfirst($this->usedOptions['relationField']);
            if ($this->hasChangedParent($ea, $object, $getter)) {
                $needToChangeSlug = true;
            }
        }
    }

    /**
     * Check if the given object has a changed parent recursively
     *
     * @param SluggableAdapter $ea
     * @param object           $object
     * @param string           $getter
     *
     * @return bool
     */
    private function hasChangedParent(SluggableAdapter $ea, $object, $getter)
    {
        $relation = $object->$getter();

        if (!$relation) {
            return false;
        }

        $changeSet = $ea->getObjectChangeSet($this->om->getUnitOfWork(), $relation);
        if (isset($changeSet[$this->usedOptions['relationField']])) {
            return true;
        }

        return $this->hasChangedParent($ea, $relation, $getter);
    }
}
