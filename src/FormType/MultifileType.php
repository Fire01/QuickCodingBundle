<?php
namespace Fire01\QuickCodingBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MultifileType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'multiple' => true,
            'mapped' => false,
            'attr' => ['allow' => null],
        ]);
    }
    
    public function getParent()
    {
        return FileuploadType::class;
    }
}

?>  