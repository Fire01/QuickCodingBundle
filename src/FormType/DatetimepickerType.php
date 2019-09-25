<?php
namespace Fire01\QuickCodingBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class DatetimepickerType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {       
        $resolver->setDefaults([
            'required' => false,
            'attr' => ['class' => 'flatpickr-datetimepicker'],
            'widget'=>'single_text',
            'format' => 'yyyy-MM-dd HH:mm',
        ]);
    }
    
    public function getParent()
    {
        return DateTimeType::class;
    }
}

?>  