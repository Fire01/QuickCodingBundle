<?php
namespace Fire01\QuickCodingBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->resetViewTransformers();
        
        $builder->addModelTransformer(new CallbackTransformer(function ($value) {
            return json_decode($value);
        }, function ($value) {
            return json_encode($value);
        }));
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {     
        $resolver->setDefaults([
            'multiple' => true,
            'required' => false,
            'attr' => ['class' => 'list-type-component']
        ]);
    }
    
    public function getParent()
    {
        return ChoiceType::class;
    }
    
}

?>  