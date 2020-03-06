<?php 
namespace Fire01\QuickCodingBundle\Builder;

use Symfony\Component\HttpFoundation\RequestStack;

class Builder {

    private $request;

    protected $form;

    function __construct(RequestStack $request)
    {
        $this->request = $request;
    }

    function generateForm(Form $form){
        $form->create('App\Form\DocumentTypeType', null, []);
        return $form;
    }
}