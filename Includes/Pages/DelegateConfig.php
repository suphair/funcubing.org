    <?php include 'Delegates.php'; ?>
<hr>
<?php 
    $delegate=DataBaseClass::SelectTableRow("Delegate","WCA_ID='".RequestClass::getParam1()."'");
    $DelegateID=$delegate['Delegate_ID'];
?>

<h1 class="<?= $delegate['Delegate_Status'] ?>"><a href="<?= LinkDelegate($delegate['Delegate_WCA_ID'])?>"><?= $delegate['Delegate_Name'] ?></a>
		&#9642; <a href="https://www.worldcubeassociation.org/persons/<?= $delegate['Delegate_WCA_ID'] ?>"><?= $delegate['Delegate_WCA_ID'] ?></a>
   <?php if(CheckAdmin()){ ?>
		<span class="config" >&#9642; Setting</span>
    <?php } ?>
</h1>
<?php if($delegate['Delegate_Contact']){ ?>
    <div class="form"><?= Echo_format($delegate['Delegate_Contact']) ?></div><br>
<?php } ?>
<?php if(CheckAdmin()){ ?>
	<div class="form">
		<form method="POST" action="<?= PageIndex()."Actions/DelegateEdit" ?>">
			<input name="ID" type="hidden" value="<?= $delegate['Delegate_ID'] ?>" />
			<div class="form_field">
				Name 
			</div>
			<div class="form_input">
				<input type="text" name="Name" value="<?= $delegate['Delegate_Name'] ?>" />
			</div>
			<div class="form_field">
				Contacts
			</div>
			<div class="form_input">
				<textarea name="Contact"/><?= $delegate['Delegate_Contact']?></textarea>
			</div>
			<div class="form_field">
				WCA ID
			</div>
			<div class="form_input">
				<input type="text" name="WCA_ID" value="<?= $delegate['Delegate_WCA_ID']?>" />
			</div>
			<div class="form_field">
				Senior
                                <input type="checkbox" name="Admin" <?php if($delegate['Delegate_Admin']){ ?> checked <?php } ?> />
			</div>
                        <div class="form_field">
				Junior
                                <input type="checkbox" name="Candidate" <?php if($delegate['Delegate_Candidate']){ ?> checked <?php } ?> />
			</div>
                        <div class="form_field">
				Secret
                                <input name="Secret" value="<?= $delegate['Delegate_Secret'] ?>" />
			</div>
			<div class="form_change">
				<input type="submit" value="Change">
			</div>
		</form>
	</div>

<?php
    DataBaseClass::FromTable("CompetitionDelegate", "Delegate='".$delegate['Delegate_ID']."'");
    DataBaseClass::Join_current("Competition");
    DataBaseClass::QueryGenerate();
    ?>
    <div class="form">
        <?php if (DataBaseClass::rowsCount()==0){ ?>
                <form method="POST" action="<?= PageIndex()."Actions/DelegateDelete" ?>"   onsubmit="return confirm('Attention: Confirm the deletion.')">
                    <input name="ID" type="hidden" value="<?= $delegate['Delegate_ID'] ?>" />
                    <input class="delete" type="submit" value="Delete">
                </form>
       <?php }elseif($delegate['Delegate_Status']=='Active'){ ?>
                <form method="POST" action="<?= PageIndex()."Actions/DelegateArchive" ?>">
                    <input name="ID" type="hidden" value="<?= $delegate['Delegate_ID'] ?>" />
                    <input class="delete"  type="submit" value="Send to archive">
                </form>
        <?php }else{ ?>
                <form method="POST" action="<?= PageIndex()."Actions/DelegateActive" ?>">
                    <input name="ID" type="hidden" value="<?= $delegate['Delegate_ID'] ?>" />
                    <input type="submit" value="To return from the archive">
                </form>
        <?php } ?> 
    </div>
<?php }else{ ?>
Access denied
<?php } ?>