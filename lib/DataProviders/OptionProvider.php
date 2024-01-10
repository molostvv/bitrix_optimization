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
        $arResult = [
            'DELAYED' => false
        ];
        $options  = [];

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

        // Проверка наличия хотябы одного отложенного скрипта
        foreach ($arCodes as $scriptData) {
            if(isset($scriptData[OptionProvider::KEY_DELAYED]) && $scriptData[OptionProvider::KEY_DELAYED] == 'Y'){
                $arResult['DELAYED'] = true;
            }
        }

        // Преобразуем скрипты в том случае, если в опциях установлено значения для отложенной загрузки или указано время для setTimeout 
        foreach ($arCodes as $key => &$value) {
            
            if( ($value[OptionProvider::KEY_DELAYED] == 'Y' || !empty($value[OptionProvider::KEY_TIME])) 
                && !empty($value[OptionProvider::KEY_CODE])
            ){

                $type = 'text/javascript';
                if($value[OptionProvider::KEY_DELAYED] == 'Y'){
                    $type = 'delayed';
                }

                $scriptParams = [];
                $dom = new \DomDocument();
                $dom->loadHTML($value[OptionProvider::KEY_CODE], LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                
                $nodeScript = $dom->getElementsByTagName('script');

                $nodeScript = is_object($nodeScript[0]) ? $nodeScript[0] : false;

                foreach ($nodeScript->attributes as $attr) {
                    $scriptParams[$attr->name] = $attr->value;
                }

                if(array_key_exists('src', $scriptParams)){ // внешний скрипт
                    $value[OptionProvider::KEY_CODE_MODIF] = "var script = document.createElement('script'); script.src = '". $scriptParams['src'] ."'; script.charset = 'UTF-8'; document.getElementsByTagName('body')[0].appendChild(script);";
                
                    // Обрабатываем скрипт css
                    if(!empty($value[OptionProvider::KEY_CSS])){
                        $linkParams = [];
                        $domCss = new \DomDocument();
                        $domCss->loadHTML($value[OptionProvider::KEY_CSS], LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                        $nodeLink = $domCss->getElementsByTagName('link');
                        $nodeLink = is_object($nodeLink[0]) ? $nodeLink[0] : false;
                        foreach ($nodeLink->attributes as $attr) {
                            $linkParams[$attr->name] = $attr->value;
                        }
                        if(!empty($linkParams['href'])){
                            $value[OptionProvider::KEY_CODE_MODIF] .= "var link  = document.createElement('link'); link.rel = 'stylesheet'; link.type = 'text/css'; link.href = '". $linkParams['href'] ."'; document.getElementsByTagName('head')[0].appendChild(link);";
                        }
                    }

                    if(!empty($value[OptionProvider::KEY_TIME])){
                        $value[OptionProvider::KEY_CODE_MODIF] = 'setTimeout(function(){ ' . $value[OptionProvider::KEY_CODE_MODIF] . ' }, '.$value[OptionProvider::KEY_TIME].');';
                    }

                    $nodeScript->removeAttribute('src');
                    $nodeScript->removeAttribute('async');
                    $nodeScript->setAttribute('type', $type);
                    $nodeScript->setAttribute('data-cfasync', 'false');
                    $nodeScript->textContent = $value[OptionProvider::KEY_CODE_MODIF];

                    $value[OptionProvider::KEY_CODE_MODIF] = $dom->saveHTML();

                } elseif( !empty($nodeScript->nodeValue) ) { // встроенный скрипт

                    if(!empty($value[OptionProvider::KEY_TIME])){
                        $nodeScript->nodeValue = 'setTimeout(function(){ ' . $nodeScript->nodeValue . ' }, '.$value[OptionProvider::KEY_TIME].');';
                    } 

                    $nodeScript->setAttribute('type', $type);
                    $nodeScript->setAttribute('data-cfasync', 'false');
                    $value[OptionProvider::KEY_CODE_MODIF] = $dom->saveHTML();

                }

            }
            // endif
        }

        // Группируем скрипты по месту вывода
        foreach ($arCodes as $fragKey => $data) {
            foreach ($data as $_key => $_value) {
                $arResult['SCRIPTS_PLACES'][$data[OptionProvider::KEY_PLACE]][$fragKey][$_key] = $_value;
            }
        }

        return $arResult;
    }

}