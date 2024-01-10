<?
namespace Vspace\Optimization;

use Bitrix\Main\Context;
use Vspace\Optimization\Tools;
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

    	// Обработка и вставка внешних скриптов
    	Tools::externalScripts();

        $isHeadersPushCss = \COption::GetOptionString(VSPACE_OPT_MODULE_ID, 'headers_push_css');

        if($isHeadersPushCss == 'Y')
            Tools::addPushHeaderCSS($content);


        // Проверка, если нет кода получения кода в шаблоне, то вставить спомощью замены.
        $head 	   = '<!-- head -->'      . Tools::$_optionData[ OptionProvider::KEY_PLACE_HEAD ];
        $beginBody = '<!-- beginBody -->' . Tools::$_optionData[ OptionProvider::KEY_PLACE_BEGIN_BODY ];
        $endBody   = '<!-- endBody -->'   . Tools::$_optionData[ OptionProvider::KEY_PLACE_END_BODY ];

        // Подключаем скрипт с отложенной загрузкой
        if(Tools::$_optionScriptDelayed){
            $endBody .= Tools::getDelayedScript();
        }

        if(!\Vspace\Optimization\Tools::isInsertedHead()){
        	$content = str_replace('</head>', $head . "\n" . '</head>', $content);
        }

        if(!\Vspace\Optimization\Tools::isInsertedBeginBody()){
        	$content = preg_replace('/<body([^>]*)>/is', '<body$1>' . "\n" . $beginBody, $content);
        }
  
        if(!\Vspace\Optimization\Tools::isInsertedEndBody()){
        	$content = str_replace('</body>', $endBody . "\n" . '</body>', $content);
        }      
        
    }
}