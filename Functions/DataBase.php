<?php

Function DataBaseInit() {

    $connection = mysqli_init();
    $success = mysqli_real_connect(
            $connection
            , Suphair \ Config :: get('DB', 'host')
            , Suphair \ Config :: get('DB', 'username')
            , Suphair \ Config :: get('DB', 'password')
            , Suphair \ Config :: get('DB', 'schema')
            , Suphair \ Config :: get('DB', 'port')
    );
    if (!$success) {
        echo '<h1>Error establishing a database connection</h1>';
        exit();
    }
    mysqli_query($connection, "SET CHARSET UTF8");
    DataBaseClass::setConection($connection);

    DataBaseClass::AddTable('Competitor', 'Cm', array('ID', 'Name', 'WCAID', 'Country', 'WID'));
    DataBaseClass::SetOrder('Competitor', ' Name');
    
    DataBaseClass::AddTable('Meeting', 'M', array('ID', 'Name', 'Details', 'Competitor', 'Secret', 'SecretRegistration', 'ShareRegistration', 'Show', 'Website', 'Date'));
    DataBaseClass::SetOrder('Meeting', ' ID desc');

    DataBaseClass::AddTable('MeetingDiscipline', 'MD', array('ID', 'Meeting', 'MeetingDisciplineList', 'Round', 'MeetingFormat', 'Comment', 'Name', 'Amount'));
    DataBaseClass::SetJoin('MeetingDiscipline', 'Meeting');

    DataBaseClass::AddTable('MeetingDisciplineList', 'MDL', array('ID', 'Name', 'Image', 'Code', 'Amount'));
    DataBaseClass::SetOrder('MeetingDisciplineList', ' ID');
    DataBaseClass::SetJoin('MeetingDiscipline', 'MeetingDisciplineList');

    DataBaseClass::AddTable('MeetingFormat', 'MF', array('ID', 'Format', 'Attempts'));
    DataBaseClass::SetJoin('MeetingDiscipline', 'MeetingFormat');
    DataBaseClass::SetOrder('MeetingFormat', 'Attempts DESC, Format DESC');

    DataBaseClass::AddTable('MeetingCompetitor', 'MC', array('ID', 'Meeting', 'Name', 'Session'));
    DataBaseClass::SetJoin('MeetingCompetitor', 'Meeting');

    DataBaseClass::AddTable('MeetingCompetitorDiscipline', 'MCD', array('ID', 'MeetingCompetitor', 'MeetingDiscipline', 'Place', 'Attempts', 'Attempt1', 'Attempt2', 'Attempt3', 'Attempt4', 'Attempt5', 'Best', 'Average', 'Mean', 'MilisecondsOrder'));
    DataBaseClass::SetJoin('MeetingCompetitorDiscipline', 'MeetingCompetitor');
    DataBaseClass::SetJoin('MeetingCompetitorDiscipline', 'MeetingDiscipline');

    DataBaseClass::AddTable('MeetingOrganizer', 'MO', array('ID', 'Meeting', 'WCAID'));
    DataBaseClass::SetJoin('MeetingOrganizer', 'Meeting');

    DataBaseClass::AddTable('GoalCompetition', 'GCn', array('ID', 'Name', 'DateStart', 'DateEnd', 'WCA'));

    DataBaseClass::AddTable('GoalDiscipline', 'GD', array('ID', 'Name', 'Code'));
    DataBaseClass::SetOrder('GoalDiscipline', ' ID');

    DataBaseClass::AddTable('Goal', 'G', array('ID', 'Competitor', 'Discipline', 'Competition', 'Format', 'Result', 'TimeFixed', 'Record', 'Progress', 'Complete'));


}
