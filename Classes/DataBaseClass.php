<?php

class DataBaseClass{
    protected static $_instance; 
    protected static $connection; 
    protected static $query; 
    protected static $tables; 
    protected static $select; 
    protected static $join; 
    protected static $where;
    protected static $order;
    protected static $from;
    protected static $limit;
    protected static $count;
    protected static $queries;
    protected static $currentTable;

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
    
    public static function setConection($connection){
        self::$connection=$connection;   
        self::$count=0;
        self::$queries=array();
    }
    
    public static function getConection(){
        return self::$connection;   
    }
    
    public static function AddTable($name,$alias,$fields){
       self::$tables[$name]['fields']=$fields;
       self::$tables[$name]['alias']=$alias;
       $select="";
       foreach($fields as $field){
          $select.=" ".$alias.".`".$field."` ".$name."_".$field.", "; 
       }
       
       self::$tables[$name]['select']=$select;
       self::$tables[$name]['from']=" from `".$name."` ".$alias;
       self::$tables[$name]['order']=" order by ".$alias.".".$fields[0];
    }
    
    public static function SetOrder($name,$order){
        self::$tables[$name]['order']=" order by ".self::$tables[$name]['alias'].".".$order;
    }
    
    public static function SetJoin($table_field,$table_ID){
        self::$tables[$table_field]['join'][$table_ID]=" join $table_ID ". self::$tables[$table_ID]['alias'].' on '.
                self::$tables[$table_ID]['alias'].".ID=".self::$tables[$table_field]['alias']."."."$table_ID";
        self::$tables[$table_ID]['join'][$table_field]=" join $table_field ". self::$tables[$table_field]['alias'].' on '.
                self::$tables[$table_ID]['alias'].".ID=".self::$tables[$table_field]['alias']."."."$table_ID";
    }
    
    public static function Join_current($table_2){
        self::Join(self::$currentTable,$table_2);
    }
    
    public static function Join($table_1,$table_2){
        self::$join.=' '.self::$tables[$table_1]['join'][$table_2];
        self::$select.=' '.self::$tables[$table_2]['select'];
        self::$currentTable=$table_2;
        
    }
    
    public static function Order_current($order){
        self::Order(self::$currentTable,$order);
    }
    
    public static function OrderClear($table,$order){
        self::$order="";
        self::Order($table,$order);
    }
    
    public static function OrderSpecial($order){
        self::$order=" order by ".$order;
    }
    
    public static function Order($table,$order){
        if(self::$order){
            self::$order.=",";
        }else{
            self::$order=" order by ";
        }
        self::$order.=' '.self::$tables[$table]['alias'].".".$order;
    }
     
    public static function Where_current($where){
        self::Where(self::$currentTable,$where);
    }
    
    public static function Where($name,$where=""){
        
        if(self::$where){
            self::$where.=" and ";
        }else{
            self::$where=" where ";
        }
        if(isset(self::$tables[$name]['alias'])){
            self::$where.=' '.self::$tables[$name]['alias'].".".$where;
        }else{
            self::$where.=' '.$name;
        }
    }
    
    public static function WhereCustom($where=""){
        if(self::$where){
            self::$where.=" and ".$where;
        }else{
            self::$where=" where ".$where;
        }  
    }
    
    public static function WhereOR($name,$where){
        self::$where.=' OR '.self::$tables[$name]['alias'].".".$where;
    }
    
    public static function Select($select){
       foreach(self::$tables as $name=>$table){
           $select=str_replace($name.".",$table['alias'].".",$select);
       } 
       self::$select= "$select ,";
       self::$order="";
    }
    
    public static function SelectPre($select){
        self::$select="$select ,";
    }
    
    public static function SelectAdd($name,$field){
        self::$select.=self::$tables[$name]['alias'].".".$field." ".$name."_".$field.", ";
    }
    
    public static function Limit($limit){
       self::$limit= " limit $limit ";
    }
    
    
    public static function SelectTableRows($name,$where=""){
        self::FromTable($name,$where);
        return self::QueryGenerate();
    }
    
    public static function SelectTableRow($name, $where){
        self::FromTable($name,$where);
        return self::QueryGenerate(false);
    }
    
    public static function FromTable($name, $where=''){
        self::$select=self::$tables[$name]['select']; 
        self::$join=''; 
        self::$limit='';
        self::$currentTable=$name;
        if($where){
            self::$where=" where ".self::$tables[$name]['alias'].".".$where;
        }else{
            self::$where='';     
        }
        self::$order=self::$tables[$name]['order'];
        self::$from=self::$tables[$name]['from'];
    }
    
    public static function QueryGenerate($rows=true,$out=false,$log=false){
        $sql="select ".self::$select." 1 ".self::$from.self::$join.self::$where.self::$order.self::$limit;
        self::Query($sql,$out,$log);
        if($rows){
            return self::getRows();
        }else{
            return self::getRow();
        }
    }
    
    
    public static function GetTables(){
       echo '<pre>';
       print_r(self::$tables);
       echo '</pre>';
    }
    
    public static function Query($sql,$out=false,$log=false){
        self::$count++;
        self::$queries[]=$sql;
        if (!self::$query=mysqli_query(self::$connection, $sql)) {
            
            $time = date("Y-m-d H:i:s");
            $handle = fopen("SQLError.txt", "a");
            fwrite($handle, "\r\n$time\r\n$sql\r\n".mysqli_error(self::$connection));
            fwrite($handle,print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS),true));
            fclose($handle);
            
            if(strpos($_SERVER['PHP_SELF'],'/'.GetIni('LOCAL','PageBase').'/')!==false){
                echo "<p>[SQLError] $sql :".mysqli_error(self::$connection)."<br>".print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS),true)."</p>";
            }else{
                echo '"';
                echo "<h1><font color='red'>Unexpected error. We'll fix it soon.</font></h1>$time";
            }
            
            exit();
            
        }
        if($out){
            echo "<p>$sql</p>";
        }
        
        if($log){
            $time = date("Y-m-d H:i:s");
            $handle = fopen("SQLlog.txt", "a");
            fwrite($handle, "\r\n$time\r\n$sql\r\n");
            fclose($handle);
        }
    }
    
    public static function getRow(){
        return self::$query->fetch_assoc();
    }
    
    public static function getRows(){
        
        $row=array();
        for($i=0;$i<DataBaseClass::rowsCount();$i++){
            $row[$i]=DataBaseClass::getRow();
        }
        return $row;
    }
    
    public static function rowsCount(){
        return self::$query->num_rows;
    }
    
    public static function getID(){
        return self::$connection->insert_id;
    } 
    
    Public static function Escape($str){
        return trim(mysqli_escape_string(self::$connection,$str));
    }
    
    public static function getCount(){
        return self::$count;
    } 
    
    public static function echoQueries(){
        echo "<pre>";
        print_r(self::$queries);
        echo "</pre>";
    } 
    
    public static function echoQueriesCount(){
        return sizeof(self::$queries);
    } 
    
}


class DataBaseClassWCA{
    protected static $_instance; 
    protected static $connection; 
    protected static $query; 


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
    
    public static function setConection($connection){
        self::$connection=$connection;   
    
    }
    
    public static function Query($sql,$out=false){
        if (!self::$query=mysqli_query(self::$connection, $sql)) {
            
            $time = date("Y-m-d H:i:s");
            echo "<h1>WCA: <font color='red'>Unexpected error. We'll fix it soon.</font></h1>$time";
            
            $handle = fopen("SQLError.txt", "a");
            fwrite($handle, "\r\n$time\r\n$sql\r\n".mysqli_error(self::$connection));
            fclose($handle);  
        }
        if($out){
            echo $sql;
        }
    }
    
    public static function getRow(){
        return self::$query->fetch_assoc();
    }
    
    public static function getRows(){
        
        $row=array();
        for($i=0;$i<self::$query->num_rows;$i++){
            $row[$i]=DataBaseClassWCA::getRow();
        }
        return $row;
    }
}

