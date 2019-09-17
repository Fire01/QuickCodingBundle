<?php
namespace Fire01\QuickCodingBundle\Event;

class QuickCodingEvents
{
    /**
     * Called before persist Entity.
     * Listeners have the opportunity to change that data.
     * @Event("Fire01\QuickCodingBundle\Event\BuilderEvent")
     */
    const BUILDER_FORM_BEFORE_SAVE = 'quick_coding.builder_form_before_save';
}