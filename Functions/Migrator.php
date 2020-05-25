<?php
function AttemptsExceptUpdater(){
    DataBaseClass::FromTable("Command");
    foreach(DataBaseClass::QueryGenerate() as $command){
            DataBaseClass::FromTable("Attempt","Command=".$command['Command_ID']);     
            DataBaseClass::Where_current("Special is null");
            DataBaseClass::OrderClear("Attempt", "vOrder");
            DataBaseClass::Order("Attempt", "Attempt");
            $attemps=DataBaseClass::QueryGenerate();
            $NDFS=array();
            $ATTS=array();
            if(sizeof($attemps)==5){
                foreach($attemps as $a){
                    if($a['Attempt_IsDNF'] or $a['Attempt_IsDNS']){
                        $NDFS[]=$a;
                    }else{
                        $ATTS[]=$a;
                    }
                }
                $EXCS=array();
                if(sizeof($NDFS)==0){
                    $EXCS=array($ATTS[0],$ATTS[4]);
                }
                
                if(sizeof($NDFS)>=1 and sizeof($NDFS)<5){
                    $EXCS=array($NDFS[0],$ATTS[0]);
                }
                
                if(sizeof($NDFS)==5){
                    $EXCS=array($NDFS[0],$NDFS[1]);
                }
                
                
                DataBaseClass::Query("Update Attempt set Except=0 where Command=".$command['Command_ID']);
                foreach($EXCS as $e){
                    DataBaseClass::Query("Update Attempt set Except=1 where ID=".$e['Attempt_ID']);    
                }
            }
         
         
     }
    
    
    
}

function CompetitionsUpdater(){
    
    DataBaseClass::FromTable("Competition");
    $Competitions= DataBaseClass::QueryGenerate();
    foreach($Competitions as $Competition){
        $WCA=$Competition['Competition_WCA'];
        $result=@file_get_contents(GetIni('WCA_API','competition')."/$WCA");
        $registrations=json_decode($result);
        
        if(!$registrations){
            echo GetIni('WCA_API','competition')."/$WCA";
            echo "ERROR CompetitionsUpdater: ".$WCA;
            Exit();  
        }

        $Name=$registrations->short_name;
        $City=$registrations->city;
        $Country=$registrations->country_iso2;
        $StartDate=$registrations->start_date;
        $EndDate=$registrations->end_date;
        $WebSite=$registrations->website;
        DataBaseClass::Query("Update `Competition` set "
        . "`Name`='$Name',"
        . "`StartDate`='$StartDate',"
        . "`EndDate`='$EndDate',"
        . "`City`='$City',"
        . "`Country`='$Country',"
        . "`WebSite`='$WebSite'"
        . " where `WCA`='$WCA'");
    }    
}

function CompetitorCountryUpdater(){
    DataBaseClass::Query("Update Competitor set Country='RU' where Country='Russia'");
    DataBaseClass::Query("Update Competitor set Country='UA' where Country='Ukraine'");
    DataBaseClass::Query("Update Competitor set Country='BY' where Country='Belarus'");
    DataBaseClass::Query("Update Competitor set Country='IL' where Country='Israel'");
    DataBaseClass::Query("Update Competitor set Country='KZ' where Country='Kazakhstan'");
    DataBaseClass::Query("Update Competitor set Country='EE' where Country='Estonia'");
    DataBaseClass::Query("Update Competitor set Country='PL' where Country='Poland'");
    DataBaseClass::Query("Update Competitor set Country='CN' where Country='China'");
    DataBaseClass::Query("Update Competitor set Country='AM' where Country='Armenia'");
    
    DataBaseClass::Query("Select distinct Country from Competitor where length(Country)>2");
    $checks=DataBaseClass::getRows();
    if(sizeof($checks)){
        echo "ERROR CompetitorCountryUpdater: ";
        foreach($checks as $c){
            echo $c['Country'].' / ';
        }
        Exit(); 
    }
}

function CommandUpdater(){
    CommandUpdate();
}


function AttemptUpdater($Com=0){
    if($Com){
        DataBaseClass::FromTable("Attempt","Command=$Com");
    }else{
        DataBaseClass::FromTable("Attempt");    
    }
    $Attempts= DataBaseClass::QueryGenerate();
   
    foreach($Attempts as $r){
        $string="";
        $order=1000*1000000+999999;
        if(isset($r['Attempt_IsDNF']) and $r['Attempt_IsDNF']){
            $string='DNF';
        }elseif(isset($r['Attempt_IsDNS']) and $r['Attempt_IsDNS']){
            $string='DNS';
        }else{
            $order= $r['Attempt_Minute']*60*100+$r['Attempt_Second']*100+$r['Attempt_Milisecond'];
            if($r['Attempt_Minute']){
                $string=sprintf( "%d:%02d.%02d", $r['Attempt_Minute'],$r['Attempt_Second'],$r['Attempt_Milisecond']);
            }elseif($r['Attempt_Second']){
                $string=sprintf( "%2d.%02d", $r['Attempt_Second'],$r['Attempt_Milisecond']);
            }else{
                $string=sprintf( "0.%02d", $r['Attempt_Milisecond']);
            }
        
            if($string=="0.00")$string="";
            if($r['Attempt_Amount']>0){
                if($r['Attempt_Special']!='Mean'){
                    $string=round($r['Attempt_Amount']).' ('.$string.')';
                }else{
                    $string=$r['Attempt_Amount'];
                }
            }
            
            $order=(1000-$r['Attempt_Amount'])*1000000+$order;
            
        }
        DataBaseClass::Query("Update Attempt set `vOut`='$string',`vOrder`=$order where ID=".$r['Attempt_ID']);
    }
}

function CompetitorEmptyDeleter(){
    DataBaseClass::Query("Delete from `Competitor` where ID not in 
    (select Competitor from `CommandCompetitor`) and WID is null ");
}

function  CreateCommandCompetitor(){
    DatabaseClass::Query("Delete from CommandCompetitor");

    
    DataBaseClass::Query("Select C.Name Competitor_Name,C.ID Competitor_ID, Com.ID Command_ID"
            . " from Command Com join Competitor C on C.ID=Com.Competitor");
    $rows=DatabaseClass::getRows();

    foreach($rows as $row){
        $names=explode("&",$row['Competitor_Name']);

        foreach($names as $name){
            if(trim($name)){
                $name=trim($name);
                DatabaseClass::FromTable('Competitor', "Name='$name'");
                $competitor=DatabaseClass::QueryGenerate(false);
                if(!isset($competitor['Competitor_ID'])){
                    echo "ERROR $name";
                    exit();
                }
                DatabaseClass::Query("Insert into CommandCompetitor (Command,Competitor) "
                        . " values (".$row['Command_ID'].",".$competitor['Competitor_ID'].")");  
            }
        }
    }
}


/* 
 * # For each database:
ALTER DATABASE suphair_funcubing CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
# For each table:
ALTER TABLE Competitor CHANGE Name Name VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
 */

/*
update `Event` set Discipline=24, Round=2 where Discipline=43
Delete from `Discipline`where ID=43
 * 
 * Update `Competition` set Registration=0 where Registration=-1
 * Update `Competition` set Status=0 where Status=-1
 * 
 *
 * 
 * 
update Competition C set `MaxCardID` =
(
select max(CardID) from Command Com
join Event E on E.ID=Com.Event where E.Competition=C.ID
group by E.Competition
)
 * 
 * 
 * 
 */


function AlterDB(){
    
    /*
    DataBaseClass::Query("ALTER TABLE `Delegate` DROP COLUMN `Password`, DROP COLUMN `Parent`, ADD COLUMN `OrderLine` int DEFAULT 99 AFTER `Admin`");
    DataBaseClass::Query("Update `Delegate` set OrderLine=1 where ID=5");
    DataBaseClass::Query("Update `Delegate` set OrderLine=2 where ID=4");
    DataBaseClass::Query("Update `Delegate` set OrderLine=3 where ID=6");
    DataBaseClass::Query("ALTER TABLE `Competitor` ADD COLUMN `WID` int AFTER `Country`");
    DataBaseClass::Query("RENAME TABLE CompetitorEvent TO Command");
    
    
    DataBaseClass::Query("DROP TABLE IF EXISTS `CommandCompetitor`;");
    DataBaseClass::Query("CREATE TABLE `CommandCompetitor` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `Command` int(11) DEFAULT NULL,
          `Competitor` int(11) DEFAULT NULL,
          `CheckStatus` int(11) DEFAULT '1',
          PRIMARY KEY (`ID`),
          KEY `Command` (`Command`),
          KEY `Competitor` (`Competitor`),
          CONSTRAINT `commandcompetitor_ibfk_1` FOREIGN KEY (`Command`) REFERENCES `Command` (`ID`),
          CONSTRAINT `commandcompetitor_ibfk_2` FOREIGN KEY (`Competitor`) REFERENCES `Competitor` (`ID`)
        ) ENGINE=InnoDB AUTO_INCREMENT=6403 DEFAULT CHARSET=utf8;");   
    CreateCommandCompetitor();
    
    DataBaseClass::Query("ALTER TABLE `Competition` ADD COLUMN `StartDate` date AFTER `DateStart`, ADD COLUMN `EndDate` date AFTER `StartDate`;");
    DataBaseClass::Query("ALTER TABLE `Competition` ADD COLUMN `WebSite` varchar(255) AFTER `EndDate`, ADD COLUMN `MaxCardID` integer AFTER `WebSite`;");
    DataBaseClass::Query("update Competition C set `MaxCardID` =(
        select max(CardID) from Command Com
        join Event E on E.ID=Com.Event where E.Competition=C.ID
        group by E.Competition)
        ");  
    
    DataBaseClass::Query("ALTER TABLE `Competition` ADD COLUMN `CheckDateTime` timestamp NULL DEFAULT NULL AFTER `MaxCardID`;");
    
    DataBaseClass::Query("ALTER TABLE `Competition` DROP COLUMN `Date`, DROP COLUMN `Fest`, DROP COLUMN `Secret`, DROP COLUMN `DateStart`;");
    
    DataBaseClass::Query("Update Competition set Registration=0 where Registration=-1");
     * 
     */
    CompetitionsUpdater();
    
    DataBaseClass::Query("ALTER TABLE `Command` ADD COLUMN `vCompetitors` int AFTER `Competitors`, ADD COLUMN `vCountry` varchar(255) AFTER `vCompetitors`, ADD COLUMN `vName` varchar(255) AFTER `vCountry`;");
    
    DataBaseClass::Query("
CREATE TABLE `Country` (
  `Name` varchar(255) DEFAULT NULL,
  `ISO2` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"); 
DataBaseClass::Query("
INSERT INTO `Country` VALUES ('Абхазия', 'AB'), ('Австралия', 'AU'), ('Австрия', 'AT'), ('Азербайджан', 'AZ'), ('Албания', 'AL'), ('Алжир', 'DZ'), ('Американское Самоа', 'AS'), ('Ангилья', 'AI'), ('Ангола', 'AO'), ('Андорра', 'AD'), ('Антарктида', 'AQ'), ('Антигуа и Барбуда', 'AG'), ('Аргентина', 'AR'), ('Армения', 'AM'), ('Аруба', 'AW'), ('Афганистан', 'AF'), ('Багамы', 'BS'), ('Бангладеш', 'BD'), ('Барбадос', 'BB'), ('Бахрейн', 'BH'), ('Беларусь', 'BY'), ('Белиз', 'BZ'), ('Бельгия', 'BE'), ('Бенин', 'BJ'), ('Бермуды', 'BM'), ('Болгария', 'BG'), ('Боливия  Многонациональное Государство', 'BO'), ('Бонайре  Саба и Синт-Эстатиус', 'BQ'), ('Босния и Герцеговина', 'BA'), ('Ботсвана', 'BW'), ('Бразилия', 'BR'), ('Британская территория в Индийском океане', 'IO'), ('Бруней-Даруссалам', 'BN'), ('Буркина-Фасо', 'BF'), ('Бурунди', 'BI'), ('Бутан', 'BT'), ('Вануату', 'VU'), ('Венгрия', 'HU'), ('Венесуэла Боливарианская Республика', 'VE'), ('Виргинские острова  Британские', 'VG'), ('Виргинские острова  США', 'VI'), ('Вьетнам', 'VN'), ('Габон', 'GA'), ('Гаити', 'HT'), ('Гайана', 'GY'), ('Гамбия', 'GM'), ('Гана', 'GH'), ('Гваделупа', 'GP'), ('Гватемала', 'GT'), ('Гвинея', 'GN'), ('Гвинея-Бисау', 'GW'), ('Германия', 'DE'), ('Гернси', 'GG'), ('Гибралтар', 'GI'), ('Гондурас', 'HN'), ('Гонконг', 'HK'), ('Гренада', 'GD'), ('Гренландия', 'GL'), ('Греция', 'GR'), ('Грузия', 'GE'), ('Гуам', 'GU'), ('Дания', 'DK'), ('Джерси', 'JE'), ('Джибути', 'DJ'), ('Доминика', 'DM'), ('Доминиканская Республика', 'DO'), ('Египет', 'EG'), ('Замбия', 'ZM'), ('Западная Сахара', 'EH'), ('Зимбабве', 'ZW'), ('Израиль', 'IL'), ('Индия', 'IN'), ('Индонезия', 'ID'), ('Иордания', 'JO'), ('Ирак', 'IQ'), ('Иран  Исламская Республика', 'IR'), ('Ирландия', 'IE'), ('Исландия', 'IS'), ('Испания', 'ES'), ('Италия', 'IT'), ('Йемен', 'YE'), ('Кабо-Верде', 'CV'), ('Казахстан', 'KZ'), ('Камбоджа', 'KH'), ('Камерун', 'CM'), ('Канада', 'CA'), ('Катар', 'QA'), ('Кения', 'KE'), ('Кипр', 'CY'), ('Киргизия', 'KG'), ('Кирибати', 'KI'), ('Китай', 'CN'), ('Кокосовые (Килинг) острова', 'CC'), ('Колумбия', 'CO'), ('Коморы', 'KM'), ('Конго', 'CG'), ('Конго  Демократическая Республика', 'CD'), ('Корея  Народно-Демократическая Республика', 'KP'), ('Корея  Республика', 'KR'), ('Коста-Рика', 'CR'), ('Кот д\'Ивуар', 'CI'), ('Куба', 'CU'), ('Кувейт', 'KW'), ('Кюрасао', 'CW'), ('Лаос', 'LA'), ('Латвия', 'LV'), ('Лесото', 'LS'), ('Ливан', 'LB'), ('Ливийская Арабская Джамахирия', 'LY'), ('Либерия', 'LR'), ('Лихтенштейн', 'LI'), ('Литва', 'LT'), ('Люксембург', 'LU'), ('Маврикий', 'MU'), ('Мавритания', 'MR'), ('Мадагаскар', 'MG'), ('Майотта', 'YT'), ('Макао', 'MO'), ('Малави', 'MW'), ('Малайзия', 'MY'), ('Мали', 'ML'), ('Малые Тихоокеанские отдаленные острова Соединенных Штатов', 'UM'), ('Мальдивы', 'MV'), ('Мальта', 'MT'), ('Марокко', 'MA'), ('Мартиника', 'MQ'), ('Маршалловы острова', 'MH'), ('Мексика', 'MX'), ('Микронезия  Федеративные Штаты', 'FM'), ('Мозамбик', 'MZ'), ('Молдова  Республика', 'MD'), ('Монако', 'MC'), ('Монголия', 'MN'), ('Монтсеррат', 'MS'), ('Мьянма', 'MM'), ('Намибия', 'NA'), ('Науру', 'NR'), ('Непал', 'NP'), ('Нигер', 'NE'), ('Нигерия', 'NG'), ('Нидерланды', 'NL'), ('Никарагуа', 'NI'), ('Ниуэ', 'NU'), ('Новая Зеландия', 'NZ'), ('Новая Каледония', 'NC'), ('Норвегия', 'NO'), ('Объединенные Арабские Эмираты', 'AE'), ('Оман', 'OM'), ('Остров Буве', 'BV'), ('Остров Мэн', 'IM'), ('Остров Норфолк', 'NF'), ('Остров Рождества', 'CX'), ('Остров Херд и острова Макдональд', 'HM'), ('Острова Кайман', 'KY'), ('Острова Кука', 'CK'), ('Острова Теркс и Кайкос', 'TC'), ('Пакистан', 'PK'), ('Палау', 'PW'), ('Палестинская территория  оккупированная', 'PS'), ('Панама', 'PA'), ('Папский Престол (Государство &mdash , город Ватикан)', 'VA'), ('Папуа-Новая Гвинея', 'PG'), ('Парагвай', 'PY'), ('Перу', 'PE'), ('Питкерн', 'PN'), ('Польша', 'PL'), ('Португалия', 'PT'), ('Пуэрто-Рико', 'PR'), ('Республика Македония', 'MK'), ('Реюньон', 'RE'), ('Россия', 'RU'), ('Руанда', 'RW'), ('Румыния', 'RO'), ('Самоа', 'WS'), ('Сан-Марино', 'SM'), ('Сан-Томе и Принсипи', 'ST'), ('Саудовская Аравия', 'SA'), ('Свазиленд', 'SZ'), ('Святая Елена  Остров вознесения  Тристан-да-Кунья', 'SH'), ('Северные Марианские острова', 'MP'), ('Сен-Бартельми', 'BL'), ('Сен-Мартен', 'MF'), ('Сенегал', 'SN'), ('Сент-Винсент и Гренадины', 'VC'), ('Сент-Китс и Невис', 'KN'), ('Сент-Люсия', 'LC'), ('Сент-Пьер и Микелон', 'PM'), ('Сербия', 'RS'), ('Сейшелы', 'SC'), ('Сингапур', 'SG'), ('Синт-Мартен', 'SX'), ('Сирийская Арабская Республика', 'SY'), ('Словакия', 'SK'), ('Словения', 'SI'), ('Соединенное Королевство', 'GB'), ('Соединенные Штаты', 'US'), ('Соломоновы острова', 'SB'), ('Сомали', 'SO'), ('Судан', 'SD'), ('Суринам', 'SR'), ('Сьерра-Леоне', 'SL'), ('Таджикистан', 'TJ'), ('Таиланд', 'TH'), ('Тайвань (Китай)', 'TW'), ('Танзания  Объединенная Республика', 'TZ'), ('Тимор-Лесте', 'TL'), ('Того', 'TG'), ('Токелау', 'TK'), ('Тонга', 'TO'), ('Тринидад и Тобаго', 'TT'), ('Тувалу', 'TV'), ('Тунис', 'TN'), ('Туркмения', 'TM'), ('Турция', 'TR'), ('Уганда', 'UG'), ('Узбекистан', 'UZ'), ('Украина', 'UA'), ('Уоллис и Футуна', 'WF'), ('Уругвай', 'UY'), ('Фарерские острова', 'FO'), ('Фиджи', 'FJ'), ('Филиппины', 'PH'), ('Финляндия', 'FI'), ('Фолклендские острова (Мальвинские)', 'FK'), ('Франция', 'FR'), ('Французская Гвиана', 'GF'), ('Французская Полинезия', 'PF'), ('Французские Южные территории', 'TF'), ('Хорватия', 'HR'), ('Центрально-Африканская Республика', 'CF'), ('Чад', 'TD'), ('Черногория', 'ME'), ('Чешская Республика', 'CZ'), ('Чили', 'CL'), ('Швейцария', 'CH'), ('Швеция', 'SE'), ('Шпицберген и Ян Майен', 'SJ'), ('Шри-Ланка', 'LK'), ('Эквадор', 'EC'), ('Экваториальная Гвинея', 'GQ'), ('Эландские острова', 'AX'), ('Эль-Сальвадор', 'SV'), ('Эритрея', 'ER'), ('Эстония', 'EE'), ('Эфиопия', 'ET'), ('Южная Африка', 'ZA'), ('Южная Джорджия и Южные Сандвичевы острова', 'GS'), ('Южная Осетия', 'OS'), ('Южный Судан', 'SS'), ('Ямайка', 'JM'), ('Япония', 'JP');");    
    
    CompetitorCountryUpdater();
    CommandUpdate();
    
    DataBaseClass::Query("ALTER TABLE `Event` ADD COLUMN `vRound` varchar(255) AFTER `Round`;");
    DataBaseClass::Query("ALTER TABLE `Attempt` ADD COLUMN `vOrder` bigint AFTER `Special`, ADD COLUMN `vOut` varchar(255) AFTER `vOrder`;");
    
    AttemptUpdater();
    
    DataBaseClass::Query("ALTER TABLE `Attempt` CHANGE COLUMN `CompetitorEvent` `Command` int(11) NOT NULL;");
    
    DataBaseClass::Query("UPDATE Command SET Secret=concat(
              substring('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand(@seed:=round(rand(@lid)*4294967296))*36+1, 1),
              substring('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand(@seed:=round(rand(@seed)*4294967296))*36+1, 1),
              substring('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand(@seed:=round(rand(@seed)*4294967296))*36+1, 1),
              substring('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand(@seed:=round(rand(@seed)*4294967296))*36+1, 1),
              substring('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand(@seed:=round(rand(@seed)*4294967296))*36+1, 1),
              substring('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand(@seed:=round(rand(@seed)*4294967296))*36+1, 1),
              substring('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand(@seed:=round(rand(@seed)*4294967296))*36+1, 1),
              substring('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', rand(@seed)*36+1, 1)
             )");
    
    DataBaseClass::Query("update `Event` set Round=1");
    DataBaseClass::Query("update `Event` set Discipline=24, Round=2 where Discipline=43");
    DataBaseClass::Query("Delete from `Discipline`where ID=43");
    EventRoundView();
    
    DataBaseClass::Query("ALTER DATABASE suphair_funcubing CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;");
    DataBaseClass::Query("ALTER TABLE Competitor CHANGE Name Name VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    DataBaseClass::Query("ALTER TABLE `Command` DROP FOREIGN KEY `command_ibfk_1`;");
    DataBaseClass::Query("ALTER TABLE `Command` DROP COLUMN `Competitor`, DROP INDEX `Competitor_2`, DROP INDEX `Competitor`;");
    DataBaseClass::Query("delete  from Competitor where Name like '%&%'");
    DataBaseClass::Query("drop table CompetitorEventWCA ");
    DataBaseClass::Query("drop table CompetitorWCA ");
}