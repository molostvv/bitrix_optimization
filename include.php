<?
use Bitrix\Main\Loader;

const VSPACE_OPT_MODULE_ID = 'vspace.optimization';

Loader::registerAutoLoadClasses(VSPACE_OPT_MODULE_ID, array(
	'Vspace\Optimization\Tools'	=> 'lib/Tools.php',
));

?>