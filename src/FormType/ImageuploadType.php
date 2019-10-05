<?php
namespace Fire01\QuickCodingBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Fire01\QuickCodingBundle\Services\ImagineWrapper;
use Symfony\Component\Form\FormEvent;

class ImageuploadType extends AbstractType
{
    private $imagineWrapper;
    
    function __construct(ImagineWrapper $imagineWrapper){
        $this->imagineWrapper = $imagineWrapper;
    }
    
    public function buildView(FormView $view, FormInterface $form, array $options) {
        
        $view->vars['path'] = $options['path'];
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {   
        $imagineWrapper = $this->imagineWrapper;
        
        $builder->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($imagineWrapper, $options){
            $image = $event->getData();
            
            if($image){
                if(is_string($image)){
                    $event->setData($image);
                }else{
                    $fileName = $event->getForm()->getData() ? $event->getForm()->getData() : null;
                    $path = $options['public_dir'] . $options['path'];
                    
                    $imagine = $imagineWrapper->upload($image, $options['filter'], $path, $fileName);
                    
                    $event->setData($imagine);
                }
            }
        });
        
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr'          => ['class' => 'input-image-upload'],
            'filter'        => null,
            'path'          => null
        ]);
    }
    
    public function getParent()
    {
        return FileuploadType::class;
    }
}

?>  