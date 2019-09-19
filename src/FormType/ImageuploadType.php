<?php
namespace Fire01\QuickCodingBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ImageuploadType extends AbstractType
{   
    public function configureOptions(OptionsResolver $resolver)
    {       
        $resolver->setDefaults([
            'data_class' => null,
            'attr' => ['class' => 'input-image-upload']
        ]);
    }
    
    public function getParent()
    {
        return FileType::class;
    }
}

?>  