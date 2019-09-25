<?php
namespace Fire01\QuickCodingBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class GoogleAutocompleteType extends AbstractType
{
    
    public function buildView(FormView $view, FormInterface $form, array $options) {
        if(!isset($_ENV['GOOGLE_MAPS_KEY'])) throw new InvalidConfigurationException('Please set google maps api key "GOOGLE_MAPS_KEY" on .env  to use GoogleAutocompleteType!');
        
        $view->parent->vars['google_maps_key'] = $_ENV['GOOGLE_MAPS_KEY'];
        $view->vars['country'] = $options['country'];
        $view->vars['lat'] = $options['lat'];
        $view->vars['lng'] = $options['lng'];
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {       
        $resolver->setDefaults([
            'required' => false,
            'attr' => ['class' => 'google-autocomplete'],
            'country' => null,
            'lat' => null,
            'lng' => null
        ]);
    }
    
    public function getParent()
    {
        return TextType::class;
    }
    
}

?>  