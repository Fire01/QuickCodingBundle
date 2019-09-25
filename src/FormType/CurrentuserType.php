<?php
namespace Fire01\QuickCodingBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CurrentuserType extends AbstractType
{
    
    private $security;
    
    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CallbackTransformer(function () {
                return $this->security->getUser()->getUsername();
            }, function () {
                return $this->security->getUser();
            })
        );
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {     
        $resolver->setDefaults([
            'required' => false,
            'attr' => ['class' => 'uk-form-blank', 'readonly' => true]
        ]);
    }
    
    public function getParent()
    {
        return TextType::class;
    }
    
}

?>  