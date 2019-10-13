<?php 
namespace Fire01\QuickCodingBundle\Services;

class Helpers {
    
    public static function dateFormater(\DateTimeInterface $obj){
        if($obj && $obj->format('Y-m-d H:i:s') != '-0001-11-30 00:00:00'){
            $obj->add(date_interval_create_from_date_string('-7 hours'));
            return ['date' => $obj->format('Y-m-d') . 'T' . $obj->format('H:i:s') . 'Z'];
        }
        
        return ['date' => null];
    }
    
    public static function dateToString(\DateTimeInterface $obj){
        if($obj && $obj->format('Y-m-d H:i:s') != '-0001-11-30 00:00:00'){
            $obj->add(date_interval_create_from_date_string('-7 hours'));
            return $obj->format('Y-m-d') . 'T' . $obj->format('H:i:s') . 'Z';
        }
        
        return null;
    }
}