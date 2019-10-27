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
        ];
    }
    
    public function getAllWidgetType($form)
    {
        $this->findWidget($form);
        return $this->widgets;
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