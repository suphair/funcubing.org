<?php

class RequestClass {
    protected static $_instance; 
    protected static $title;
    protected static $page;
    protected static $error;
    protected static $param1;
    protected static $param2;
    protected static $param3;
    protected static $param4;

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
    
    public static function setRequest(){
        
        self::$title="FunCubing Projects";
        self::$page="index";
        self::$param1=0;
        self::$param2=0;
        self::$param3=0;
        self::$param4=0;
        self::$error[401]="";
        self::$error[404]="";
        
        $request=getRequest();

        if(!isset($request[0])){
            self::$title="FunCubing Projects"; 
            return;
        }
        
        /*
        if(isset($_GET['Events'])){
            self::$title.=' &#9642; Events';
        }
        
        if(isset($_GET['Judges'])){
            self::$title.=' &#9642; Judges';
        }
        
        if(isset($_GET['Records'])){
            self::$title.=' &#9642; Records';
        }
        
        if(isset($_GET['Competitors'])){
            self::$title.=' &#9642; Competitors';
        }
        
        if(isset($_GET['Regulations'])){
            self::$title.=' &#9642; Regulations';
        }
         */
        
        if(isset($_GET['CompetitionGoals'])){
            self::$title='Competition Goals';
        }
        if(isset($_GET['Meetings'])){
            self::$title='Unofficial Competitions';
        }
        if(isset($_GET['MosaicBuilding']) or $request[0]=='MosaicBuilding'){
            self::$title='Mosaic Building';
        }
        if(isset($_GET['Achievements'])  or $request[0]=='Achievements'){
            self::$title='Speedcuber\'s Achievements';
        }
        if(isset($_GET['FriendsCompetitions'])  or $request[0]=='FriendsCompetitions'){
            self::$title="Friends' Competitions";
        }
        if(isset($_GET['WCA_API'])  or $request[0]=='WCA_API'){
            self::$title="WCA API";
        }
        
        $type=$request[0];
        

        if(substr($type,0,1)!='?'){
            if(in_array($type,array(
                'Discipline','Competition','Competitor','Competitors','Delegate','Admin','RequestCandidate','Alternative')))
            {
                    self::set404("Unofficial Events moved to <a href='https://SpeedcubingExtraEvents.org'>SpeedcubingExtraEvents.org</org> ");
                return;
            }    
            if(!in_array($type,array(
                #'Discipline','Competition','Competitor','Competitors','Delegate','Admin','RequestCandidate','Alternative',
                'Login','LogoIcon','Logs',
                'Meetings','CompetitionGoals','MosaicBuilding','MosaicBuilding2',
                'Achievements','FriendsCompetitions','WCA_API','MailUpcomingCompetition','Schedule','Validation','Translit'
                ))){
                self::set404("Type $type not found ");
                return;
            }
        }
    #    if($type=='Scramble'){
    #        self::$page="Scramble";
    #        self::$title.=' / Scramble / '.$request[1];  
    #        self::$param1=$request[1];
    #    }
        
        if($type=='Alternative'){
            self::$page="Alternative";
            self::$title.=' &#9642;  Alternative';
        }
        
        if($type=='Validation'){
            self::$page="Validation";
            self::$title.=' &#9642;  Validation';
        }
        
        if($type=='Translit'){
            self::$page="Translit";
            self::$title.=' &#9642;  Translit';
        }
        
        
        if($type=='Admin'){
            if(!CheckAdmin()){
                self::set401(" Unauthorized for Senior Judge ");
                return; 
            }else{
                self::$page="Admin";
                self::$title.=' &#9642;  Senior Judge &#9642; '. GetDelegateData()['Delegate_Name'];
            }
        }
        
        if($type=='RequestCandidate'){
                    self::$page="RequestCandidate";
                    self::$title.=' &#9642;  Request Candidate';
                    if(isset($request[1]) and DataBaseClass::Escape($request[1])=='config'){
                        if(!CheckAdmin()){
                            self::set401(" Unauthorized for setting Request Candidate");
                            return;
                        }else{
                            self::$page="RequestCandidateConfig";
                            self::$title.=' &#9642;  Request Candidate &#9642; Config';
                        }
                    }
                    
        }
        
        if($type=='Delegate'){
            $Site=isset($request[1])?DataBaseClass::Escape($request[1]):"_empty_";
            if($Site=='Add'){
                if(!CheckAdmin()){
                    self::set401(" Unauthorized for add delegate");
                    return; 
                }else{
                    self::$page="DelegateAdd";
                    self::$title.=' &#9642;  Add Delegate';
                }
            }else{
                $delegate = DataBaseClass::SelectTableRow('Delegate', "WCA_ID='$Site'");
                if($delegate){
                    if($delegate['Delegate_Candidate']){
                        self::$title.=' &#9642; Candidate Judge &#9642; '. $delegate['Delegate_Name'];
                    }else{
                        self::$title.=' &#9642; Judge &#9642; '. $delegate['Delegate_Name'];    
                    }
                    self::$page="Delegate";
                    self::$param1=$Site;
                    if(isset($request[2]) and DataBaseClass::Escape($request[2])=='config'){
                        if(!GetDelegateData($Site) and !CheckAdmin()){
                            self::set401(" Unauthorized for setting Judge $Site ");
                            return; 
                        }else{
                            self::$page="DelegateConfig";
                            self::$title.=' &#9642;  '.$delegate['Delegate_Name'].' &#9642;  Setting';
                        }
                    }   

                }else{
                    self::set404(" Delegate $Site not found ");
                    return;
                }   
            }
        }
        
        if($type=='Discipline'){
            $Code=isset($request[1])?DataBaseClass::Escape($request[1]):"_empty_";
            if($Code=='Add'){
                if(!CheckAdmin()){
                    self::set401(" Unauthorized for add discipline");
                    return; 
                }else{
                    self::$page="DisciplineAdd";
                    self::$title.=' &#9642;  Add Discipline';
                }
            }else{
                $discipline = DataBaseClass::SelectTableRow('Discipline', "Code='$Code'");
                if($discipline){
                    self::$title.=' &#9642;  '.$discipline['Discipline_Name'];
                    self::$page="Discipline";
                    self::$param1=$Code;
                    if(isset($request[2]) and DataBaseClass::Escape($request[2])=='config'){
                        if(!CheckAdmin()){
                            self::set401(" Unauthorized for config discipline $Code ");
                            return; 
                        }else{
                            self::$page="DisciplineConfig";
                            self::$title.=' &#9642;  Setting';
                        }
                    }  
                }elseif($Code=='MosaicBuilding'){
                    self::$title.=' &#9642;  Mosaic Building';
                    self::$page="Discipline";
                    self::$param1=$Code;   
                }else{
                    self::set404(" Discipline $Code not found ");
                    return;
                }   
            }
        }
        
        
        if($type=='LogoIcon'){
            self::$title.=' &#9642;  Logos & Icons';
            self::$page="LogoIcon";  
        }
        
        if($type=='Competitors'){
            self::$page="Competitors";
            self::$title.=' &#9642;  Competitors';  
        }
        
        if($type=='Competitor'){
            $ID=isset($request[1])?DataBaseClass::Escape($request[1]):"_empty_";
            
            if(is_numeric($ID)){
                $competitior = DataBaseClass::SelectTableRow('Competitor', "ID='$ID'");
            }else{
                $competitior = DataBaseClass::SelectTableRow('Competitor', "WCAID='$ID'");    
            }
            
            if($competitior){
                self::$title.=' &#9642;  '.Short_Name($competitior['Competitor_Name']);
                self::$page="Competitor";
                self::$param1=$competitior['Competitor_ID'];
            }else{
                self::set404(" Competitor $ID not found ");
                return;
            }   
        }
        
        if($type=='Competition'){
            $WCA=isset($request[1])?DataBaseClass::Escape($request[1]):"_empty_";
            $delegate=GetDelegateData();
            if($WCA=='Add'){
                if(!$delegate or $delegate['Delegate_Candidate'] or $delegate['Delegate_Status']!='Active'){
                    self::set401(" Unauthorized for add competition");
                    return; 
                }else{
                    self::$page="CompetitionAdd";
                    self::$title.=' &#9642;  Add Competition';
                }
                
            }else{
                $competition = DataBaseClass::SelectTableRow('Competition', "WCA='$WCA'");
                if($competition){
                    self::$title.=' &#9642;  '.$competition['Competition_Name'];
                    self::$page="Competition";
                    self::$param1=$WCA;
                    if(isset($request[2])){
                        $Code=$request[2];
                        if($Code=='config'){
                            if(!CheckDelegateCompetition($competition['Competition_ID'])){
                                self::set401(" Unauthorized for setting competition $WCA ");
                                return; 
                            }else{
                                self::$page="CompetitionConfig";
                                self::$title.=' &#9642;  Setting';
                                 self::$param2='config';
                            }
                        }elseif($Code=='report'){
                            if(!$delegate){
                                self::set401(" Unauthorized for reports");
                                return; 
                            }else{
                                self::$page="CompetitionReport";
                                self::$title.=' &#9642;  Report';
                                 self::$param2='report';
                            }
                        }elseif($Code=='MosaicBuilding'){
                            if(isset($request[3]) and $request[3]=='config'){
                                if(!CheckDelegateCompetition($competition['Competition_ID'],false)){
                                    self::set401(" Unauthorized for settings MosaicBuilding in ".$competition['Competition_WCA']);
                                    return; 
                                }else{
                                    self::$page="EventConfigMosaicBuilding";
                                    self::$title.=' &#9642; Mosaic Building &#9642;  Setting';
                                    self::$param3='config';
                                }
                            }else{
                                self::$title.=' &#9642;  Mosaic Building';
                            }
                        }else{  
                            DataBaseClass::FromTable('Competition', "WCA='$WCA'");
                            DataBaseClass::Join('Competition', 'Event');
                            DataBaseClass::Join('Event', 'DisciplineFormat');
                            DataBaseClass::Join_current('Discipline');
                            if(isset($request[3]) and is_numeric($request[3])){
                                DataBaseClass::Where('Event',"Round='$request[3]'");
                            }else{
                                DataBaseClass::Where('Event',"Round=1");
                                $request[3]=1;
                            }
                            self::$param3=$request[3];
                            DataBaseClass::Where('Discipline',"Code='$Code'");
                            $discipline=DataBaseClass::QueryGenerate(false);
                            if($discipline){
                                if(str_replace(": ","",$discipline['Event_vRound'])){
                                    self::$title.=' &#9642;  '.$discipline['Discipline_Code'].' &#9642; '.str_replace(": ","",$discipline['Event_vRound']);
                                }else{
                                    self::$title.=' &#9642;  '.$discipline['Discipline_Code'];
                                }
                                self::$param2=$discipline['Event_ID'];
                                if(isset($request[4])){
                                    if($request[4]=='config'){
                                        if(!CheckDelegateCompetition($competition['Competition_ID'],false)){
                                            self::set401(" Unauthorized for settings Event ".$discipline['Discipline_Code']." in ".$competition['Competition_WCA']);
                                            return; 
                                        }else{
                                            self::$page="EventConfig";
                                            self::$title.=' &#9642;  Setting';
                                            self::$param3='config';
                                        }
                                    }
                                }
                            }else{
                            self::set404(" Discipline $Code not found in Competition $WCA");
                            return;
                            }
                        }
                    }
                    
                }else{
                    self::set404(" Competition $WCA not found ");
                    return;
                }
            }
        }
        
        if($type=='Meetings'){
            $Secret=isset($request[1])?DataBaseClass::Escape($request[1]):"_empty_";
            $meeting = DataBaseClass::SelectTableRow('Meeting', "Secret='$Secret'");
            if($meeting){
                    self::$title='Unnoficial Competition &#9642;  '.$meeting['Meeting_Name'];
                    self::$page="Meeting";
                    self::$param1=$meeting['Meeting_Secret'];
            }else{
                self::set404("Unnoficial Competition $Secret not found ");
                return;
            }   
        }
        
        if($type=='CompetitionGoals'){
            $wca=isset($request[1])?DataBaseClass::Escape($request[1]):"_empty_";
            $competition = DataBaseClass::SelectTableRow('GoalCompetition', "WCA='$wca'");
            if($competition){
                    self::$title='Competition Goals &#9642;  '.$competition['GoalCompetition_Name'];
                    self::$page="CompetitionGoal";
                    self::$param1=$wca;
            }else{
                self::set404("Competition Goals $wca not found ");
                return;
            }   
        }
        
        if($type=='MosaicBuilding'){
            self::$page="MosaicBuilding";
        }
        
        if($type=='MosaicBuilding2'){
            self::$page="MosaicBuilding2";
        }
        
        if($type=='Achievements'){
            self::$page="Achievements";
        }
        
        if($type=='FriendsCompetitions'){
            self::$page="FriendsCompetitions";
        }
        
        if($type=='WCA_API'){
            self::$page="WCA_API";
        }
        
        if($type=='MailUpcomingCompetition'){
            self::$page="MailUpcomingCompetition";
        }
        
        if($type=='Schedule'){
            self::$page="Schedule";
        }
       

        
    }
    
    private static function set404($error){
        self::$title.=' &#9642;  404';
        self::$error[404]=$error;
        self::$page="error";  
    }
 
    private static function set401($error){
        self::$title.=' &#9642;  401';
        self::$error[401]=$error;
        self::$page="error";  
    }
    
    public static function getParam1(){
        return self::$param1;
    }
    
    public static function getParam2(){
        return self::$param2;
    }
    
    public static function getParam3(){
        return self::$param3;
    }
    
    public static function getParam4(){
        return self::$param4;
    }
    
    public static function getPage(){
        return self::$page;
    }
    
    public static function getTitle(){
        return self::$title;
    }

    public static function getError($n=0){
        if($n){
            return self::$error[$n];
        }else{
            $error_return="";
            foreach(self::$error as $error){
                $error_return.=$error;
            }
            return $error_return;
        }
    }

}


