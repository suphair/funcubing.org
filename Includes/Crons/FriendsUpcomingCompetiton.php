<?php

DataBaseClass::Query("
        SELECT DISTINCT
            FriendWCAID,
            value.Timestamp
        FROM Friend
        LEFT OUTER JOIN (
            SELECT MAX(Timestamp) Timestamp, Name 
            FROM Value 
            GROUP BY Name) value 
            ON value.Name = CONCAT('users_',Friend.FriendWCAID)
        WHERE  value.Timestamp is NULL 
            OR TIMESTAMPDIFF(MINUTE,Timestamp,now()) > 60   
");
$friends = DataBaseClass::getRows();
foreach ($friends as $friend) {
    GetUpcomingCompetition($friend['FriendWCAID']);
}
$_details['friends']['reload'] = count($friends);

DataBaseClass::Query("
        SELECT DISTINCT
            FriendWCAID
        FROM Friend
");
$friends = DataBaseClass::getRows();
$_details['friends']['total'] = count($friends);