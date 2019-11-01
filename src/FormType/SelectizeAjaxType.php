<?php
namespace Fire01\QuickCodingBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Validator\Constraints\Choice;

class SelectizeAjaxType extends AbstractType
{
    private $em;
    
    function __construct($em){
        $this->em = $em;    
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->resetViewTransformers();
        $em = $this->em;
        
        $builder->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options, $em){
            if($options['class']){
                if(!$event->getData()) return;
                $data = $em->getRepository($options['class'])->find($event->getData());
                if (null === $data) throw new TransformationFailedException(sprintf('Data "%s" does not exist!', $event->getData()));
                
                $event->setData($data);
            }            
        });
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
            'class' => null,
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