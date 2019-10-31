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
use Symfony\Component\Form\CallbackTransformer;

class SelectizeAjaxType extends AbstractType
{
    private $em;
    
    function __construct($em){
        $this->em = $em;    
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$builder->resetViewTransformers();
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event){
            
            $data = $event->getData();
            
            
            /*
            $maker_id = $data['maker'];
            $model= $form->get('model');
            $options = $model->getConfig()->getOptions();

            if (!empty($maker_id)) {
                unset($options['attr']['disabled']);
                $options['choices'] = $this->extractor->getModelsFor($maker_id);

                $form->remove('model');
                $form->add('model', CarModelsSelectType::class, $options );
            }
        
             */
            if(!empty($data)){
                $form = $event->getForm();
                $name = $form->getName();
                $parent = $form->getParent();
                $options = $form->getConfig()->getOptions();
                $parent->remove($name);
                $options['choices'] = [$data => $data];
                $parent->add($name, self::class, $options)->setData($data);
                dump($parent->get($name)->getData());
            }
        });
        
        $em = $this->em;
        $builder->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) use ($options, $em){
            $form = $event->getForm();
            dd("OKE");
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