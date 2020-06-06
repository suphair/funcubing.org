<?php

function GetUrlWCA() {
    Suphair \ Wca \ Oauth::set(
            GetIni('WCA_OAUTH', 'client_id')
            , GetIni('WCA_OAUTH', 'client_secret')
            , GetIni('WCA_OAUTH', 'scope')
            , PageIndex() . GetIni('WCA_OAUTH', 'url_refer')
            , DataBaseClass::getConection()
    );

    return Suphair \ Wca \ Oauth::url();
}
