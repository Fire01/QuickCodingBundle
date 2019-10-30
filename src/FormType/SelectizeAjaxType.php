<?php
namespace Fire01\QuickCodingBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;

class SelectizeAjaxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*
        $builder->addModelTransformer(new CallbackTransformer(function ($value) {
                return $value;
            }, function ($value) use ($options) {
                dd($value);
                if($options['radixPoint'] == "."){
                    $value = str_replace($options['groupSeparator'], "", $value);
                }else{
                    $value = str_replace($options['groupSeparator'], "", $value);
                    $value = str_replace($options['radixPoint'], ".", $value);
                }
                
                return $value;
            })
        );
        */
    }
    
    public function buildView(FormView $view, FormInterface $form, array $options) {
        $view->vars['selectize'] = [
            'valueField'    => $options['valueField'],
            'labelField'    => $options['labelField'],
            'searchField'   => $options['searchField'],
            'url'           => $options['url'],
            'method'        => $options['method'],
            'dataType'      => $options['dataType'],
            'params'        => $options['params'],
        ];
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => [
                'class' => 'selectize-ajax',
            ],
            'valueField' => 'value',
            'labelField' => 'text',
            'searchField' => 'text',
            'url' => null,
            'method' => 'get',
            'dataType' => 'json',
            'params' => [],
        ]);
    }
    
    public function getParent()
    {
        return ChoiceType::class;
    }
}

?>  