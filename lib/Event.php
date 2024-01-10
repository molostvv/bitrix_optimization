<?
namespace Vspace\Optimization;

use Bitrix\Main\Context;
use Vspace\Optimization\Tools;
use Vspace\Optimization\Script;
use Bitrix\Main\Config\Option;
use Vspace\Optimization\DataProviders\OptionProvider;

class Event {

    /**
     * Вызывается при выводе буферизированного контента.
     * @param $content
     */
    public static function OnEndBufferContent(&$content){

        global $APPLICATION;

        // Не выполнять, если административная часть
        if(strpos($APPLICATION->GetCurPage(), "/bitrix/") !== false)
            return;

        $isHeadersPushCss = \COption::GetOptionString(VSPACE_OPT_MODULE_ID, 'headers_push_css');

        if($isHeadersPushCss == 'Y')
            Tools::addPushHeaderCSS($content);

        // Обработка и вставка внешних скриптов
        Script::externalScripts();


        // Проверка, если нет кода получения кода в шаблоне, то вставить спомощью замены.
        $head 	   = '<!-- head -->'      . Script::$_optionData[ OptionProvider::KEY_PLACE_HEAD ];
        $beginBody = '<!-- beginBody -->' . Script::$_optionData[ OptionProvider::KEY_PLACE_BEGIN_BODY ];
        $endBody   = '<!-- endBody -->'   . Script::$_optionData[ OptionProvider::KEY_PLACE_END_BODY ];

        // Подключаем скрипт с отложенной загрузкой
        if(Script::$_optionScriptDelayed){
            $endBody .= Script::getDelayedScript();
        }

        if(!Script::isInsertedHead()){
        	$content = str_replace('</head>', $head . "\n" . '</head>', $content);
        }

        if(!Script::isInsertedBeginBody()){
        	$content = preg_replace('/<body([^>]*)>/is', '<body$1>' . "\n" . $beginBody, $content);
        }
  
        if(!Script::isInsertedEndBody()){
        	$content = str_replace('</body>', $endBody . "\n" . '</body>', $content);
        }      
        
    }

    /*
    *  Сбрасываем кэш сгенерированных внешних скриптов при изменение парамтеров в админке
    */
    public static function onAfterSetOptionHandler()
    {
        $cache = new \CPHPCache();
        $cache->cleanDir(Script::$cachePath);
    }

}