<div class="shadow" >
    <h2>
        Subscription to announcements of WCA competitions
    </h2>
    <p style="padding: 10px 0px;">
        When WCA competitions for tracked countries are announced on the WCA website, you will receive an email
    </p>
    <?php
    $me = wcaoauth::me();
    if ($me) {
        include 'authorized.php';
    } else {
        include 'unauthorized.php';
    }
    ?>
    <?php $row = db::row("SELECT count(*) count FROM `announcements` WHERE Status = 1"); ?>
</div>
<p>
    <i class="fas fa-info-circle"></i>  
    Total active subscriptions: <?= $row->count ?>
</p>