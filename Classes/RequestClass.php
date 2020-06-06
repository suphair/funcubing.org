<?php

class RequestClass {

    protected static $_instance;
    protected static $title;
    protected static $page;
    protected static $error;
    protected static $param1;
    protected static $param2;
    protected static $param3;
    protected static $param4;

    private function __construct() {
        
    }

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    private function __clone() {
        
    }

    private function __wakeup() {
        
    }

    public static function setRequest() {

        self::$title = "FunCubing Projects";
        self::$page = "index";
        self::$param1 = 0;
        self::$param2 = 0;
        self::$param3 = 0;
        self::$param4 = 0;
        self::$error[401] = "";
        self::$error[404] = "";

        $request = getRequest();

        if (!isset($request[0])) {
            self::$title = "FunCubing Projects";
            return;
        }


        if ($request[0] == 'Goals') {
            self::$title = 'Competition Goals';
        }
        if (isset($_GET['Meetings'])) {
            self::$title = 'Unofficial Competitions';
        }
        if (isset($_GET['MosaicBuilding']) or $request[0] == 'MosaicBuilding') {
            self::$title = 'Mosaic Building';
        }
        if (isset($_GET['Achievements']) or $request[0] == 'Achievements') {
            self::$title = 'Speedcuber\'s Achievements';
        }
        if (isset($_GET['FriendsCompetitions']) or $request[0] == 'FriendsCompetitions') {
            self::$title = "Friends' Competitions";
        }

        $type = $request[0];


        if (substr($type, 0, 1) != '?') {
            if (!in_array($type, array(
                        'Login',
                        'Meetings', 'Goals', 'MosaicBuilding',
                        'FriendsCompetitions', 'MailUpcomingCompetition',
                    ))) {
                self::set404("Type $type not found ");
                return;
            }
        }


        if ($type == 'Meetings') {
            $Secret = isset($request[1]) ? DataBaseClass::Escape($request[1]) : "_empty_";
            $meeting = DataBaseClass::SelectTableRow('Meeting', "Secret='$Secret'");
            if ($meeting) {
                self::$title = 'Unnoficial Competition &#9642;  ' . $meeting['Meeting_Name'];
                self::$page = "Meeting";
                self::$param1 = $meeting['Meeting_Secret'];
            } else {
                self::set404("Unnoficial Competition $Secret not found ");
                return;
            }
        }

        if ($type == 'Goals') {
            
            $Competitor = GetCompetitorData();

            if ($Competitor) {
                GoalUpdateCompetitions($Competitor->id);
            }

            if (isset($request[1]) and isset($request[2]) and $request[1]=='Competition') {
                $wca = DataBaseClass::Escape($request[2]);
                $competition = DataBaseClass::SelectTableRow('GoalCompetition', "WCA='$wca'");
                if ($competition) {
                    self::$title = 'Competition Goals &#9642;  ' . $competition['GoalCompetition_Name'];
                    self::$page = "GoalsCompetition";
                    self::$param1 = $wca;
                } else {
                    self::set404("Competition Goals $wca not found ");
                    return;
                }
            } else {
                self::$page = "Goals";
            }
        }

        if ($type == 'MosaicBuilding') {
            self::$page = "MosaicBuilding";
        }

        if ($type == 'Achievements') {
            self::$page = "Achievements";
        }

        if ($type == 'FriendsCompetitions') {
            self::$page = "FriendsCompetitions";
        }

        if ($type == 'MailUpcomingCompetition') {
            self::$page = "MailUpcomingCompetition";
        }
    }

    private static function set404($error) {
        self::$title .= ' &#9642;  404';
        self::$error[404] = $error;
        self::$page = "error";
    }

    private static function set401($error) {
        self::$title .= ' &#9642;  401';
        self::$error[401] = $error;
        self::$page = "error";
    }

    public static function getParam1() {
        return self::$param1;
    }

    public static function getParam2() {
        return self::$param2;
    }

    public static function getParam3() {
        return self::$param3;
    }

    public static function getParam4() {
        return self::$param4;
    }

    public static function getPage() {
        return self::$page;
    }

    public static function getTitle() {
        return self::$title;
    }

    public static function getError($n = 0) {
        if ($n) {
            return self::$error[$n];
        } else {
            $error_return = "";
            foreach (self::$error as $error) {
                $error_return .= $error;
            }
            return $error_return;
        }
    }

}
