<?php
require_once '../errorsClass.php';

errors::done(filter_input(INPUT_GET, 'done', FILTER_VALIDATE_INT));
errors::skip(filter_input(INPUT_GET, 'skip', FILTER_VALIDATE_INT));
errors::work(filter_input(INPUT_GET, 'work', FILTER_VALIDATE_INT));

$mode = 'new';
if (filter_input(INPUT_GET, 'view') == 'all') {
    $errors = errors::getAll();
    $mode = 'all';
} else {
    $errorsNew = errors::getNew();
    $errorsWork = errors::getWork();
    $errors = $errorsNew + $errorsWork;
    ksort($errors);
}
?>
<h1>Error <?= errors::VERSION ?></h1> 
<h2>[PHP_AUTH_USER: <?= $_SERVER['PHP_AUTH_USER'] ?>]</h2>
<h3><?= $mode == 'all' ? 'All' : 'New' ?></h3>
<?php if ($mode == 'all') { ?>
    <a href="?view=new">view new</a>
<?php } else { ?>
    <?php if (sizeof($errors)) { ?>
        <a href="?skip=<?= max(array_keys($errors)) ?>">
            skip to #<?= max(array_keys($errors)) ?>
        </a>
        <br>
        <br>
    <?php } ?>
    <a href="?view=all">view all</a>
<?php } ?>
<br>
<br>
<?php
$number = 0;
foreach ($errors as $number => $error) {
    ?>
    <div style="width: 300px; display: inline-block;">
        <a target="_blank" href="<?= $error['file'] ?>">
            #<?= $number ?>
            <?= $error['err'] ?>
            <?= date("Y-m-d H:i:s", $error['time']) ?>
        </a>
    </div>
    <span style="padding-left:300px">
        <b><?= $error['status'] ?></b>
    </span>

    <?php $new = errors::_NEW; ?>
    <?php $done = errors::_DONE; ?>
    <?php $work = errors::_WORK; ?>
    <?php $skip = errors::_SKIP; ?>

    <span style="padding-left:300px"> </span>
    <?php if (in_array($error['status'], [$new, $skip, $done])) { ?>
        <a href="?work=<?= $number ?>">work #<?= $number ?></a>
    <?php } ?> 
    <span style="padding-left:300px"> </span>    
    <?php if (in_array($error['status'], [$new, $work])) { ?>
        <a href="?done=<?= $number ?>">done #<?= $number ?></a>    
    <?php } ?>
    </br>
    </br>    
<?php } ?>

