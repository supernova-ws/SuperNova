<?php

use \Core\Autoloader;

define('INSIDE', true);

define('SN_TIME_MICRO', microtime(true));
define('SN_MEM_START', memory_get_usage());

define('SN_ROOT_PHYSICAL', str_replace(array('\\', '//'), '/', dirname(__DIR__) . '/'));
define('SN_ROOT_PHYSICAL_STR_LEN', strlen(SN_ROOT_PHYSICAL)); // mb_strlen ???

require_once __DIR__ . '/includes/test_constants.php';
require_once __DIR__ . '/includes/test_functions.php';

require_once SN_ROOT_PHYSICAL . 'includes/constants/constants.php';
require_once SN_ROOT_PHYSICAL . 'includes/general/general.php';

require_once SN_ROOT_PHYSICAL . 'classes/Core/Autoloader.php';

Autoloader::register('classes/');
Autoloader::register('classes/UBE/');

Autoloader::register('tests/');
Autoloader::register('tests/Tests');
Autoloader::register('tests/Fixtures');
