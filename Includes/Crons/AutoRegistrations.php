<?php


$DATAS=[
    ['WCAID'=>'2015SOLO01','Password'=>'kostyaWrN','Competition'=>'FMCEurope2020',
        'events'=>['333fm'],'guests'=>[]],
];


function request_WCA($url,$cookie,$post = 0){
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url );
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/'.$cookie.'.txt');
  curl_setopt($ch, CURLOPT_COOKIEFILE,  dirname(__FILE__).'/'.$cookie.'.txt');
  curl_setopt($ch, CURLOPT_POST, $post!==0 );
  if($post)
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
  $data = curl_exec($ch);
  $status=curl_getinfo($ch, CURLINFO_HTTP_CODE); 
  curl_close($ch);
  return [$data,$status];
}


function request_json_error($url,$cookie){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url );
    curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/'.$cookie.'.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE,  dirname(__FILE__).'/'.$cookie.'.txt');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    $status=curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $result=json_decode($data,true);
    if(!isset($result['error'])){
        $error='none';
    }else{
        $error=$result['error'];
    }
    return $error;
}

$result="";        
foreach($DATAS as $DATA){
    $result.=" ".$DATA['WCAID'];
    $competition=$DATA['Competition'];
    $URL_sign_in="https://www.worldcubeassociation.org/users/sign_in";
    $URL_wcif="https://www.worldcubeassociation.org/api/v0/competitions/$competition/wcif";
    $URL_register="https://www.worldcubeassociation.org/competitions/$competition/register";
    $URL_registrations="https://www.worldcubeassociation.org/competitions/$competition/registrations";
    
    $cookie='/COOKIES/cookie_'.$DATA['WCAID'];
    $error=request_json_error($URL_wcif,$cookie);
    if($error=='Not logged in'){
        $data = request_WCA($URL_sign_in,$cookie)[0];    
        $data = str_get_html($data);
        $token=$data->find('input[name="authenticity_token"]',0)->value;

        $auth = array(
          'user[login]'=>$DATA['WCAID'],
          'user[password]'=>$DATA['Password'],
          'authenticity_token'=>$token,
        );
        request_WCA($URL_sign_in,$cookie,$auth);
        $error=request_json_error($URL_wcif,$cookie); 
        $result.='SIGNIN';
    }

    if($error=='Not authorized to manage competition'){
        $data = request_WCA($URL_register,$cookie)[0];    
        $data = str_get_html($data);
        if($el=$data->find('input[name="authenticity_token"]',0)){
            if($data->find('#new_registration',0)){
                $token=$el->value;

                $events_ids=[];
                $post = array(
                  'authenticity_token'=>$token,
                  'registration[guests]'=>sizeof($DATA['guests']),
                  'registration[comments]'=> implode(", ",$DATA['guests'])
                );
                $event_n=0;
                foreach($DATA['events'] as $event){
                    if($el=$data->find('#registration_competition_events_'.$event,0)){
                        $post['registration[registration_competition_events_attributes]['.$event_n.'][competition_event_id]']=$el->parent()->parent()->children(0)->value;
                        $event_n++;
                    }
                }
                request_WCA($URL_registrations,$cookie,$post);
                $result.='REGISTER COMPLETE';     
                SendMail("suphair@gmail.com",$DATA['WCAID']." $competition REGISTER","<a href='$URL_register'>Registration complete</a>");
            }else{
                $result.='REGISTERED YET';    
            }
        }else{
            $result.='CLOSE';
        }
    }
}

if($result)
SaveValue('AutoRegistrations',$result);

exit();
