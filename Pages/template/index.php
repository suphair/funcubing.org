<?php
if(config::get('Admin', 'wcaid')){
    config::template('Templates');
    die('Templates generation is complete');
}else{
    die('Access denied');
}