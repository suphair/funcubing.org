<?php include 'Delegates.php'; ?>    
<h1>Add judge</h1>

<div class="form">
    <form method="POST" action="<?= PageIndex()."Actions/DelegateCreate" ?>">
    <div class="form_field">
        WCA ID 
    </div>
    <div class="form_input">
        <input required type="text" name="WCAID" value="" />
    </div>
    <div class="form_enter">
        <input type="submit" value="Create">
    </div>
    </form>
</div>
