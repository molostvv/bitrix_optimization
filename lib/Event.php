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
        $oScript = Script::getInstance();
        $extrenalScriptsData = $oScript->getExternalScripts();
        $extrenalScripts = $extrenalScriptsData['SCRIPTS'];

        $head 	   = $extrenalScripts[ OptionProvider::KEY_PLACE_HEAD ];
        $beginBody = $extrenalScripts[ OptionProvider::KEY_PLACE_BEGIN_BODY ];
        $endBody   = $extrenalScripts[ OptionProvider::KEY_PLACE_END_BODY ];

        // Подключаем скрипт с отложенной загрузкой
        if($extrenalScriptsData['DELAYED']){
            $endBody .= $oScript->getDelayedScript();
        }

        if(!empty($head)){
        	$content = str_replace('</head>', '<!-- head -->'. $head . "\n" . '</head>', $content);
        }

        if(!empty($beginBody)){
        	$content = preg_replace('/<body([^>]*)>/is', '<body$1>' . "\n" . '<!-- beginBody -->' . $beginBody, $content);
        }
  
        if(!empty($endBody)){
        	$content = str_replace('</body>', '<!-- endBody -->' . $endBody . "\n" . '</body>', $content);
        }      
        
    }

    /*
    *  Сбрасываем кэш сгенерированных внешних скриптов при изменение парамтеров в админке
    */
    public static function onAfterSetOptionHandler()
    {
        $oScript = Script::getInstance();

        $cache = new \CPHPCache();
        $cache->cleanDir($oScript->cachePath);
    }

}