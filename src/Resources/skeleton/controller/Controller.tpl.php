<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use Symfony\Bundle\FrameworkBundle\Controller\<?= $parent_class_name; ?>;
use Symfony\Component\Routing\Annotation\Route;
use Fire01\QuickCodingBundle\Services\PageGenerator;

class <?= $class_name; ?> extends <?= $parent_class_name; ?><?= "\n" ?>
{

	function getConfiguration(){
        return [
            'title'	=> '<?= ucfirst($route_name) ?>',
            'entity'=> '<?= $entity_name ?>',
            'form'	=> '<?= $form_name ?>',
            'view'	=> [
            	<?php foreach($columns as $column){ ?>
    ['title' => '<?= $column['title'] ?>', 'name' => '<?= $column['name'] ?>'],
    <?php } ?>
]
        ];
    }
    
    /**
     * @Route("/qc<?= $route_path ?>/{action}/{id}", requirements={"id"="\d+"}, name="quick_coding_<?= $route_name ?>", methods="GET|POST")
     */
    public function qc_generator(PageGenerator $pageGenerator, $action=null, $id=null)
    {
        return $pageGenerator->generate($this->getConfiguration());
    }
    
}
