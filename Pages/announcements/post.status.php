<?php

db::exec("UPDATE announcements SET status = $status WHERE user={$me->id}");
