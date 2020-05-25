<?php

$Competitor = GetCompetitorData();
if ($Competitor) {
    CheckPostIsset('Name', 'Date');
    CheckPostNotEmpty('Name');

    $Name = DataBaseClass::Escape($_POST['Name']);
    $Details = DataBaseClass::Escape($_POST['Details']);
    $Secret = random_string(10);
    $Date = date('Y-m-d', strtotime($_POST['Date']));

    DataBaseClass::Query("Insert into  `Meeting` ( Name,Date,Details,Competitor,Secret) VALUES('$Name','$Date','$Details'," . $Competitor->id . ",'$Secret')");

    AddLog("Meeting", "Create", $Competitor->name . ' / ' . $Name . ': ' . $Date);
}
header('Location: ' . PageIndex() . "/Meetings/$Secret/?Setting");
exit();
