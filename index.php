<?php
session_start();
ob_start();

foreach (['Classes', 'Functions', 'API'] as $dir) {
    foreach (scandir($dir) as $file) {
        if (strpos($file, ".php") !== FALSE and $file != 'index.php') {
            require_once "$dir/$file";
        }
    }
}
$mobile = (check_mobile_device() or $_SESSION['mobile'] ?? false);
include_once 'vendor/autoload.php';
config::init('Config');
errors::register(config::isLocalhost());

$get = function($instance = false) {
    $keys = ['host', 'username', 'password', 'schema', 'port'];
    foreach ($keys as $key) {
        $values[$key] = config::get('DB' . $instance, $key);
    }
    return $values;
};

db::set($get());
db2::set($get(2));

wcaapi::setConnection(db::connection());

$request_0 = request();
$request_1 = request(1);
$request_2 = request(2);
$request_3 = request(3);
$request_4 = request(4);
$request_0 = str_replace('unofficial', 'competitions', $request_0);
if (!$request_0) {
    $request_0 = 'competitions';
}

if (filter_input(INPUT_GET, 'language')) {
    $_SESSION['user_lang'] = strtoupper(filter_input(INPUT_GET, 'language'));
}

if ($_SESSION['user_lang'] ?? false) {
    $_SESSION['lang'] = $_SESSION['user_lang'];
} else {
    $_SESSION['lang'] = 'RU';
}
if (!in_array($_SESSION['lang'], ['RU', 'EN'])) {
    $_SESSION['lang'] = 'RU';
}

if ($request_0 == 'api') {
    include('API/index.php');
}


if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'POST') {

    $file = "Pages/$request_0/post.php";
    if (file_exists($file)) {
        include $file;
        $query = postGet('query');
        if ($query) {
            header("Location: ?post=$query");
        } else {
            header('Location: ?');
        }
        exit();
    }
    die("Unknown action POST [$request_0]");
}

if (filter_input(INPUT_GET, 'action')) {
    $file = "Pages/$request_0/action.php";
    if (file_exists($file)) {
        include $file;
        exit();
    }
    die("Unknown action GET [$request_0]");
}
if ($request_0 == 'cron') {
    include('Pages/cron/index.php');
}

if ($request_0 == 'template') {
    include('Pages/template/index.php');
}

$title = [
    'competitions' => t('Competitions', 'Соревнования'),
    'goals' => 'Competition Goals',
    'mosaic' => 'Mosaic Building',
    'friends' => 'Friends\' Competitions',
    'announcements' => 'Competitions\' Announcements'
        ][$request_0] ?? false;
?>
<!DOCTYPE HTML>
<html  lang="en">
    <head>
        <!-- <?= config :: info() ?>-->
        <meta name="Description" content="Fun Cubing">
        <?php if ($title) { ?>
            <title><?= $title ?></title>
            <link rel="icon" href="<?= PageLocal() ?>Pages/<?= $request_0 ?>/icon.png" >
        <?php } else { ?>
            <title>Fun Cubing</title>
            <link rel="icon" href="<?= PageLocal() ?>Pages/index.png" >
        <?php } ?>
        <link rel="stylesheet" href="<?= PageLocal() ?>Styles/index.css" type="text/css"/>
        <link rel="stylesheet" href="<?= PageLocal() ?>Styles/fc2205.css" type="text/css"/>
        <link rel="stylesheet" href="<?= PageLocal(); ?>Styles/flag-icon-css/css/flag-icon.css" type="text/css"/>
        <link rel="stylesheet" href="<?= PageIndex(); ?>Styles/fontawesome-free-5.13.0-web/css/all.css" type="text/css"/>
        <link rel="stylesheet" href="<?= PageIndex(); ?>Styles/icons-extra-event/css/Extra-Events.css" type="text/css"/>
        <link rel="stylesheet" href="<?= PageLocal() ?>jQuery/chosen_v1/chosen.css" type="text/css"/>
        <script src="<?= PageLocal() ?>jQuery/jquery-3.4.1.min.js" type="text/javascript"></script>
        <script src="<?= PageLocal() ?>jQuery/chosen_v1/chosen.jquery.js?2" type="text/javascript"></script>
        <script src="<?= PageLocal() ?>jQuery/tooltip.js?2" type="text/javascript"></script>

    </head>
    <body> 
        <div class="wrapper">
            <div class="content">
                <table class="title">
                    <tbody>
                        <tr>
                            <td class="header" width="50%" style="white-space: nowrap">
                                <?php if ($title) { ?>
                                    <a href="<?= PageIndex() ?><?= $request_0 ?>"><?= $title ?></a>
                                    <span hidden id="sub_navigation_separator">&bull;</span>
                                    <span id="sub_navigation"></span>
                                <?php } ?>
                            </td>
                            <td valign="center" align="right" width="100%">
                                <?php include('Pages/competitor/block.index.php'); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <?php
                if (!$request_0) {
                    include 'Pages/index.php';
                } elseif (in_array($request_0, ['friends', 'goals', 'announcements'])) {
                    ?>
                    <h1 style="color: red">
                        The <b><?= $title ?></b> section disabled until the WCA returns the events to Russia.
                    </h1>
                    <?php
                } else {
                    $file = "Pages/$request_0/index";
                    if (file_exists("$file.php")) {
                        include "$file.php";
                    } else {
                        ?>
                        <div class='shadow2 error'>
                            <i class='far fa-hand-paper'></i>
                            <?= "Wrong page [$request_0]" ?>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
            <div class="footer">
                <hr>
                <p align='center'>
                    <a href="mailto:<?= config::get('Support', 'email') ?>?subject=funcubung.org">
                        <i class="far fa-envelope"></i>
                        <?= config::get('Support', 'email') ?></a>
                    <a target="_blank" href="https://github.com/suphair/funcubing.org">
                        <i class="fab fa-github"></i>
                        GitHub</a>
                    <a  target="_blank" href="https://www.worldcubeassociation.org/persons/2015SOLO01">
                        <i class="fas fa-laptop-code"></i> 
                        Konstantin Solovev (Константин Соловьёв)</a>
                    <i class="fas fa-user-circle"></i>
                    <?= get_count_visitors_day(); ?> visitors today 

                    <a target="_blank" href="<?= PageIndex() ?>api" title="Api Information"> 
                        <i class="fas fa-wrench"></i>
                        Api Information
                    </a> 
                </p>
            </div>
        </div>
    </body>
</html>
<?php count_visitors(); ?>