<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\<?= $parent_class_name; ?>;
use Fire01\QuickCodingBundle\Builder;

class <?= $class_name; ?> extends <?= $parent_class_name; ?><?= "\n" ?>
{
    /**
     * @Route("/qc<?= $route_path ?>/{action}/{id}", requirements={"id"="\d+"}, name="quick_coding_<?= $route_name ?>", methods="GET|POST")
     */
    public function qc_generator(Builder $builder, $action=null, $id=null){
        return $builder->setConfig([
            'title'	=> '<?= $title ?>',
            'entity'=> '<?= $entity_name ?>',
            'form'	=> '<?= $form_name ?>',
            'view'=> [
                'select' => [<?php foreach($columns as $column){ ?>	'<?= $column['title'] ?>' => '<?= $column['name'] ?>',	<?php } ?>],
            ],
            'action' => $action
        ])->build();
    }
    
}