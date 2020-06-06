<?php

function GetUrlWCA() {
    Suphair \ Wca \ Oauth::set(
            Suphair \ Config :: get('WCA_OAUTH', 'client_id')
            , Suphair \ Config :: get('WCA_OAUTH', 'client_secret')
            , Suphair \ Config :: get('WCA_OAUTH', 'scope')
            , PageIndex() . Suphair \ Config :: get('WCA_OAUTH', 'url_refer')
            , DataBaseClass::getConection()
    );

    return Suphair \ Wca \ Oauth::url();
}
