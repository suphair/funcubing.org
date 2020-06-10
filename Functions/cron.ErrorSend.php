<?php

function errorSend($daily = 0) {

    $errors = Suphair \ Error :: getAll();
    $counts = [
        Suphair \ Error :: _NEW => 0,
        Suphair \ Error :: _WORK => 0,
        Suphair \ Error :: _SKIP => 0,
        Suphair \ Error :: _DONE => 0];
    foreach ($errors as $error) {
        $counts[$error['status']] ++;
    }

    $new = $counts[Suphair \ Error :: _NEW];
    $work = $counts[Suphair \ Error :: _WORK];
    $skip = $counts[Suphair \ Error :: _SKIP];
    $done = $counts[Suphair \ Error :: _DONE];

    if (!$daily) {
        if ($new) {
            SendMail(
                    Suphair \ Config :: get('Admin', 'email'), "FunCubing error: $new"
                    , "New errors on the site http:" . PageIndex() . " $new<br><a href='http:" . PageIndex() . "Classes/suphair_error'>http:" . PageIndex() . "Classes/suphair_error</a>"
            );
        }
    } elseif($counts[Suphair \ Error :: _NEW] == 0){
        if ($counts[Suphair \ Error :: _WORK] == 0) {
            SendMail(
                    Suphair \ Config :: get('Admin', 'email'), "FunCubing NO ERROR"
                    , "No new errors on the site http:" . PageIndex() . "<br><a href='http:" . PageIndex() . "Classes/suphair_error'>http:" . PageIndex() . "Classes/suphair_error</a>"
            );
        } else {
            SendMail(
                    Suphair \ Config :: get('Admin', 'email'), "FunCubing error in work: $work"
                    , "Errors in work on site http:" . PageIndex() . " $work<br><a href='http:" . PageIndex() . "Classes/suphair_error'>http:" . PageIndex() . "Classes/suphair_error</a>"
            );
        }
    }
    return json_encode($counts);
}
