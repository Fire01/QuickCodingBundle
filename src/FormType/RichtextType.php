<?php
namespace Fire01\QuickCodingBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RichtextType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {       
        $resolver->setDefaults([
            'required' => false,
            'attr' => ['class' => 'tinymce-texteditor'],
        ]);
    }
    
    public function getParent()
    {
        return TextareaType::class;
    }
}

?>  