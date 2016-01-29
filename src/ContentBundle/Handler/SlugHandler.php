<?php

namespace Opifer\ContentBundle\Handler;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Gedmo\Sluggable\Handler\RelativeSlugHandler;
use Gedmo\Sluggable\Handler\SlugHandlerInterface;
use Gedmo\Sluggable\Mapping\Event\SluggableAdapter;
use Gedmo\Sluggable\SluggableListener;

/**
 * class SlugHandler
 *
 * @author denis
 */
class SlugHandler extends RelativeSlugHandler
{
    
    /**
     * {@inheritDoc}
     */
    public function onSlugCompletion(SluggableAdapter $ea, array &$config, $object, &$slug)
    {
        parent::onSlugCompletion($ea, $config, $object, $slug);
        
        $this->slug = &$slug;
        $this->usedOptions = $config['handlers'][get_called_class()];
        
        if(isset($this->usedOptions[__FUNCTION__])) {
            foreach($this->usedOptions[__FUNCTION__] as $method) {
                if(method_exists($this, $method)) {
                    $this->$method();
                }
            }
        }
    }
    
    /**
     * slug trim
     */
    private function rightTrim()
    {
        $this->slug = rtrim($this->slug, $this->usedOptions['separator']);
    }
    
    /**
     * 
     */
    private function appendIndex()
    {
        if(substr($this->slug, -1) == '/') {
            $this->slug = $this->slug . 'index';
        }
    }
}
