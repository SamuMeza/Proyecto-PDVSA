<?php

define('APP_NAME', getenv('APP_NAME') ?: 'Sistema PDVSA');
define('APP_TIMEZONE', getenv('APP_TIMEZONE') ?: 'America/Caracas');

date_default_timezone_set(APP_TIMEZONE);
