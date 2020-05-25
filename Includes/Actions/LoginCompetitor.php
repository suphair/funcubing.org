<?php


$url_refer = PageIndex().GetIni('WCA_AUTH','url_refer');
if(strpos($_SERVER['PHP_SELF'],'/'.GetIni('LOCAL','PageBase').'/')!==false){
   $url_refer="http:".$url_refer;
}else{
   $url_refer="https:".$url_refer;
}
$client_id = GetIni('WCA_AUTH','client_id');
$client_secret = GetIni('WCA_AUTH','client_secret');
$scope= GetIni('WCA_AUTH','scope');
$url = "https://www.worldcubeassociation.org/oauth/authorize?client_id=$client_id&redirect_uri=".urlencode($url_refer)."&response_type=code&scope=$scope";

unset($_SESSION['Competitor']);

if(isset($_GET['error']) and $_GET['error']=='access_denied'){
    header('Location: '.$_SESSION['Refer']);
    exit();    
}

$code="";
if(isset($_GET['code'])){
    $code=$_GET['code'];

    $postdata = http_build_query(
    array(
        'grant_type' => 'authorization_code',
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'code'=>$code,
        'redirect_uri'=>$url_refer
    )
    );
    $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );
    $context = stream_context_create($opts);
    $result = file_get_contents("https://www.worldcubeassociation.org/oauth/token", false, $context); 
    $access_token=json_decode($result)->access_token;


    $ch = curl_init('https://www.worldcubeassociation.org/api/v0/me'); // Initialise cURL
    
    $authorization = "Authorization: Bearer ".$access_token; // Prepare the authorisation token
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization ));
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

    $result = curl_exec($ch);
    curl_close($ch);
    
    #$name=json_decode($result)->me->name;
    #$wcaid=json_decode($result)->me->wca_id;
    #$wid=json_decode($result)->me->id;
    #$country=json_decode($result)->me->country_iso2;
    if(isset(json_decode($result)->me->id)){
        $competitor=json_decode($result)->me;
        $competitor = (object) array_merge( (array)$competitor );
        $_SESSION['Competitor']=$competitor;
    }
    
    
/*    
    if($name and $wid){
        DataBaseClass::FromTable("Delegate","WID='$wid'");
        DataBaseClass::QueryGenerate();
        if(DataBaseClass::rowsCount()==1){
            DataBaseClass::Query("Update Delegate set Name='$name',WCA_ID='$wcaid' where WID=$wid");    
        }else{
            DataBaseClass::FromTable("Delegate","WCA_ID='$wcaid'");    
            DataBaseClass::QueryGenerate();
            if(DataBaseClass::rowsCount()==1 and $wcaid){
                DataBaseClass::Query("Update Delegate set Name='$name', WID=$wid where WCA_ID='$wcaid'");    
            }
        }
        
        DataBaseClass::FromTable("Competitor","WID='$wid'");
        $Competitor=DataBaseClass::QueryGenerate();
       
        if(DataBaseClass::rowsCount()==1){
            DataBaseClass::Query("Update Competitor set Name='$name',WCAID='$wcaid',Country='$country' where WID=$wid");    
            $CompetitorID=$Competitor[0]['Competitor_ID'];
        }else{
            DataBaseClass::FromTable("Competitor","WCAID='$wcaid'");    
            $Competitor=DataBaseClass::QueryGenerate();
            if(DataBaseClass::rowsCount()==1 and $wcaid){
                DataBaseClass::Query("Update Competitor set Name='$name', WID=$wid,Country='$country' where WCAID='$wcaid'");    
                $CompetitorID=$Competitor[0]['Competitor_ID'];
            }else{
                $short_name= strtolower(Short_Name($name));
                DataBaseClass::FromTable("Competitor");  
                DataBaseClass::WhereCustom("LOWER(Name) like '$short_name%'");
                DataBaseClass::Where_current("WID is null");
                $Competitor=DataBaseClass::QueryGenerate();
                if(DataBaseClass::rowsCount()==1){
                    DataBaseClass::Query("Update Competitor set WCAID='$wcaid', WID=$wid,Country='$country' where ID=".$Competitor[0]['Competitor_ID']);    
                    $CompetitorID=$Competitor[0]['Competitor_ID'];
                }else{
                    DataBaseClass::Query("Insert Into Competitor (WCAID,WID,Country,Name) values ('$wcaid',$wid,'$country','$name')");    
                    $CompetitorID=DataBaseClass::getID();
                }   
            }
        }

        $competitor=json_decode($result)->me;
        $competitor = (object) array_merge( (array)$competitor, array( 'local_id' => $CompetitorID ) );
        $_SESSION['Competitor']=$competitor;
        #DataBaseClass::Query("Insert into `WCAauth` (Name,WCAID,WID,Country) values ('$name','$wcaid','$wid','$country')");
        
        CommandUpdateCompetitor($CompetitorID);
        
        AddLog('WCA_Auth','Login',$name);

    }
   */     
    if(isset($_SESSION['ReferAuth']) and strpos($_SESSION['ReferAuth'],'/flag-icon-css/css/flag-icon.css')===false){
        header('Location: '.$_SESSION['ReferAuth']);
    }else{
        header('Location: '. PageIndex());
    }
    exit();
}

?>