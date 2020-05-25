<?php

Function DataBaseInit() {

    if (strpos($_SERVER['PHP_SELF'], '/' . GetIni('LOCAL', 'PageBase') . '/') !== false) {
        $section = "DB_LOCAL";
    } else {
        $section = "DB";
    }


    $connection = mysqli_init();
    @$success = mysqli_real_connect(
            $connection, GetIni($section, 'host'), GetIni($section, 'username'), GetIni($section, 'password'), GetIni($section, 'schema'), GetIni($section, 'port')
    );

    $connection2 = mysqli_init();
    @$success = mysqli_real_connect(
            $connection2, GetIni($section, 'host'), GetIni($section, 'username'), GetIni($section, 'password'), GetIni($section, 'schema_WCA'), GetIni($section, 'port')
    );

    if (!$success) {
        echo '<h1>Error establishing a database connection</h1>';
        exit();
    }

    mysqli_query($connection, "SET CHARSET UTF8");
    DataBaseClass::setConection($connection);
    mysqli_query($connection2, "SET CHARSET UTF8");
    DataBaseClassWCA::setConection($connection2);

    DataBaseClass::AddTable('Competition', 'C', array('ID', 'Name', 'WCA', 'City', 'StartDate', 'EndDate', 'WebSite', 'Registration', /* 'Delegate', */ 'Country', 'Status', 'MaxCardID', 'CheckDateTime', 'LoadDateTime', 'Comment'/* ,'Report' */, 'Onsite', 'EventPicture'));
    DataBaseClass::SetOrder('Competition', ' StartDate desc');

    DataBaseClass::AddTable('Discipline', 'D', array('ID', 'Name', 'Code', 'Status', 'Competitors', 'TNoodle', 'TNoodles', 'TNoodlesMult', 'CutScrambles', 'GlueScrambles'));
    DataBaseClass::SetOrder('Discipline', 'Name');

    DataBaseClass::AddTable('Delegate', 'Dl', array('ID', 'Name', 'Site', 'Contact', 'Status', 'WCA_ID', 'WID', 'Admin', 'OrderLine', 'Candidate', 'Secret'));
    DataBaseClass::SetOrder('Delegate', ' OrderLine');
    // DataBaseClass::SetJoin('Competition','Delegate');

    DataBaseClass::AddTable('CompetitionDelegate', 'CD', array('ID', 'Competition', 'Delegate'));
    DataBaseClass::SetJoin('CompetitionDelegate', 'Delegate');
    DataBaseClass::SetJoin('CompetitionDelegate', 'Competition');

    DataBaseClass::AddTable('CompetitionReport', 'CR', array('ID', 'Competition', 'Delegate', 'Report'));
    DataBaseClass::SetJoin('CompetitionReport', 'Delegate');
    DataBaseClass::SetJoin('CompetitionReport', 'Competition');


    DataBaseClass::AddTable('Competitor', 'Cm', array('ID', 'Name', 'WCAID', 'Country', 'WID'));
    DataBaseClass::SetOrder('Competitor', ' Name');

    DataBaseClass::AddTable('Format', 'F', array('ID', 'Result', 'Attemption', 'Name', 'ExtResult'));

    DataBaseClass::AddTable('DisciplineFormat', 'DF', array('ID', 'Discipline', 'Format'));
    DataBaseClass::SetJoin('DisciplineFormat', 'Format');
    DataBaseClass::SetJoin('DisciplineFormat', 'Discipline');


    DataBaseClass::AddTable('Event', 'E', array('ID', 'CutoffMinute', 'CutoffSecond', 'LimitMinute', 'LimitSecond', 'Secret', 'Competitors', 'Competition', 'Groups', 'LocalID', 'Round', 'vRound', 'DisciplineFormat', 'Cumulative', 'Comment'));
    DataBaseClass::SetJoin('Event', 'Competition');
    DataBaseClass::SetJoin('Event', 'DisciplineFormat');

    DataBaseClass::AddTable('Command', 'Com', array('ID', 'Place', 'CardID', 'Decline', 'Event', 'Group', 'Event', 'Secret', 'vCompetitors', 'vCountry', 'vName', 'vCompetitorIDs', 'Warnings', 'Onsite', 'DateCreated', 'Video'));
    DataBaseClass::SetJoin('Command', 'Event');

    DataBaseClass::AddTable('CommandCompetitor', 'CC', array('ID', 'Command', 'Competitor', 'CheckStatus'));
    DataBaseClass::SetJoin('CommandCompetitor', 'Command');
    DataBaseClass::SetJoin('CommandCompetitor', 'Competitor');

    DataBaseClass::AddTable('Attempt', 'A', array('ID', 'Attempt', 'IsDNF', 'IsDNS', 'Minute', 'Second', 'Milisecond', 'Except', 'Special', 'vOut', 'vOrder', 'Amount'));
    DataBaseClass::SetJoin('Attempt', 'Command');
    DataBaseClass::SetOrder('Attempt', ' vOrder');

    DataBaseClass::AddTable('Scramble', 'S', array('ID', 'Event', 'Scramble', 'Group', 'Timestamp'));
    DataBaseClass::SetJoin('Scramble', 'Event');

    DataBaseClass::AddTable('RequestCandidate', 'RC', array('ID', 'Competitor', 'Datetime', 'Status'));
    DataBaseClass::SetJoin('RequestCandidate', 'Competitor');

    DataBaseClass::AddTable('RequestCandidateField', 'RCF', array('ID', 'RequestCandidate', 'Field', 'Value'));
    DataBaseClass::SetJoin('RequestCandidateField', 'RequestCandidate');


    DataBaseClass::AddTable('RequestCandidateTemplate', 'RCT', array('ID', 'Name', 'Type', 'Language'));
    DataBaseClass::SetOrder('RequestCandidateTemplate', ' ID');

    DataBaseClass::AddTable('Registration', 'Reg', array('ID', 'Competition', 'Competitor'));
    DataBaseClass::SetJoin('Registration', 'Competitor');

    DataBaseClass::AddTable('FormatResult', 'FR', array('ID', 'Name', 'Format'));
    DataBaseClass::SetJoin('Discipline', 'FormatResult');

    DataBaseClass::AddTable('BlockText', 'BT', array('ID', 'Name', 'Value', 'Country'));

    DataBaseClass::AddTable('Meeting', 'M', array('ID', 'Name', 'Details', 'Competitor', 'Secret', 'SecretRegistration', 'ShareRegistration', 'Show', 'Website', 'Date'));
    DataBaseClass::SetOrder('Meeting', ' ID desc');

    DataBaseClass::AddTable('MeetingDiscipline', 'MD', array('ID', 'Meeting', 'MeetingDisciplineList', 'Round', 'MeetingFormat', 'Comment', 'Name','Amount'));
    DataBaseClass::SetJoin('MeetingDiscipline', 'Meeting');

    DataBaseClass::AddTable('MeetingDisciplineList', 'MDL', array('ID', 'Name', 'Image', 'Code','Amount'));
    DataBaseClass::SetOrder('MeetingDisciplineList', ' ID');
    DataBaseClass::SetJoin('MeetingDiscipline', 'MeetingDisciplineList');

    DataBaseClass::AddTable('MeetingFormat', 'MF', array('ID', 'Format', 'Attempts'));
    DataBaseClass::SetJoin('MeetingDiscipline', 'MeetingFormat');
    DataBaseClass::SetOrder('MeetingFormat', 'Attempts DESC, Format DESC');

    DataBaseClass::AddTable('MeetingCompetitor', 'MC', array('ID', 'Meeting', 'Name', 'Session'));
    DataBaseClass::SetJoin('MeetingCompetitor', 'Meeting');

    DataBaseClass::AddTable('MeetingCompetitorDiscipline', 'MCD', array('ID', 'MeetingCompetitor', 'MeetingDiscipline', 'Place', 'Attempts', 'Attempt1', 'Attempt2', 'Attempt3', 'Attempt4', 'Attempt5', 'Best','Average','Mean',  'MilisecondsOrder'));
    DataBaseClass::SetJoin('MeetingCompetitorDiscipline', 'MeetingCompetitor');
    DataBaseClass::SetJoin('MeetingCompetitorDiscipline', 'MeetingDiscipline');

    DataBaseClass::AddTable('MeetingOrganizer', 'MO', array('ID', 'Meeting', 'WCAID'));
    DataBaseClass::SetJoin('MeetingOrganizer', 'Meeting');

    DataBaseClass::AddTable('GoalCompetition', 'GCn', array('ID', 'Name', 'DateStart', 'DateEnd', 'WCA'));

    DataBaseClass::AddTable('GoalDiscipline', 'GD', array('ID', 'Name', 'Code'));
    DataBaseClass::SetOrder('GoalDiscipline', ' ID');

    DataBaseClass::AddTable('Goal', 'G', array('ID', 'Competitor', 'Discipline', 'Competition', 'Format', 'Result', 'TimeFixed', 'Record', 'Progress', 'Complete'));

    DataBaseClass::AddTable('MosaicBuilding', 'MB', array('ID', 'Description'));

    DataBaseClass::AddTable('MosaicBuildingImage', 'MBI', array('ID', 'MosaicBuilding', 'Filename'));
    DataBaseClass::SetJoin('MosaicBuildingImage', 'MosaicBuilding');

    DataBaseClass::AddTable('Regulation', 'R', array('ID', 'Discipline', 'Country', 'Text'));
    DataBaseClass::SetJoin('Regulation', 'Discipline');
}
