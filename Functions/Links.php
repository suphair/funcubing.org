<?php

function GetUrlWCA() {
    Suphair \ Wca \ Oauth::set(
            GetIni('WCA_AUTH', 'client_id')
            , GetIni('WCA_AUTH', 'client_secret')
            , GetIni('WCA_AUTH', 'scope')
            , PageIndex() . GetIni('WCA_AUTH', 'url_refer')
            , DataBaseClass::getConection()
    );

    return Suphair \ Wca \ Oauth::url();
}
