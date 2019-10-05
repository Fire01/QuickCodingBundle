<?php 
namespace Fire01\QuickCodingBundle\Services;

use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Liip\ImagineBundle\Model\Binary;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;

class ImagineWrapper {
    
    private $filterManager;
    
    function __construct(FilterManager $filterManager){
        $this->filterManager = $filterManager;
    }
    
    function upload($image, $filter, $path, $fileName=null)
    {
        
        $fileType = explode("/", $image->getMimeType())[0];
        if($fileType != "image"){ throw new InvalidTypeException("Invalid image type! Please select image type jpg or png.");}
        
        if($fileName){
            $fname = pathinfo($fileName)['filename'] . "." . $image->guessExtension();
            if(file_exists($path . '/' . $fileName)) unlink($path . '/' . $fileName);
        }else{
            $fname = md5(microtime(true)) . md5(uniqid(rand(), true)) . "." . $image->guessExtension();
        }
        
        $binary = new Binary(file_get_contents($image), $image->getMimeType(), $image->guessExtension());
        
        $content = $this->filterManager->applyFilter($binary, $filter)->getContent();
        
        $f = fopen($path . '/' . $fname, 'w');
        fwrite($f, $content);
        fclose($f);         
        
        return $fname;
    }
}