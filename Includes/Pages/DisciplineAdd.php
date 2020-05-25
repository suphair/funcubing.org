<h1>Add event</h1>

<div class="wrapper">
    <div class="form">
        <form method="POST" action="<?= PageIndex()."Actions/DisciplineCreate" ?>">
        <div class="form_field">
            Name 
        </div>
        <div class="form_input">
            <input type="text" name="Name" value="" />
        </div>
        <div class="form_field">
            Code
        </div>
        <div class="form_input">
            <input type="text" name="Code" value="" />
        </div>
        <div class="form_enter">
            <input type="submit" value="Create">
        </div>
        </form>
    </div>
</div>
