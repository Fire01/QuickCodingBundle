<?php
namespace Fire01\QuickCodingBundle\Event;

class QuickCodingEvents
{
    /**
     * Called before persist Entity.
     * @Event("Fire01\QuickCodingBundle\Event\BuilderViewEvent")
     */
    const BUILDER_VIEW_WHERE = 'quick_coding.builder_view_where';

    /**
     * Called before persist Entity.
     * @Event("Fire01\QuickCodingBundle\Event\BuilderFormEvent")
     */
    const BUILDER_FORM_BEFORE_SAVE = 'quick_coding.builder_form_before_save';
    
    /**
     * Called before handle request.
     * @Event("Fire01\QuickCodingBundle\Event\BuilderFormEvent")
     */
    const BUILDER_FORM_BEFORE_SUBMIT = 'quick_coding.builder_form_before_submit';
    
    /**
     * Called after remove entity.
     * @Event("Fire01\QuickCodingBundle\Event\BuilderRemoveEvent")
     */
    const BUILDER_REMOVE_AFTER = 'quick_coding.builder_remove_after';
}