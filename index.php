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

$Section = 'UnofficialEvents';

$isMeeting = false;
if (isset($_GET['Meetings']) or ( isset(getRequest()[0]) and getRequest()[0] == 'Meetings')) {
    $Section = 'UnofficialCompetitions';
}
$isGoal = false;
if (isset($_GET['CompetitionGoals']) or ( isset(getRequest()[0]) and getRequest()[0] == 'CompetitionGoals')) {
    $Section = 'CompetitionGoals';
}
$isMosaic = false;
if (isset($_GET['MosaicBuilding']) or ( isset(getRequest()[0]) and getRequest()[0] == 'MosaicBuilding')) {
    $Section = 'MosaicBuilding';
}

$isFriendsСompetitions = false;
if (isset($_GET['FriendsCompetitions']) or ( isset(getRequest()[0]) and getRequest()[0] == 'FriendsCompetitions')) {
    $Section = 'FriendsCompetitions';
}

$isMailUpcomingCompetition = false;
if ((isset(getRequest()[0]) and getRequest()[0] == 'MailUpcomingCompetition')) {
    $Section = 'MailUpcomingCompetition';
}

$isAllProjects = false;
if (isset($_GET['AllProjects']) or ( sizeof($_GET) == 0 and $Section == 'UnofficialEvents')) {
    $Section = 'AllProjects';
}

$sectionData = arrayToObject([
    'UnofficialCompetitions' => [
        'logo' => 'Logo_Color_UC',
        'title' => 'Unofficial Competitions',
        'link' => '?Meetings',
        'descrption' => 'For WCA events at unofficial competitions. Any speedcuber can register a competition.'
    ],
    'CompetitionGoals' => [
        'logo' => 'Logo_Color_GC',
        'title' => 'Competition Goals',
        'link' => '?CompetitionGoals',
        'descrption' => 'To set personal goals for official disciplines in official competitions.'
    ],
    'MosaicBuilding' => [
        'logo' => 'Logo_Color_MB',
        'title' => 'Mosaic Building',
        'link' => 'MosaicBuilding',
        'descrption' => 'Upload the image. Get the PDF. Create picture.'
    ],
    'FriendsCompetitions' => [
        'logo' => 'Logo_Color_FC',
        'title' => 'Friends\' Competitions',
        'link' => 'FriendsCompetitions',
        'descrption' => 'Shows the competitions your friends have registered for.'
    ],
    'MailUpcomingCompetition' => [
        'logo' => 'Logo_Color_SA',
        'title' => 'Competitions\' Announcements',
        'link' => 'MailUpcomingCompetition',
        'descrption' => 'Subscription to announcements of WCA competitions.'
    ],
        ]);
?>
<!DOCTYPE HTML>
<html  lang="en">
    <head>
        <meta name="Description" content="Fun Cubing">

        <?php if (isset($sectionData->$Section)) { ?>
            <title><?= $sectionData->$Section->title ?></title>
            <link rel="icon" href="<?= PageLocal() ?>Logo/<?= $sectionData->$Section->logo ?>.png" >
        <?php } else { ?>
            <title><?= RequestClass::getTitle(); ?></title>
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
        <?php
        $Competitor = GetCompetitorData();
        ?>
    </head>
    <body>
        <table class="title">
            <tbody><tr>
                    <td class="logo">
                        <img src="<?= PageIndex() ?>Logo/FC_Main.png">  
                    </td>
                    <td class="header">
                        <a href="<?= PageIndex(); ?>">Fun Cubing</a>    
                    </td>
                    <?php if (isset($sectionData->$Section)) { ?>
                        <td class="logo">
                            <img src="<?= PageIndex() ?>Logo/<?= $sectionData->$Section->logo ?>.png">  
                        </td>
                        <td class="header">
                            <a href="<?= PageIndex(); ?><?= $sectionData->$Section->link ?>"><?= $sectionData->$Section->title ?></a>
                        </td>
                    <?php } ?>     
                </tr>
            </tbody>
        </table>
        <p style="padding:10px 0px">
            <?php Include ("Includes/Pages/Login.php"); ?>
        </p>

        <?php Include ("Includes/Pages/" . RequestClass::getPage() . ".php"); ?>

        <p style="padding:10px 0px">
            Support 
            <i class="far fa-envelope"></i>
            <a href="mailto:suphair@gmail.com?subject=funcubung.org">suphair@gmail.com</a>
        </p>
    </body>
</html>    