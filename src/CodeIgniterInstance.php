<?php

define('APPPATH', realpath('application') . '/');
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');
define('VIEWPATH', APPPATH . '/views/');

/**
 * Search for the CodeIgniter core files
 */

$directory = new RecursiveDirectoryIterator(APPPATH . '../', FilesystemIterator::SKIP_DOTS);
foreach (new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST) as $path) {
	if (strpos($path->__toString(), 'core/CodeIgniter.php') !== FALSE) {
		$basepath = str_replace('core/CodeIgniter.php', '', $path->__toString());
		define('BASEPATH', $basepath);

		break;
	}
}

require BASEPATH . 'core/Common.php';
require BASEPATH . 'core/Controller.php';

if (file_exists(APPPATH . 'config/' . ENVIRONMENT . '/constants.php')) {
	require APPPATH . 'config/' . ENVIRONMENT . '/constants.php';
} else {
	require APPPATH . 'config/constants.php';
}

$charset = strtoupper(config_item('charset'));
ini_set('default_charset', $charset);

if (extension_loaded('mbstring')) {
	define('MB_ENABLED', TRUE);
	@ini_set('mbstring.internal_encoding', $charset);
	mb_substitute_character('none');
} else {
	define('MB_ENABLED', FALSE);
}

if (extension_loaded('iconv')) {
	define('ICONV_ENABLED', TRUE);
	@ini_set('iconv.internal_encoding', $charset);
} else {
	define('ICONV_ENABLED', FALSE);
}

$GLOBALS['CFG'] = & load_class('Config', 'core');
$GLOBALS['UNI'] = & load_class('Utf8', 'core');
$GLOBALS['SEC'] = & load_class('Security', 'core');

load_class('Loader', 'core');
load_class('Router', 'core');
load_class('Input', 'core');
load_class('Lang', 'core');

function &get_instance()
{
	return \CI_Controller::get_instance();
}

return new \CI_Controller();