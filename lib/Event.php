<?
namespace Vspace\Optimization;

use Bitrix\Main\Context;
use Vspace\Optimization\Tools;

class Event {

    /**
     * Вызывается при выводе буферизированного контента.
     * @param $content
     */
    public static function OnEndBufferContent(&$content){

    	// Обработка и вставка внешних скриптов
    	Tools::externalScripts();

        $isHeadersPushCss = \COption::GetOptionString(VSPACE_OPT_MODULE_ID, 'headers_push_css');

        if($isHeadersPushCss == 'Y')
            Tools::addPushHeaderCSS($content);


        // Проверка, если нет кода получения кода в шаблоне, то вставить спомощью замены.
        $head 	   = '<!-- head -->';
        $beginBody = '<!-- beginBody -->';
        $endBody   = '<!-- endBody -->';

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