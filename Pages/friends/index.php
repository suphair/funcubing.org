<?php

$me = wcaoauth::me() ?? FALSE;
if ($me and $me->wca_id) {
    include 'authorized.php';
} else {
    include 'unauthorized.php';
}
?>

<?php $row = db::row("SELECT COUNT(DISTINCT user) count FROM friends"); ?>
<p>
    <i class="fas fa-info-circle"></i>  
    Total competitors with friends: <?= $row->count ?>
</p>
