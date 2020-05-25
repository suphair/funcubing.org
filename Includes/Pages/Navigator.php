    <?php if(!isset($type)){
        $type='';
    } ?>
    
    <?php if($Section=='UnofficialEvents'){ ?>
<h2 class="Navigator">
        <nobr><a <?= ($type=='Competitions')?"class='select'":"" ?> href="<?= PageIndex() ?>?Competitions">Competitions</a> <span style="color:var(--back_color)">|</span></nobr>
        <nobr><a <?= ($type=='Events')?"class='select'":"" ?>  href="<?= PageIndex() ?>?Events">Events</a> <span style="color:var(--back_color)">|</span></nobr>
        <nobr><a <?= ($type=='Judges')?"class='select'":"" ?>  href="<?= PageIndex() ?>?Judges">Judges</a> <span style="color:var(--back_color)">|</span></nobr>
        <nobr><a <?= ($type=='Records')?"class='select'":"" ?>  href="<?= PageIndex() ?>?Records">Records</a> <span style="color:var(--back_color)">|</span></nobr>
        <nobr><a <?= ($type=='Competitors')?"class='select'":"" ?>  href="<?= PageIndex() ?>?Competitors">Competitors</a> <span style="color:var(--back_color)">|</span></nobr>
        <nobr><a <?= ($type=='Regulations')?"class='select'":"" ?>  href="<?= PageIndex() ?>?Regulations">Regulations</a> </nobr>
</h2>
<hr>
    <?php } ?>
