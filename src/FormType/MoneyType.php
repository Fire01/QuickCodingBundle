<?php
namespace Fire01\QuickCodingBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;

class MoneyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CallbackTransformer(function ($value) {
                return $value;
            }, function ($value) use ($options) {
                if($options['radixPoint'] == "."){
                    $value = str_replace($options['groupSeparator'], "", $value);
                }else{
                    $value = str_replace($options['groupSeparator'], "", $value);
                    $value = str_replace($options['radixPoint'], ".", $value);
                }
                
                return $value;
            })
        );
    }
    
    public function buildView(FormView $view, FormInterface $form, array $options) {
        $view->vars['inputmask']['alias']           = 'numeric';
        $view->vars['inputmask']['groupSeparator']  = $options['groupSeparator'];
        $view->vars['inputmask']['radixPoint']      = $options['radixPoint'];
        $view->vars['inputmask']['autoGroup']       = $options['autoGroup'];
        $view->vars['inputmask']['digits']          = $options['digits'];
        $view->vars['inputmask']['digitsOptional']  = $options['digitsOptional'];
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'alias' => 'numeric',
            'groupSeparator' => ',',
            'radixPoint' => '.',
            'autoGroup' => true,
            'digits' => 2,
            'digitsOptional' => true,
        ]);
    }
    
    public function getParent()
    {
        return TextType::class;
    }
}

?>  