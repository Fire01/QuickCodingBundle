<?php 
namespace Fire01\QuickCodingBundle\Builder;

use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Form {

    private $formInterface;
    protected $twig;

    protected $form;
    public $actionBars;
    public $title;
    protected $editable=true;

    function __construct(FormFactoryInterface $formInterface, Environment $twig)
    {
        $this->formInterface = $formInterface;
        $this->twig = $twig;
    }

    function setEditable(bool $editable){
        $this->editable = $editable;
    }

    function getForm(){
        return $this->form;
    }

    function render(){
        return $this->twig->render('@quick_coding.view/component/form.html.twig', ["config" => ['title' => '', 'actionbar' => []], "form" => $this->form->createView()]);
    }

    function response(){
        return new Response($this->render());
    }

    function create($form, $data=null, $options=[])
    {
        // Validator::form($this->config);
        
        // $request = $this->get('request_stack')->getCurrentRequest();
        // $repository = $this->getDoctrine()->getRepository($this->config->getEntity());
        // $entity = $this->config->getEntity();
        
        // $item = $id ? $repository->find($id) : new $entity();
        // $options = array_merge($this->config->getFormOptions(), ['disabled' => !$edit]);
        $this->form = $this->formInterface->create($form, $data, $options);
        
        return $this;
        // $this->config->addActionbarFormClose();
        // if($edit){
        //     $this->config->addActionbarFormSave();
        // }else{
        //     if($this->config->getACL()->canUpdate($this->getUser()->getRoles())){
        //         $this->config->addActionbar(new Action([
        //             'type' => 'link',
        //             'text' => 'Edit',
        //             'icon' => 'pencil',
        //             'path' => $this->config->getPathForm(),
        //             'params' => array_merge($this->config->getParams(), ['action' => 'update', 'id' => $item->getId()]),
        //             'target' => 'route'
        //         ]));
        //     }
        // }
        
        // $event = new BuilderFormEvent($item, $form);
        
        // if ($this->eventDispatcher) {
        //     $this->eventDispatcher->dispatch($event, 'quick_coding.builder_form_before_submit');
        // }
        
        // $form->handleRequest($request);
        
        // if($form->isSubmitted() && $form->isValid()) {

        //     $event = new BuilderFormEvent($item, $form);
            
        //     if ($this->eventDispatcher) {
        //         $this->eventDispatcher->dispatch($event, 'quick_coding.builder_form_before_save');
        //     }

        //     $em = $this->getDoctrine()->getManager();
        //     $em->persist($item);
        //     $em->flush();
            
        //     return $this->redirectToRoute($this->config->getPathView(), $this->getConfig()->getParams());
        // }
        
        // return $this->render($this->config->getTemplateForm(), ["config" => $this->config, "form" => $form->createView()]);
    }

    public function getActionBars()
    {
        return $this->actionBars;
    }

    public function setActionBars($actionBars)
    {
        $this->actionBars = $actionBars;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
}