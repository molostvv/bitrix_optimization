<?php
namespace Vspace\Optimization\DataProviders;

use Vspace\Optimization\DataProviders\Entities\OptionTable;

require_once __DIR__ . "/Entities/OptionTable.php";



class OptionProvider
{

    public const KEY_CODE     = 'ex_scripts_code';
    public const KEY_PLACE    = 'ex_scripts_place';
    public const KEY_DELAYED  = 'ex_scripts_delayed';
    public const KEY_TIME     = 'ex_scripts_time';
    public const KEY_CSS      = 'ex_scripts_css';

    public function getOptions()
    {
        $options = [];

        $result = OptionTable::getList(array(
            'select'  => ['NAME', 'VALUE'],
            'filter'  => [
                '=MODULE_ID' => 'vspace.optimization'
            ]
        ));

        while ($row = $result->fetch()){
            $options[$row['NAME']] = $row['VALUE'];
        }

        // Получаем список уникальных фрагментов ключей внешних скриптов 
        $arCodes = [];
        foreach ($options as $key => $value) {
            $position = stripos($key, self::KEY_CODE);
            if($position !== false){
                $_fragKeyOption = str_replace(self::KEY_CODE, "", $key);
                $arCodes[ $_fragKeyOption ] = [];
            }
        }

        // Получаем параметры для каждого внешнего скрипта
        $arConst = [OptionProvider::KEY_CODE, OptionProvider::KEY_PLACE, OptionProvider::KEY_DELAYED, OptionProvider::KEY_TIME, OptionProvider::KEY_CSS];
        foreach ($arCodes as $key => &$value) {
            foreach ($arConst as $fragKey) {
                if(array_key_exists($fragKey . $key, $options)){
                    $value[$fragKey] = $options[ $fragKey . $key ];
                }
            }
        }

        $options = $arCodes;

        return $options;
    }

}