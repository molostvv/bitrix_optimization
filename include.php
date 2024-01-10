<?
use Bitrix\Main\Loader;

const VSPACE_OPT_MODULE_ID = 'vspace.optimization';

Loader::registerAutoLoadClasses(VSPACE_OPT_MODULE_ID, array(
	'Vspace\Optimization\DataProviders\OptionProvider' => 'lib/DataProviders/OptionProvider.php',
	'Vspace\Optimization\Tools'	=> 'lib/Event.php',
	'Vspace\Optimization\Tools'	=> 'lib/Script.php',
	'Vspace\Optimization\Tools'	=> 'lib/Tools.php',
));

?>