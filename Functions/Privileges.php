<?php


Function CheckAdmin(){
    if(isset($_SESSION['Competitor'])){
        DataBaseClass::FromTable("Delegate","WCA_ID='".$_SESSION['Competitor']->wca_id."' and Admin=1 and Status='Active'");    
        $delegate=DataBaseClass::QueryGenerate(false);
        if(DataBaseClass::rowsCount()){
            return true;
        }
    }
    return false;
}

Function GetCompetitorData(){
    if(isset($_SESSION['Competitor'])){        
        if(!isset($_SESSION['Competitor']->id)){
            unset($_SESSION['Competitor']);
            return false;        
        }
        
        return $_SESSION['Competitor'];
    }
    return false;
}

Function GetDelegateData(){
    if(isset($_SESSION['Competitor'])){
        DataBaseClass::FromTable("Delegate","WCA_ID='".$_SESSION['Competitor']->wca_id."' and Status='Active'");    
        $delegate=DataBaseClass::QueryGenerate(false);
        if(DataBaseClass::rowsCount()){
            return $delegate;
        }
    }
    return false;
}


Function CheckDelegateCompetition($Competition,$NotCandidate=true){
    if(CheckAdmin()){
        return true;
    }
    
    if(isset($_SESSION['Competitor'])){
       DataBaseClass::Query("Select C.ID From `Competition` C "
               . " join CompetitionDelegate CD on CD.Competition=C.ID "
               . " join Delegate D on D.ID=CD.Delegate "
               . " where C.ID='$Competition' ".($NotCandidate?" and D.Candidate=0":"")
               . " and D.WCA_ID='".$_SESSION['Competitor']->wca_id."' and D.Status='Active'");
       if(DataBaseClass::rowsCount()!=0){
           return true;
       }
    }
    
    return false;
}



Function CheckingRoleAdmin(){
    if(!CheckAdmin()){
        SetMessage("admin access denied");
        HeaderExit(); 
    }    
}

Function CheckingRoleDelegate($Competition,$NotCandidate=true){
    if(!CheckAdmin() and !CheckDelegateCompetition($Competition,$NotCandidate)){  
        SetMessage("delegate access denied");
        HeaderExit(); 
    }
}

Function CheckingRoleDelegateEvent($Event,$NotCandidate=true){
    DataBaseClass::Query("Select Competition from `Event` where `ID`=$Event");
    CheckingRoleDelegate(DataBaseClass::getRow()['Competition'],$NotCandidate);
    return true;
}

function DelegateEventMail($event){
    DataBaseClass::Query("Select D.Email from `Event` E join `Competition` C on C.ID=E.Competition join `Delegate` D on D.ID=C.Delegate where E.`ID`='$event'");
    return DataBaseClass::getRow()['Email'];
}

function DelegateMail($ID){
    DataBaseClass::Query("Select Email from `Delegate` where `ID`='$ID'");
    return DataBaseClass::getRow()['Email'];
}


function CheckingScoreTakerCompetitor($CommandID,$Secret){
    DataBaseClass::Query("Select Com.ID from `Command` Com join `Event` E on E.ID=Com.Event  where Com.`ID`='$CommandID' and E.Secret='$Secret'");
    if(!DataBaseClass::rowsCount()){  
        SetMessage("score taker access denied");
        HeaderExit(); 
    }   
}

function GetScoreTakerEvent($Secret){
    DataBaseClass::Query("Select ID from `Event` E where E.Secret='$Secret'");
    
    if(!DataBaseClass::rowsCount()){  
        SetMessage("score taker not exists");
        HeaderExit(); 
    }   
    return DataBaseClass::getRow()['ID'];
}

function CheckingScoreTakerCompetitorFest($Competition,$Secret){
    DataBaseClass::Query("Select ID from `Competition` where `ID`='$Competition' and Secret='$Secret'");
    
    if(!DataBaseClass::rowsCount()){  
        SetMessage("score taker access denied");
        HeaderExit(); 
    }   
}

function CheckingScoreTakerEvent($EventID,$Secret){
    DataBaseClass::Query("Select E.ID from  `Event` E where E.`ID`='$EventID' and E.Secret='$Secret'");
    
    if(!DataBaseClass::rowsCount()){  
        SetMessage("score taker access denied");
        HeaderExit(); 
    }   
}