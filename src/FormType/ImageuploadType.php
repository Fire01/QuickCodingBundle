<?php
namespace Fire01\QuickCodingBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class ImageuploadType extends AbstractType
{
    private $public_dir;
    
    function __construct($public_dir){
        $this->public_dir = $public_dir;
    }
    
    public function buildView(FormView $view, FormInterface $form, array $options) {
        
        $view->vars['path'] = $options['path'];
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
            'attr'          => ['class' => 'input-image-upload'],
            'public_dir'    => $this->public_dir,
            'data_class'    => null,
            'filter'        => null,
            'path'          => null
        ]);
    }
    
    public function getParent()
    {
        return FileType::class;
    }
}

?>  