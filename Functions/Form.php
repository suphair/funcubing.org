<?php
function FormCheckFields($required_fields){
    $error="";
    foreach($required_fields as $field){
        if(!isset($_POST[$field]) or DataBaseClass::Escape(trim($_POST[$field]))==""){
            $error.="$field не заполнено;<br>";   
            $_SESSION["Form".FormClass::getFormName().$field."Error"]="Поле не заполнено";
        }
    } 
    return $error=="";
}    

function SetFormError($field,$error){
    $_SESSION["Form".FormClass::getFormName().$field."Error"]=$error;
}

class FormClass {
    protected static $_instance; 
    protected static $formName; 

    private function __construct() {        
    }

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;   
        }
 
        return self::$_instance;
    }
  
    private function __clone() {
    }

    private function __wakeup() {
    }   
    
    public static function setFormName($formName){
        self::$formName=$formName;   
    }
    
    public static function getFormName(){
        return self::$formName;   
    }
}

function CheckPostIsset(){
   foreach(func_get_args() as $arg){
        if(!isset($_POST[$arg])){
            SetMessage('not set POST '.$arg);
            HeaderExit();
        }
   }  
}

function CheckPostIsNumeric(){
   foreach(func_get_args() as $arg){
        if(!is_numeric($_POST[$arg])){
            SetMessage('not numeric POST '.$arg);
            HeaderExit();
        }
   }  
}

function CheckPostNotEmpty(){
    foreach(func_get_args() as $arg){
        if($_POST[$arg]==""){
            SetMessage('empty POST '.$arg);
            HeaderExit();
        }
   }       
}

Function ClearPost(){
      foreach(func_get_args() as $arg){
        $_POST[$arg]=DataBaseClass::Escape(($_POST[$arg]));
   }    
}

function CheckGetIsset(){
   foreach(func_get_args() as $arg){
        if(!isset($_GET[$arg])){
            SetMessage('not set GET '.$arg);
            HeaderExit();
        }
   }  
}

function CheckGetIsnumeric(){
   foreach(func_get_args() as $arg){
        if(!is_numeric($_GET[$arg])){
            SetMessage('not numeric GET '.$arg);
            HeaderExit();
        }
   }  
}




?>