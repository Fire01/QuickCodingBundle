<?php
namespace Fire01\QuickCodingBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class DatepickerType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {       
        $resolver->setDefaults([
            'attr' => ['class' => 'flatpickr-datepicker'],
            'widget'=>'single_text'
        ]);
    }
    
    public function getParent()
    {
        return DateType::class;
    }
}

?>  