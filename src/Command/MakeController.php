<?php

namespace Fire01\QuickCodingBundle\Command;

use Doctrine\Common\Annotations\Annotation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Doctrine\ORM\EntityManagerInterface;

final class MakeController extends AbstractMaker
{
    private $entityHelper;
    
    public function __construct(DoctrineHelper $entityHelper)
    {
        $this->entityHelper = $entityHelper;
    }
    
    public static function getCommandName(): string
    {
        return 'qc:controller';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConf)
    {
        $command
            ->setDescription('Creates a new controller class')
            ->addArgument('controller-class', InputArgument::REQUIRED, sprintf('Choose a name for your controller class'))
            ->addArgument('entity-class', InputArgument::REQUIRED, 'The name of Entity or fully qualified model class name that will be bound to controller')
            ->addArgument('form-class', InputArgument::REQUIRED, 'The name of FormType or fully qualified model class name that will be bound to controller')
            ->setHelp(file_get_contents(__DIR__.'/../Resources/help/MakeController.txt'))
        ;
        
        $inputConf->setArgumentAsNonInteractive('entity-class');
        $inputConf->setArgumentAsNonInteractive('form-class');
    }
    
    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {   
        if (null === $input->getArgument('entity-class')) {
            $argument = $command->getDefinition()->getArgument('entity-class');
            
            $entities = $this->entityHelper->getEntitiesForAutocomplete();
            
            $question = new Question($argument->getDescription());
            $question->setValidator(function ($answer) use ($entities) {return Validator::existsOrNull($answer, $entities); });
            $question->setAutocompleterValues($entities);
            $question->setMaxAttempts(3);
            $input->setArgument('entity-class', $io->askQuestion($question));
        }
        
        if (null === $input->getArgument('form-class')) {
            $argument2 = $command->getDefinition()->getArgument('form-class');
            
            $question2 = new Question($argument2->getDescription());
            $question2->setValidator(function ($answer){
                $answer = class_exists('App\Form\\' . $answer) ? 'App\Form\\' . $answer : $answer;
                if (!class_exists($answer)) {throw new InvalidArgumentException(sprintf('Could not load type "%s": class does not exist.', $answer));}
                if (!is_subclass_of($answer, 'Symfony\Component\Form\FormTypeInterface')) {throw new InvalidArgumentException(sprintf('Could not load type "%s": class does not implement "Symfony\Component\Form\FormTypeInterface".', $answer));}
                
                return $answer;
            });
            
            $question2->setMaxAttempts(3);
            $input->setArgument('form-class', $io->askQuestion($question2));
        }
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $controllerClassNameDetails = $generator->createClassNameDetails($input->getArgument('controller-class'), 'Controller\\', 'Controller');
        
        $entityClass = $input->getArgument('entity-class');
        $boundClassDetails = null;
        if (null !== $entityClass) {
            $boundClassDetails = $generator->createClassNameDetails($entityClass, 'Entity\\');
        }
        
        $fieldNames = $this->entityHelper->getMetadata($boundClassDetails->getFullName())->getFieldNames();
        $columns = [];
        
        for($i=1;$i<3;$i++){
            if(isset($fieldNames[$i])) $columns[] = ['title' => $fieldNames[$i], 'name' => $fieldNames[$i]];
        }
        
        $formClass = $input->getArgument('form-class');
        $generator->generateController($controllerClassNameDetails->getFullName(), __DIR__ . '/../Resources/skeleton/controller/Controller.tpl.php',
            [
                'route_path'    => Str::asRoutePath($controllerClassNameDetails->getRelativeNameWithoutSuffix()),
                'route_name'    => Str::asRouteName($controllerClassNameDetails->getRelativeNameWithoutSuffix()),
                'title'         => $this->readable($input->getArgument('entity-class')),
                'entity_name'   => $boundClassDetails->getFullName(),
                'form_name'     => $formClass,
                'columns'       => $columns
            ]
        );
        
        $generator->writeChanges();

        $this->writeSuccessMessage($io);
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $dependencies->addClassDependency(
            Annotation::class,
            'doctrine/annotations'
        );
    }

    private function isTwigInstalled()
    {
        return class_exists(TwigBundle::class);
    }
    
    private function readable($string){
        return trim(ucfirst(preg_replace(['/(?<=[^A-Z])([A-Z])/', '/(?<=[^0-9])([0-9])/'], ' $0', $string)));
    }
}
