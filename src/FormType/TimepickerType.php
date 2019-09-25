<?php
namespace Fire01\QuickCodingBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

class TimepickerType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {       
        $resolver->setDefaults([
            'required' => false,
            'attr' => ['class' => 'flatpickr-timepicker'],
            'widget'=>'single_text',
            'format' => 'HH:mm',
        ]);
    }
    
    public function getParent()
    {
        return TimeType::class;
    }
}

?>  