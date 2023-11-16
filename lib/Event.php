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

        $isHeadersPushCss = \COption::GetOptionString(VSPACE_OPT_MODULE_ID, 'headers_push_css');

        if($isHeadersPushCss == 'Y')
            Tools::addPushHeaderCSS($content);
    }
}