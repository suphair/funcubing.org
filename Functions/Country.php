<?php
function CountryCode_($str,$size=30){
    $str=str_replace(" ","-",$str);
    if(file_exists("Image/Flags/$str.png")){
        return "<img title='$str' width=$size src='".PageIndex()."Image/Flags/$str.png'>";
    }elseif($str!=""){
        return substr($str,0,2);
    }else{
        return "<img title='$str' width=$size src='".PageIndex()."Image/Flags/All.png'>";
    }
    
}

function CountryName($str){
    if($str=='All')return 'All countries';
    DataBaseClass::Query("Select * from Country where ISO2='$str'");
    $country=DataBaseClass::GetRow();
    if(isset($country['Name'])){
        return $country['Name'];
    }else{
        return $str;    
    }
}

function CountryNames($str){
    DataBaseClass::Query("Select * from Country");
    $countries=[];
    foreach(DataBaseClass::GetRows() as $country){
        $countries[$country['ISO2']]=$country['Name'];
    }
    $strs=[];
    foreach(explode(",",$str) as $s){
       if(isset($countries[$s])){
            $strs[]=$countries[$s].' ('.$s.')';
       }else{
            $strs[]=$s;
       }
    }
    return implode(", ",$strs);
}

function ImageCountry($country,$width){ 
    if($country){
        if(file_exists("Image/Flags/".strtolower($country).".png")){ ?>
            <img alt="<?= $country?>" width="<?= $width ?>" style="vertical-align: middle" src="<?= PageIndex() ?>Image/Flags/<?= strtolower($country)?>.png">
        <?php }else{ ?>   
            &bull;
        <?php } ?>        
    <?php }else{ ?>
        <img alt="world" width="<?= $width ?>" style="vertical-align: middle" src="<?= PageIndex() ?>Image/Flags/world.png">
    <?php }?>
<?php }