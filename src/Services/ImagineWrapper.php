<?php 
namespace Fire01\QuickCodingBundle\Services;

use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Liip\ImagineBundle\Model\Binary;

class ImagineWrapper {
    
    private $filterManager;
    
    function __construct(FilterManager $filterManager){
        $this->filterManager = $filterManager;
    }
    
    function upload($image, $filter, $path, $fileName=null) : void
    {
        $fileName = $fileName ? $fileName : md5(microtime(true)) . md5(uniqid(rand(), true)) . "." . $image->guessExtension();
        $binary = new Binary(file_get_contents($image), $image->getMimeType(), $image->guessExtension());
        
        $content = $this->filterManager->applyFilter($binary, $filter)->getContent();
        
        $f = fopen($path . '/' . $fileName, 'w');
        fwrite($f, $content);
        fclose($f);         
    }
}