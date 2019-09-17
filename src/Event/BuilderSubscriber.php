<?php
namespace Fire01\QuickCodingBundle\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
/********************* Event Subscriber example *********************/
class BuilderSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            QuickCodingEvents::BUILDER_FORM_BEFORE_SAVE => 'beforeSave',
        ];
    }
    
    public function beforeSave(BuilderEvent $event)
    {
        
    }
}