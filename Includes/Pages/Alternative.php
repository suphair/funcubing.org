<div class="form">
    <form name="LoginAlternative"  method="POST" action="<?= PageIndex()."Actions/LoginAlternative" ?>">           
        <input required name="Secret" placeholder="Enter your secret" />
        <input name="LoginAlternative" type="submit" value="Go" />
        <p><?= GetMessage('Alternative'); ?></p>
    </form> 
</div> 