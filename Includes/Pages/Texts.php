<?php if(CheckAdmin()){?>
    <h1>Texts Setting</h1>
    <div class="content">
        <?php DataBaseClass::FromTable("BlockText"); 
        foreach(DataBaseClass::QueryGenerate() as $block){ ?>
            <div class="form">
                <b><?= $block['BlockText_Name'] ?> <?= $block['BlockText_Country'] ?></b><br>
                <?= Echo_format($block['BlockText_Value']); ?><br>
                <form method="POST" action="<?= PageIndex() ?>Actions/BlockTextSave">
                    <input name="Country" type="hidden" value="<?= $block['BlockText_Country'] ?>">
                    <input name="Name" type="hidden" value="<?= $block['BlockText_Name'] ?>">
                    <textarea name="Comment" style="height: 200px;width: 200px"><?= $block['BlockText_Value'] ?></textarea><br>
                    <input type="submit" name="submit" value="Save <?= $block['BlockText_Name'] ?>">
                </form>
            </div>
        <?php } ?>
    </div>    
    
    
<?php }else{ ?>
    access denied
<?php }?>