
<?php

$name = db::escape(filter_input(INPUT_POST, 'name'));
$wca_name = db::escape(filter_input(INPUT_POST, 'wca_name'));
$FCID_prefix = db::escape(filter_input(INPUT_POST, 'FCID'));
$wcaid = db::escape(filter_input(INPUT_POST, 'WCAID'));

$current_name = db::escape(filter_input(INPUT_POST, 'current_name'));
$current_wca_name = db::escape(filter_input(INPUT_POST, 'current_wca_name'));
$current_FCID = db::escape(filter_input(INPUT_POST, 'current_FCID'));
$current_ID = db::escape(filter_input(INPUT_POST, 'current_ID'));
$nonwca = db::escape(filter_input(INPUT_POST, 'nonwca')) ? 1 : 0;

if (strlen($current_FCID) != 4 or strlen($FCID_prefix) != 2) {
    die("Wrong format FCID $current_FCID(4)->$FCID_prefix(2)");
}

$FCID = $current_FCID;
if ($name and $FCID_prefix and $current_name and $current_FCID and $current_ID and
        ($name != $current_name or $FCID_prefix != substr($current_FCID, 0, 2))) {

    $FCID_change = ($FCID_prefix != substr($current_FCID, 0, 2));
    $name_change = ($name != $current_name);
    $competitor = db::row("SELECT * "
                    . "FROM unofficial_competitors "
                    . "WHERE ID='$current_ID' AND name='$current_name' AND FCID='$current_FCID' ");

    if ($competitor->id ?? false) {
        if ($FCID_change) {
            $FCID = db::row("select CONCAT(left(max(FCID),2),right(CONCAT('00',right(max(FCID),2)+1),2))  FCID from `unofficial_competitors` where FCID like '$FCID_prefix%'")->FCID ?? "{$FCID_prefix}01";
        } else {
            $FCID = $current_FCID;
        }

        if ($name_change) {
            $competitor_dublicate = db::row("SELECT * "
                            . "FROM unofficial_competitors "
                            . "WHERE name='$name' and FCID!='$current_FCID'");
            if ($competitor_dublicate) {
                die("There is another competitor named $name");
            }
        }

        db::exec("UPDATE unofficial_competitors
        SET name = '$name', FCID = '$FCID'
        WHERE name='$current_name' AND FCID='$current_FCID' ");
        
    }
}

if($wca_name != $current_wca_name){
    db::exec("UPDATE unofficial_fc_wca
        SET wca_name = '$wca_name'
        WHERE FCID = '$FCID'");
}

unofficial\set_wca($FCID, $wcaid, $nonwca, $current_FCID);

if ($FCID_change ?? false) {
    header("Location: " . PageIndex() . "competitions/rankings/competitor/$FCID");
    exit();
}
?>