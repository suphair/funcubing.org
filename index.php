<?php
session_start();

foreach (['Classes', 'Functions'] as $dir) {
    foreach (scandir($dir) as $file) {
        if (strpos($file, ".php") !== FALSE) {
            require_once "$dir/$file";
        }
    }
}

config::init('Config');
errors::register(config::isLocalhost());

$get = function($key) {
    return config::get('DB', $key);
};
db::set($get('host'), $get('username'), $get('password'), $get('schema'), $get('port'));

wcaapi::setConnection(db::connection());


if (FALSE and ( wcaoauth::me()->wca_id ?? FALSE) == config::get('Admin', 'wcaid')) {

    db::exec("UPDATE unofficial_competitors_result set average = null WHERE average=''");
    db::exec("UPDATE unofficial_competitors_result set mean = null WHERE mean=''");
    db::exec("UPDATE unofficial_competitors_result set average='dnf' WHERE average='DNF'");
    db::exec("UPDATE unofficial_competitors_result set best='dnf' WHERE best='DNF'");
    db::exec("UPDATE unofficial_competitors_result set mean='dnf' WHERE mean='DNF'");
    db::exec("UPDATE unofficial_competitors_result set average='dns' WHERE average='DNS'");
    db::exec("UPDATE unofficial_competitors_result set best='dns' WHERE best='DNS'");
    db::exec("UPDATE unofficial_competitors_result set attempt1='dnf' WHERE attempt1='DNF'");
    db::exec("UPDATE unofficial_competitors_result set attempt2='dnf' WHERE attempt2='DNF'");
    db::exec("UPDATE unofficial_competitors_result set attempt3='dnf' WHERE attempt3='DNF'");
    db::exec("UPDATE unofficial_competitors_result set attempt4='dnf' WHERE attempt4='DNF'");
    db::exec("UPDATE unofficial_competitors_result set attempt5='dnf' WHERE attempt5='DNF'");
    db::exec("UPDATE unofficial_competitors_result set attempt1='dns' WHERE attempt1='DNS'");
    db::exec("UPDATE unofficial_competitors_result set attempt2='dns' WHERE attempt2='DNS'");
    db::exec("UPDATE unofficial_competitors_result set attempt3='dns' WHERE attempt3='DNS'");
    db::exec("UPDATE unofficial_competitors_result set attempt4='dns' WHERE attempt4='DNS'");
    db::exec("UPDATE unofficial_competitors_result set attempt5='dns' WHERE attempt5='DNS'");
    foreach (db::rows("SELECT competitor_round,average,mean,best FROM unofficial_competitors_result") as $result) {
        $order = 0;
        $order_best = 0;
        $order_average = 0;
        $order = 0;
        $order += (10000000 * unofficial\attempt_to_int($result->average ?? 0));
        $order += (10000000 * unofficial\attempt_to_int($result->mean ?? 0));
        $order += unofficial\attempt_to_int($result->best ?? 0);
        $order_best = unofficial\attempt_to_int($result->best ?? 0);
        $order_average += unofficial\attempt_to_int($result->average ?? 0);
        $order_average += unofficial\attempt_to_int($result->mean ?? 0);
        db::exec("UPDATE unofficial_competitors_result "
                . "SET `order` = '$order', order_best='$order_best', order_average='$order_average' "
                . "WHERE competitor_round = $result->competitor_round");
    }
    echo 'order updated';

    $results = db::rows("SELECT unofficial_competitors_result.`order`,"
                    . " unofficial_competitors_result.competitor_round,"
                    . "unofficial_events_rounds.id "
                    . " FROM unofficial_competitors_round"
                    . " JOIN unofficial_events_rounds on unofficial_events_rounds.id = unofficial_competitors_round.round"
                    . " JOIN unofficial_competitors_result on unofficial_competitors_result.competitor_round = unofficial_competitors_round.id"
                    . " JOIN unofficial_events on unofficial_events.id = unofficial_events_rounds.event"
                    . " JOIN unofficial_events_dict ON unofficial_events_dict.id = unofficial_events.event_dict"
                    . " ORDER BY unofficial_events_rounds.id, `order`");



    $prev_id = 0;
    foreach ($results as $result) {
        if ($prev_id != $result->id) {
            $order_current = 0;
            $place_current = 0;
            $prev_id = $result->id;
        }

        if ($result->order > $order_current) {
            $order_current = $result->order;
            $place_current++;
        }
        db::exec("UPDATE unofficial_competitors_result "
                . "SET `place` = '$place_current'"
                . "WHERE competitor_round = $result->competitor_round");
    }
    echo 'place updated';
}


$request_0 = request();
$request_1 = request(1);

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
    'unofficial' => 'Unofficial Competitions',
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
        <link rel="stylesheet" href="<?= PageLocal(); ?>Styles/flag-icon-css/css/flag-icon.css" type="text/css"/>
        <link rel="stylesheet" href="<?= PageIndex(); ?>Styles/fontawesome-free-5.13.0-web/css/all.css" type="text/css"/>
        <link rel="stylesheet" href="<?= PageLocal() ?>jQuery/chosen_v1/chosen.css" type="text/css"/>
        <script src="<?= PageLocal() ?>jQuery/jquery-3.4.1.min.js" type="text/javascript"></script>
        <script src="<?= PageLocal() ?>jQuery/chosen_v1/chosen.jquery.js?2" type="text/javascript"></script>
        <script src="<?= PageLocal() ?>jQuery/tooltip.js?2" type="text/javascript"></script>

    </head>
    <body>     
        <table class="title">
            <tbody><tr>

                    <?php if ($title) { ?>
                        <td class="header">
                            <a href="<?= PageIndex() ?>">FC</a>    
                        </td>
                        <td class="logo">
                            <img src="<?= PageIndex() ?>Pages/<?= $request_0 ?>/icon.png">  
                        </td>
                        <td class="header">
                            <a href="<?= PageIndex() ?><?= $request_0 ?>">
                                <?= $title ?>
                            </a>
                        </td>
                    <?php } else { ?>
                        <td class="logo">
                            <img src="<?= PageIndex() ?>Pages/index.png">  
                        </td>
                        <td class="header">
                            <a href="<?= PageIndex() ?>">Fun Cubing</a>    
                        </td>
                    <?php } ?>
                </tr>
            </tbody>
        </table>
        <p style="padding:10px 0px">
            <?php include('Pages/competitor/block.index.php'); ?>
        </p>
        <?php
        if (!$request_0) {
            include 'Pages/index.php';
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
        <p align='center'>
            <a href="mailto:<?= config::get('Support', 'email') ?>?subject=funcubung.org">
                <i class="far fa-envelope"></i>
                <?= config::get('Support', 'email') ?></a>
            <a target="_blank" href="https://github.com/suphair/funcubing.org">
                <i class="fab fa-github"></i>
                GitHub</a>
            <a  target="_blank" href="https://www.worldcubeassociation.org/persons/2015SOLO01">
                <i class="fas fa-laptop-code"></i> Solovyov Konstantin</a>
        </p>
    </body>
</html>