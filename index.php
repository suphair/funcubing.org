<?php
session_start();

require_once "file_utils.php";
RequireDir("Classes");
RequireDir("Functions");
Suphair \ Config :: init('Config');
Suphair \ Error :: register(Suphair \ Config :: isLocalhost());
DataBaseInit();
Suphair \ Wca \ Api :: setConnection(DataBaseClass::getConection());
IncluderAction();
RequestClass::setRequest();
$Section = 'UnofficialEvents';

$isMeeting = false;
if (isset($_GET['Meetings']) or ( isset(getRequest()[0]) and getRequest()[0] == 'Meetings')) {
    $Section = 'UnofficialCompetitions';
}

$isGoal = false;
if (isset(getRequest()[0]) and getRequest()[0] == 'Goals') {
    $Section = 'Goals';
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
    'Goals' => [
        'logo' => 'Logo_Color_GC',
        'title' => 'Competition Goals',
        'link' => 'Goals',
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
        <!-- <?= Suphair \ Config :: info() ?>-->
        <meta name="Description" content="Fun Cubing">
        <script src="https://kit.fontawesome.com/<?= Suphair \ Config :: get('Keys', 'fontawesome') ?>.js" crossorigin="anonymous"></script>

        <?php if (isset($sectionData->$Section)) { ?>
            <title><?= $sectionData->$Section->title ?></title>
            <link rel="icon" href="<?= PageLocal() ?>Logo/<?= $sectionData->$Section->logo ?>.png" >
        <?php } else { ?>
            <title><?= RequestClass::getTitle(); ?></title>
            <link rel="icon" href="<?= PageLocal() ?>Logo/FC_Main.png" >
        <?php } ?>

        <link rel="stylesheet" href="<?= PageLocal() ?>style_design.css?t=27d" type="text/css"/>
        <link rel="stylesheet" href="<?= PageLocal() ?>jQuery/chosen_v1/chosen.css" type="text/css"/>
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
            <a style="padding:0px 0px 10px 0px" href="mailto:suphair@gmail.com?subject=funcubung.org">
                <i class="far fa-envelope"></i>
                suphair@gmail.com
            </a>
            <a style="padding:0px 0px 10px 0px"  target="_blank" href="https://github.com/suphair/funcubing.org">
                <i class="fab fa-github"></i>
                GitHub
            </a>
        </p>
    </body>
</html>    