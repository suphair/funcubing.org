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
    echo "<p>{$friend['FriendWCAID']}</p>";
    GetUpcomingCompetition($friend['FriendWCAID']);
}

exit();
