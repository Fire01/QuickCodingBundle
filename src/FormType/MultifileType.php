<?php
namespace Fire01\QuickCodingBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class MultifileType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options) {
        $view->vars['max_size'] = $options['maxSize'];
        $view->vars['file_type'] = $options['fileType'];
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'multiple' => true,
            'fileType' => null,
            'mapped' => false,
            'attr' => ['allow' => null],
            'maxSize' => 0
        ]);
    }
    
    public function getParent()
    {
        return FileuploadType::class;
    }
}

?>  