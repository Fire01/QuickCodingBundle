<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\<?= $parent_class_name; ?>;
use Fire01\QuickCodingBundle\Builder;

class <?= $class_name; ?> extends <?= $parent_class_name; ?><?= "\n" ?>
{

	private $config = [
        'title'	=> '<?= ucfirst($route_name) ?>',
        'entity'=> '<?= $entity_name ?>',
        'form'	=> '<?= $form_name ?>',
        'column'=> [
<?php foreach($columns as $column){ ?>
			['title' => '<?= $column['title'] ?>', 'name' => '<?= $column['name'] ?>'],
<?php } ?>
        ]
    ];
    
    /**
     * @Route("/qc<?= $route_path ?>/{action}/{id}", requirements={"id"="\d+"}, name="quick_coding_<?= $route_name ?>", methods="GET|POST")
     */
    public function qc_generator(Builder $builder, $action=null, $id=null){
        $builder->setConfig(array_merge($this->config, ['action' => $action]));
        return $builder->build();
    }
    
}
