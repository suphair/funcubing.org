<?php
wcaoauth::set(
        config :: get('WCA_OAUTH', 'client_id')
        , config :: get('WCA_OAUTH', 'client_secret')
        , config :: get('WCA_OAUTH', 'scope')
        , PageIndex() . config :: get('WCA_OAUTH', 'url_refer')
        , db::connection()
);

$url = wcaoauth::url();
?>
<a href="<?= $url ?>">
    <i class="fas fa-sign-in-alt"></i>
    Sign in with WCA
</a>