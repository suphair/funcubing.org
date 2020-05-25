<?php

Class Ivy{
    
    public $Centers;
    public $Corners;
    
    function __construct() {    
        $this->Centers=array(
            'U'=>'W',
            'B'=>'B',
            'F'=>'G',
            'D'=>'Y',
            'L'=>'O',
            'R'=>'R',
        );
        
        $this->Corners=array(
            'U'=>array('L'=>'W','R'=>'W'),
            'B'=>array('U'=>'B','D'=>'B'),
            'F'=>array('U'=>'G','D'=>'G'),
            'D'=>array('F'=>'Y','B'=>'Y'),
            'L'=>array('U'=>'O','D'=>'O'),
            'R'=>array('U'=>'R','D'=>'R'),
        );
    }
    
    function out(){
        echo '<pre>';
        print_r($this->Centers);
        print_r($this->Corners);
        echo '</pre>';
    }
    
    function check(){
        foreach($this->Centers as $center=>$center_color){
            foreach($this->Corners[$center] as $corner_color){ 
                if($center_color!=$corner_color){
                    return false;
                }
            }   
        }
        return true;
    }
    
    function rotate($move,$direct){
        if($move=='R'){
            if($direct){
                $corner_circles=array();
            }
        }
        
        
        
    }
    
    
    
    
    
}