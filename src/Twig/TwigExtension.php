<?php
namespace Fire01\QuickCodingBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigExtension extends AbstractExtension
{
    
    private $widgets = [];
    
    public function getFilters()
    {
        return [
            new TwigFilter('all_widget_type', [$this, 'getAllWidgetType']),
            new TwigFilter('serialize_data_selectize', [$this, 'getSerializeDataSelectize']),
        ];
    }
    
    public function getAllWidgetType($form)
    {
        $this->findWidget($form);
        return $this->widgets;
    }
    
    public function getSerializeDataSelectize($form)
    {
        $options = $form->vars['selectize'];
        $params = $options['params'];
        $newParams = [];
        foreach($params as $key => $param){
            $value = $param;
            if(strpos($param, 'val:') !== false){
                $value = 'val:' . $form->parent->children[str_replace("val:", "", $param)]->vars['id'];
            }
            
            $newParams[$key] = $value; 
        }
        
        $options['params'] = $newParams;
        
        return json_encode($options);
    }
    
    
    function findWidget($item){
        if(isset($item->children) && $item->children){
            foreach ($item->children as $child){
                $blockPrefixes = $child->vars['block_prefixes'];
                $this->widgets[] = $blockPrefixes[count($blockPrefixes) - 2];
                
                if(count($child->children)) $this->findWidget($child);
            }
        }
    }
}