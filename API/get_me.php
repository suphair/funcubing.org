<?php

namespace api;

function get_me() {

    $wcaoauth = \wcaoauth::me();
    if (!$wcaoauth) {
        $json = ['error' => 'Not authorized'];
    } else {
        $json = [
            'wid' => $wcaoauth->id,
            'wca_id' => $wcaoauth->wca_id,
            'name' => $wcaoauth->name,
            'is_admin' => \config::get('Admin', 'wcaid') == $wcaoauth->wca_id,
            'is_federation' => in_array($wcaoauth->wca_id, explode(",", \config::get('Federation', 'wcaid')))
        ];
    }

    return (object) $json;
}
