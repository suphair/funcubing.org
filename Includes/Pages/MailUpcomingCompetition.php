<h1>Subscription to announcements of WCA competitions</h1>
When WCA competitions for tracked countries are announced on the WCA website, you will receive an email
<?php 
$competitor=GetCompetitorData();
if($competitor){
    DataBaseClass::Query("Select * from MailUpcomingCompetitions where Competitor={$competitor->id}");
    if(!DataBaseClass::getRow()){
        DataBaseClass::Query("Insert into MailUpcomingCompetitions (Email,Competitor,Country) values('{$competitor->email}',{$competitor->id},'$competitor->country_iso2')");    
    }
    DataBaseClass::Query("Update MailUpcomingCompetitions set Email='{$competitor->email}' where Competitor={$competitor->id}");
    DataBaseClass::Query("Select * from MailUpcomingCompetitions where Competitor={$competitor->id}");
    $mailUpcomingCompetitions=DataBaseClass::getRow();
    $mail=$mailUpcomingCompetitions['Email'];
    $countries=explode(",",$mailUpcomingCompetitions['Country']);    
?>    
<div class="form">
        <h2><?= Short_Name($competitor->name) ?>: <?= $mail ?></h2>  
        <h3>
            <?php if($mailUpcomingCompetitions['Status']){ ?>
                <span class="message">Subscription is done</span> <font size="2"><?= $mailUpcomingCompetitions['announced_at'] ?></font>
            <?php }else{ ?>
                <span class="error">Subscription disabled</span>
            <?php } ?>        
         </h3>       
        <?php foreach($countries as $country){ ?>
                    <span class='badge'>
                        <nobr>
                            <?= ImageCountry($country, 20)?><?= CountryName($country) ?>
                        </nobr>
                    </span>
                <?php } ?>
        <?php if($mailUpcomingCompetitions['Status']){ ?>
            <form method="POST" action="<?= PageIndex()."Actions/MailUpcomingCompetitionsUnsubscribe" ?>">
                <input type="submit" class="delete" value="Unsubscribe">
            </form> 
        <?php }else{ ?>
            <form method="POST" action="<?= PageIndex()."Actions/MailUpcomingCompetitionsSubscribe" ?>">
                <input type="submit" value="Subscribe">
            </form> 
        <?php } ?>
        <hr>
        <div class="form2">
            Tracked countries (multiple choice)
        <form method="POST" action="<?= PageIndex()."Actions/MailUpcomingCompetitionsSave" ?>">
        <select style="width: 600px" ID="countries-chosen-select" Name="countries[]" data-placeholder="Choose  countries" class="chosen-select" multiple>
            <?php DataBaseClass::Query("Select * from Country where ISO2<>'' and Name<>'' order by Name");
            foreach(DataBaseClass::getRows() as $country){ ?>
                <option name="countries[]"  <?= in_array($country['ISO2'],$countries)?'selected':'' ?> value='<?= $country['ISO2'] ?>'>
                    <?= $country['ISO2'] ?> - <?= $country['Name'] ?>
                </option>
            <?php } ?>
        </select>
        <input type="submit" value="Save countries">
         </form>    
        </div>        
                       
</div>
    

<script> 
        $('.chosen-search-input').focus();        
</script>

<?php }else{ ?>
    <div class="form">
        <span class="error">    
            <?php  $_SESSION['ReferAuth']=$_SERVER['REQUEST_URI']; ?>    
            To subscribe you need to <a href="<?= GetUrlWCA(); ?>">sign in with WCA</a>
        </span> 
    </div> 
<?php } ?>
<hr>
     <?php DataBaseClass::Query("Select count(*) count from `MailUpcomingCompetitions` where Status=1"); 
     $data=DataBaseClass::getRow() ?>
     Total subscribed <?= $data['count'] ?>
