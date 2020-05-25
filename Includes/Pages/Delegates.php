<h1>Judges</h1>

<table class="Competitions">
    <tr>
    <?php if(CheckAdmin()){ ?> 
        <td>
            <a href="<?= LinkDelegateAdd() ?>"><?= svg_blue(10,"Add judge")?>Add Judge </a>
        </td>    
    <?php } ?>
    </tr>        
    <?php 
    DataBaseClass::FromTable("Delegate");
    DataBaseClass::OrderClear("Delegate","Status");
    DataBaseClass::Order("Delegate","Name");
            foreach(DataBaseClass::QueryGenerate() as $delegate){ ?>
    <tr>
        <td>
            <a class="link <?= $delegate['Delegate_Status']!='Active'?"archive":''?>" href="<?= LinkDelegate($delegate['Delegate_WCA_ID'])?>">
                <?= Short_Name($delegate['Delegate_Name']) ?></a> 
        </td>    
        <td>
            <?php if($delegate['Delegate_Status']!='Active'){ ?>
                <span class='error'>Archive</span>
            <?php }elseif($delegate['Delegate_Admin']){ ?>
                <span class='message'>Senior Judge</span>
            <?php }elseif($delegate['Delegate_Candidate']){ ?>
                Junior Judge
            <?php }else{ ?>
                Middle Judge
            <?php } ?>
        </td>
    </tr>
<?php } ?>
    
</table>            
            

