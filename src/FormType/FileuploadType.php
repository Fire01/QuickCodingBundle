<?php
namespace Fire01\QuickCodingBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Mapping\EntityListeners;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\EntityListenerPass;

class FileuploadType extends AbstractType
{
    protected $uploadDirectory;
    
    function __construct($uploadDirectory){
        if(!is_array($uploadDirectory)){
            throw new InvalidArgumentException('Parameter "upload_directory" should be array!');
        }
        
        $this->uploadDirectory = $uploadDirectory;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $uploadDirectory = $builder->getOption('upload_directory');
        if (!array_key_exists($uploadDirectory, $this->uploadDirectory)){
            throw new InvalidArgumentException(sprintf('Parameter "%1$s" not found in parameter "upload_directory"! Please set "%1$s" on parameter "upload_directory"', $uploadDirectory));
        }
        
        $dir = $this->uploadDirectory[$uploadDirectory];
        $upload = $builder->getOption('upload');
        
        if($upload){
            $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($dir) {
                $file = $event->getData();
                $file->setOriginalName('sss');
                //$file->setData('test');
                if($file){
                    $filename = md5(uniqid(rand(), true));
                    
                    dump($file);
                    dump($file->getClientOriginalName());
                    dump($file->getClientMimeType());
                    dump($file->guessExtension());
                    
                }
                
                die();
            });
        }
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {       
        $resolver->setDefaults([
            'data_class' => null,
            'upload_directory' => null,
            'upload' => false
        ]);
    }
    
    public function getParent()
    {
        return FileType::class;
    }
}

?>  