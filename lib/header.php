<?php
include_once '_approot.php';

include_once APPROOT . '/lib/utility/common.php';
include_once APPROOT . '/lib/utility/config.php';
// include_once APPROOT . '/lib/utility/home_generator.php';
include_once APPROOT . '/settings.php';
function __autoload($className) {
	$class_path = array (
			'/lib/base/',
			'/lib/utility/',
			'/common/service/',
			'/common/part/',
			'/pbs/service/',
			'/pbs/part/',
			'/sql/',
			'/template/' 
	);
	
	foreach ($class_path as $each) {
		$file = APPROOT . $each . $className . '.class.php';
		if (file_exists($file)) {
			include_once ($file);
			break;
		}
	}
}

?>