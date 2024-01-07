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

    public const KEY_CODE_MODIF = 'ex_scripts_code_modificate';

    public const KEY_PLACE_HEAD        = 'head';
    public const KEY_PLACE_BEGIN_BODY  = 'begin_body';
    public const KEY_PLACE_END_BODY    = 'end_body';

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

        // Получаем src скриптов и модифицируем в случае необходимости
        foreach ($arCodes as $key => &$value) {
            
            if($value[OptionProvider::KEY_DELAYED] == 'Y' || !empty($value[OptionProvider::KEY_TIME])){

                $type = 'text/javascript';
                if($value[OptionProvider::KEY_DELAYED] == 'Y'){
                    $type = 'delayed';
                }
                preg_match('/<script[^>]*src="([^"]+)"/', $value[OptionProvider::KEY_CODE], $matches);
                if(!empty($matches[1])){ // Если есть src у скрипта
                    $value[OptionProvider::KEY_CODE_MODIF] = "var script = document.createElement('script'); script.src = '". $matches[1] ."'; script.charset = 'UTF-8'; document.getElementsByTagName('body')[0].appendChild(script);";
                
                    if(!empty($value[OptionProvider::KEY_TIME])){
                        $value[OptionProvider::KEY_CODE_MODIF] = 'setTimeout(function(){ ' . $value[OptionProvider::KEY_CODE_MODIF] . ' }, '.$value[OptionProvider::KEY_TIME].');';
                    }

                    $value[OptionProvider::KEY_CODE_MODIF] = '<script type="'.$type.'" charset="UTF-8" data-cfasync="false">' . $value[OptionProvider::KEY_CODE_MODIF] . '</script>';
                } else { // Если встроенный скрипт
                    $matches = [];
                    preg_match('/<script\b[^>]*>(.*?)<\/script>/s', $value[OptionProvider::KEY_CODE], $matches);

                    if (isset($matches[1])) {

                        if(!empty($value[OptionProvider::KEY_TIME])){
                            $value[OptionProvider::KEY_CODE_MODIF] = 'setTimeout(function(){ ' . $matches[1] . ' }, '.$value[OptionProvider::KEY_TIME].');';
                        } else {
                            $value[OptionProvider::KEY_CODE_MODIF] = $matches[1];
                        }

                        // todo: добавить для метрики noscript
                        $value[OptionProvider::KEY_CODE_MODIF] = '<script type="'.$type.'" charset="UTF-8" data-cfasync="false">' . $value[OptionProvider::KEY_CODE_MODIF] . '</script>';
                    }
                }

            }
            // endif
        }

        // Группируем скрипты по месту вывода
        $arResult = [];
        foreach ($arCodes as $fragKey => $data) {
            foreach ($data as $_key => $_value) {
                $arResult[$data[OptionProvider::KEY_PLACE]][$fragKey][$_key] = $_value;
            }
        }

        return $arResult;
    }

}