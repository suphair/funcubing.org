<?php

class Input{
    public $name;
    public $type;
    public $placeholder;
    public $out;
    public $value;
    
    public function __construct(){
        $this->name = "";
        $this->type = "";
        $this->out = "";
        $this->placeholder = "";
        $this->value = "";
        
    }
    
    public function out(){
        $input="<input type=".$this->type." value='".($this->value?$this->value:TakeSession("Form"."Registration".$this->name))."' name='".$this->name."' placeholder='".$this->placeholder."' >";
        $input.="<error>".TakeSession("Form".FormClass::getFormName().$this->name."Error")."<error>";
        $str= str_replace("@input", $input, $this->out);
        return $str;
    }
    
    public function setName($name){
       $this->name = $name;
    }
    
    public function setType($type){
       $this->type = $type;
    }
    
    public function setOut($out){
       $this->out = $out;
    }
    
    public function setValue($value){
       $this->value = $value;
    }
    
    public function setPlaceholder($placeholder){
       $this->placeholder = $placeholder;
    }
    
    
    static function _out($name,$type,$out,$placeholder=""){
        $input=new Input();
        $input->setName($name);
        $input->setType($type);
        $input->setOut($out);
        $input->setPlaceholder($placeholder);
        return $input->out();
    }
    
    
    static function REQUEST_URI(){
        $input=new Input();
        $input->setName("REQUEST_URI");
        $input->setType("hidden");
        $input->setOut("@input");
        $input->setValue($_SERVER['REQUEST_URI']);
        return $input->out();
        
    }
 
    static function Submit($value){
        $input = new Input();
        $input->setType("submit");
        $input->setOut("<p>@input</p>");
        $input->setValue($value);
        return $input->out();
    }
}



