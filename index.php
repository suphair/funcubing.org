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