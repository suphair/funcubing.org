<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

CONST CUBE_AMOUNT = 300;
CONST STEPS = 5;

@require( 'Classes/fpdf17/fpdf.php' );
define('FPDF_FONTPATH', 'Classes/fpdf17/font');

require_once "file_utils.php";
RequireDir("Classes");
RequireDir("Functions");
DataBaseInit();
IncluderAction();

RequestClass::setRequest();

if (RequestClass::getError(404)) {
    header('HTTP/1.0 404 not found');
}

if (RequestClass::getError(401)) {
    header('HTTP/1.1 401 Unauthorized');
}
/*
  echo '8x8x8 ';
  echo '<br>';
  $Scr8= Generate8x8x8();
  echo $Scr8."<br>";
  $Scr8s=explode(" ",$Scr8);
  for($i=0;$i<10;$i++){
  for($j=0;$j<10;$j++){
  echo $Scr8s[10*$i+$j]." / ";
  }
  echo '<br>';
  }
  echo '<br>';
  exit();

 */


$Section = 'UnofficialEvents';

$isMeeting = false;
if (isset($_GET['Meetings']) or ( isset(getRequest()[0]) and getRequest()[0] == 'Meetings')) {
    $Section = 'UnofficialCompetitions';
}
$isGoal = false;
if (isset($_GET['CompetitionGoals']) or ( isset(getRequest()[0]) and getRequest()[0] == 'CompetitionGoals')) {
    $Section = 'CompetitionGoals';
}

$isAchievement = false;
if (isset($_GET['Achievements']) or ( isset(getRequest()[0]) and getRequest()[0] == 'Achievements')) {
    $Section = 'Achievements';
}

$isMosaic = false;
if (isset($_GET['MosaicBuilding']) or ( isset(getRequest()[0]) and getRequest()[0] == 'MosaicBuilding')) {
    $Section = 'MosaicBuilding';
}

$isFriendsСompetitions = false;
if (isset($_GET['FriendsCompetitions']) or ( isset(getRequest()[0]) and getRequest()[0] == 'FriendsCompetitions')) {
    $Section = 'FriendsCompetitions';
}

$isSchedule = false;
if (isset($_GET['Schedule']) or ( isset(getRequest()[0]) and getRequest()[0] == 'Schedule')) {
    $Section = 'Schedule';
}

$isWCA_api_v0 = false;
if (isset($_GET['WCA_api_v0']) or ( isset(getRequest()[0]) and getRequest()[0] == 'WCA_api_v0')) {
    $Section = 'WCA_api_v0';
}

$isMailUpcomingCompetition = false;
if ((isset(getRequest()[0]) and getRequest()[0] == 'MailUpcomingCompetition')) {
    $Section = 'MailUpcomingCompetition';
}

$isVisiters = false;
if ((isset(getRequest()[0]) and getRequest()[0] == 'Visiters')) {
    $Section = 'Visiters';
}

$isAllProjects = false;
if (isset($_GET['AllProjects']) or ( sizeof($_GET) == 0 and $Section == 'UnofficialEvents')) {
    $Section = 'AllProjects';
}
?>
<!DOCTYPE HTML>
<html  lang="en">
    <head>
        <meta name="Description" content="Fun Cubing">
        <title><?= RequestClass::getTitle(); ?></title>
        <?php if ($Section == 'UnofficialEvents') { ?>
            <link rel="icon" href="<?= PageLocal() ?>Logo/Logo_Color.png" >
        <?php } ?>
        <?php if ($Section == 'UnofficialCompetitions') { ?>    
            <link rel="icon" href="<?= PageLocal() ?>Logo/Logo_Color_UC.png" >
        <?php } ?>
        <?php if ($Section == 'CompetitionGoals') { ?>    
            <link rel="icon" href="<?= PageLocal() ?>Logo/Logo_Color_GC.png" >
        <?php } ?>
        <?php if ($Section == 'Achievements') { ?>    
            <link rel="icon" href="<?= PageLocal() ?>Logo/Logo_Color_SA.png" >
        <?php } ?>
        <?php if ($Section == 'MosaicBuilding') { ?>    
            <link rel="icon" href="<?= PageLocal() ?>Logo/Logo_Color_MB.png" >
        <?php } ?>
        <?php if ($Section == 'FriendsCompetitions') { ?>    
            <link rel="icon" href="<?= PageLocal() ?>Logo/Logo_Color_FC.png" >
        <?php } ?>
        <?php if ($Section == 'AllProjects') { ?>    
            <link rel="icon" href="<?= PageLocal() ?>Logo/FC_Main.png" >
        <?php } ?>
        <link rel="stylesheet" href="<?= PageLocal() ?>style_design.css?t=27d" type="text/css"/>
        <!--<link rel="stylesheet" href="<?= PageLocal() ?>flags.css?t=bb" type="text/css"/>-->
        <link rel="stylesheet" href="<?= PageLocal() ?>jQuery/chosen_v1/chosen.css" type="text/css"/>

        <meta name="yandex-verification" content="71a3f60630008926" />
            <!-- Yandex.Metrika counter -->
            <link rel="stylesheet" href="<?= PageLocal(); ?>fontawesome-free-5.12.0-web/css/all.css?t=3" type="text/css"/>
            <link rel="stylesheet" href="<?= PageLocal(); ?>flag-icon-css/css/flag-icon.css" type="text/css"/>
            <script src="<?= PageLocal() ?>jQuery/jquery-3.4.1.min.js" type="text/javascript"></script>
            <script src="<?= PageLocal() ?>jQuery/chosen_v1/chosen.jquery.js?2" type="text/javascript"></script>
            <script src="<?= PageLocal() ?>jQuery/tooltip.js?2" type="text/javascript"></script>
            <script type="text/javascript" >
                (function (d, w, c) {
                    (w[c] = w[c] || []).push(function () {
                        try {
                            w.yaCounter48770939 = new Ya.Metrika({
                                id: 48770939,
                                clickmap: true,
                                trackLinks: true,
                                accurateTrackBounce: true
                            });
                        } catch (e) {
                        }
                    });

                    var n = d.getElementsByTagName("script")[0],
                            s = d.createElement("script"),
                            f = function () {
                                n.parentNode.insertBefore(s, n);
                            };
                    s.type = "text/javascript";
                    s.async = true;
                    s.src = "https://mc.yandex.ru/metrika/watch.js";

                    if (w.opera == "[object Opera]") {
                        d.addEventListener("DOMContentLoaded", f, false);
                    } else {
                        f();
                    }
                })(document, window, "yandex_metrika_callbacks");
            </script>
        <noscript><div><img src="https://mc.yandex.ru/watch/48770939" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
        <!-- /Yandex.Metrika counter -->    
    <?php
    $Competitor = GetCompetitorData();
    $Delegate = GetDelegateData();
    $back_color1 = "#ddd";
    $back_color2 = "#888";
    $color = "#ddd";
    if ($Competitor) {
        $color = 'rgb(103,103,103)';
        $back_color1 = '#dfd';
        $back_color2 = '#8f8';
    }
    if ($Delegate) {
        $color = 'rgb(17,31,135)';
        $back_color1 = '#ddf';
        $back_color2 = '#88f';
    }
    if (CheckAdmin()) {
        $color = 'rgb(162,0,0)';
        $back_color1 = '#fdd';
        $back_color2 = '#fcc';
    }

    $color = 'rgb(186,186,186)';
    $back_color1 = 'rgb(225,225,225)';
    $back_color2 = 'rgb(186,186,186)';

    if ($Competitor) {
        $color = 'rgb(162,0,0)';
        $back_color1 = '#fdd';
        $back_color2 = '#fcc';
    }
    ?>
    <style>
        :root{
            --base_color: <?= $color ?>;
            --back_color: <?= $back_color1 ?>;
        }

        body{
            background: linear-gradient(to bottom, <?= $back_color1 ?>,<?= $back_color2 ?>);
        }
    </style>
</head>
<body>

<?php if ($Section == 'AllProjects') { ?>
        <div class="header" style='clear:both; position: relative;'>
            <div style='float: left;'>
                <a href="<?= PageIndex(); ?>">
                    <span class="section_description_1">FunCubing</span><br>
                    <span class="section_description_2">Projects</span>
                </a>
            </div>
<?php } ?>    

<?php if ($Section == 'UnofficialEvents') { ?>
            <div class="header" style='clear:both; position: relative;'>
                <div style='float: left;'>
                    <a href="<?= PageIndex(); ?>">
                        <img title="Unofficial Events"  alt="Unofficial Events" class="logo_select" src="<?= PageIndex() ?>Logo/Full_En_Color.png">  
                    </a>
                </div>
<?php } ?>        

<?php if ($Section == 'UnofficialCompetitions') { ?>
                <div class="header" style='clear:both; position: relative;'>
                    <div style='float: left;'>
                        <a href="<?= PageIndex(); ?>?Meetings">
                            <img title="Unofficial Competitions" alt="Unofficial Competitions" class="logo_select" src="<?= PageIndex() ?>Logo/Full_En_Color_UC.png">  
                        </a>
                    </div>
<?php } ?>

<?php if ($Section == 'CompetitionGoals') { ?>
                    <div class="header" style='clear:both; position: relative;'>
                        <div style='float: left;'>
                            <a href="<?= PageIndex(); ?>?CompetitionGoals">
                                <img title="Competition Goals" alt="Competition Goals" class="logo_select" src="<?= PageIndex() ?>Logo/Full_En_Color_GC.png">  
                            </a>
                        </div>
<?php } ?>  

<?php if ($Section == 'Achievements') { ?>
                        <div class="header" style='clear:both; position: relative;'>
                            <div style='float: left;'>
                                <a href="<?= PageIndex(); ?>Achievements">
                                    <img title="Speedcuber's Achievements" alt="Speedcuber's Achievements" class="logo_select" src="<?= PageIndex() ?>Logo/Full_En_Color_SA.png">  
                                </a>
                            </div>
<?php } ?>  

<?php if ($Section == 'MosaicBuilding') { ?>
                            <div class="header" style='clear:both; position: relative;'>
                                <div style='float: left;'>
                                    <a href="<?= PageIndex(); ?>MosaicBuilding">
                                        <img title="Mosaic Building" alt="Mosaic Building" class="logo_select" src="<?= PageIndex() ?>Logo/Full_En_Color_MB.png">  
                                    </a>
                                </div>
<?php } ?>  

<?php if ($Section == 'FriendsCompetitions') { ?>
                                <div class="header" style='clear:both; position: relative;'>
                                    <div style='float: left;'>
                                        <a href="<?= PageIndex(); ?>FriendsCompetitions">
                                            <img title="Friends Competitions" alt="Friends Competitions" class="logo_select" src="<?= PageIndex() ?>Logo/Full_En_Color_FC.png">  
                                        </a>
                                    </div>
<?php } ?>  

<?php if ($Section == 'MailUpcomingCompetition') { ?>
                                    <div class="header" style='clear:both; position: relative;'>
                                        <div style='float: left;'>
                                            <a href="<?= PageIndex(); ?>MailUpcomingCompetition">
                                                <span class="section_description_1">Competitions'</span>
                                                <br>
                                                <span class="section_description_2">Announcements</span>
                                            </a>
                                        </div>
                                    <?php } ?>  


<?php if ($Section != 'AllProjects') { ?>       
                                        <div style='float: right;'  >
                                            <a  href="<?= PageIndex(); ?>?AllProjects">
                                                <span class="section_description_1">FunCubing</span><br>
                                                <span class="section_description_2">Projects</span>
                                            </a>
                                        </div>
                                    <?php } ?>  
                                </div>
                                <?php if ($Section != 'MosaicBuilding') { ?>
                                    <div class="content">    
                                        <?php Include ("Includes/Pages/Login.php"); ?>
                                    </div>
<?php } ?>
                                <div class="content">
                                    <?php if ($Section == 'UnofficialEvents') { ?>
                                        <h1 class="error">
                                            Unofficial Events move to <a href='https://SpeedcubingExtraEvents.org'>SpeedcubingExtraEvents.org</org>
                                        </h1>
<?php } else { ?>
                                    <?php Include ("Includes/Pages/" . RequestClass::getPage() . ".php"); ?>
                                <?php } ?>
                                </div>    

                                <?php
                                if ($Section == 'UnofficialEvents' and false) {
                                    $actions = array();
                                    $actions[] = array('grand' => array("SJ"), 'action' => "<a href='" . PageIndex() . "?Logs'>Logs</a>");
                                    $actions[] = array('grand' => array("J"), 'action' => "<a href='" . PageIndex() . "?Visiters'>Visiters</a>");
                                    $actions[] = array('grand' => array("SJ"), 'action' => "<a href='" . PageIndex() . "?Texts'>Texts</a>");
                                    $actions[] = array('grand' => array("J"), 'action' => "<a href='" . LinkCompetition('Add') . "'>" . svg_blue(10, "Add competition") . "Add competition</a>");
                                    $actions[] = array('grand' => array("SJ"), 'action' => "<a href='" . LinkDiscipline("Add") . "'>" . svg_blue(10, "Add event") . "Add event</a>");
                                    $actions[] = array('grand' => array("SJ"), 'action' => "<a href='" . LinkDelegateAdd("Add") . "'>" . svg_blue(10, "Add judge") . "Add judge</a>");
                                    $actions[] = array('grand' => array("J"), 'action' => "<a href='" . PageIndex() . "RequestCandidate'>Requests to judges</a>");

                                    $grands = array();
                                    if (CheckAdmin()) {
                                        $grands[] = 'SJ';
                                    }
                                    if ($Delegate and ! $Delegate['Delegate_Candidate']) {
                                        $grands[] = 'J';
                                    }
                                    $actions_grand = array();

                                    foreach ($actions as $a => $action) {
                                        foreach ($grands as $grand) {
                                            if (in_array($grand, $action['grand'])) {
                                                $actions_grand[$a] = $action['action'];
                                            }
                                        }
                                    }

                                    if (!empty($actions_grand)) {
                                        ?>
                                        <div class="content"> 
        <?= implode(" &#9642; ", $actions_grand); ?>
                                        </div>
                                        <?php }
                                    }
                                    ?>


                                <div class="content">    
<?php if ($Section == 'UnofficialEvents' and false) { ?>
                                        Email senior Judges: <a href="mailto:<?= urlencode(getini('Seniors', 'email')) ?>?subject=Unofficial Events"><?= getini('Seniors', 'email') ?></a>
                                <?php } else { ?>
                                        Support: <a href="mailto:suphair@gmail.com?subject=Unofficial Events">suphair@gmail.com</a>
                                <?php } ?>

            <!--&#9642; <a href="<?= PageIndex() ?>Alternative">Alternative auth</a>-->
                                </div>         

                                    <?php add_visit(); ?>
                                    <?php if ($Section == 'FriendsCompetitions' and CheckAdmin()) { ?>
                                    <div class="content">            
                                        Friends Competitions 
    <?php DataBaseClass::Query("Select count(distinct CompetitorWCAID) count from `Friend`");
    $competitor_count = DataBaseClass::getRow()['count']
    ?>
                                        [competitors <?= $competitor_count ?>]
    <?php DataBaseClass::Query("Select count(distinct FriendWCAID) count from `Friend`");
    $friend_count = DataBaseClass::getRow()['count']
    ?>
                                        [friends <?= $friend_count ?>]
                                        [f/c <?= round($friend_count / $competitor_count, 1) ?>]
                                    </div>
<?php } ?>

                                <script>
                                    $(".chosen-select-1").chosen({max_selected_options: 1});
                                    $(".chosen-select-2").chosen({max_selected_options: 2});
                                    $(".chosen-select-3").chosen({max_selected_options: 3});
                                    $(".chosen-select-4").chosen({max_selected_options: 4});
                                </script>
                                <script src="<?= PageLocal() ?>jQuery/chosen_v1/docsupport/init.js" type="text/javascript" charset="utf-8"></script>
                                </body>
                                </html>    