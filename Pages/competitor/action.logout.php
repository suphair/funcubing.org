<?php
wcaoauth::out();
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();