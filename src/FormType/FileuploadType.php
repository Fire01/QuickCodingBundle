<?php
namespace Fire01\QuickCodingBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class FileuploadType extends AbstractType
{
    private $public_dir;
    
    function __construct($public_dir){
        $this->public_dir = $public_dir;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
            'public_dir'    => $this->public_dir,
            'data_class'    => null,
        ]);
    }
    
    public function getParent()
    {
        return FileType::class;
    }
}

?>  