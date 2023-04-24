<?php

namespace api;

function get_me() {

    $wcaoauth = \wcaoauth::me();
    if (!$wcaoauth) {
        $json = ['error' => 'Not authorized'];
    } else {
        $federation = explode(",", \config::get('Federation', 'wcaid'));
        $federation_ext = explode(",", \config::get('FederationExt', 'wcaid'));

        $json = [
            'wid' => $wcaoauth->id,
            'wca_id' => $wcaoauth->wca_id,
            'name' => $wcaoauth->name,
            'is_admin' => \config::get('Admin', 'wcaid') == $wcaoauth->wca_id,
            'is_federation' => in_array($wcaoauth->wca_id, $federation) or in_array($wcaoauth->wca_id, $federation_ext),
            'is_federation_ext' => in_array($wcaoauth->wca_id, $federation_ext)
        ];
    }

    return (object) $json;
}
