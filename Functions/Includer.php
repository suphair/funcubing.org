<?php

function IncluderAction() {
    $request = getRequest();
    if (sizeof($request) == 1 and $request[0] == 'Cron') {
        if (
                ( CheckAdmin()
                or $_SERVER['HTTP_USER_AGENT'] == 'Wget/1.17.1 (linux-gnu)'
                or Suphair \ Config :: isLocalhost()
                ) === false
        ) {
            echo '!! only CRON';
            exit();
        }
        $cron = new \Suphair\Cron(DataBaseClass::getConection());
        $cron->run();
        DataBaseClass::close();
        exit();
    }

    if (sizeof($request) >= 2 and $request[0] == "Actions") {
        $request[1] = explode("?", $request[1])[0];
        SetPostValues($request[1]);
        IncludeExists('Includes/' . $request[0] . '/' . $request[1] . '.php');
    }
}

function IncludeExists($file) {
    if (file_exists($file)) {
        include $file;
    } else {
        echo ">>" . $file . "<<";
        exit();
    }
}

function getRequest() {

    global $request;

    $prefix = str_replace("index.php", "", filter_input(INPUT_SERVER, 'SCRIPT_NAME'));
    $request = explode('/', str_replace($prefix, ''
                    , filter_input(INPUT_SERVER, 'REQUEST_URI')
            )
    );

    foreach ($request as $n => $v) {
        $request[$n] = explode('?fbclid', $v)[0];
        if (!$v)
            unset($request[$n]);
    }
    $request = array_values($request);
    return $request;
}

function Request() {
    global $request;
    return $request;
}
