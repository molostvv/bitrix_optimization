<?
namespace Vspace\Optimization;

use Bitrix\Main\Context;
use Vspace\Optimization\DataProviders\OptionProvider;

class Tools {

    static private $_isInsertedHead;
    static private $_isInsertedBeginBody;
    static private $_isInsertedEndBody;

    static public $_optionData;
    static public $_optionScriptDelayed;

    /**
     * Добавление push заголовков для сервера
     * @param $content
     */
    public static function addPushHeaderCSS(&$content){

        global $APPLICATION;
        $strings =  \Bitrix\Main\Page\Asset::getInstance()->getStrings();
        $strings .= $APPLICATION->GetCSS(false);

        preg_match_all('/<link.*?href=["\'](.*?)["\'].*?>/i', $strings, $matches);

        $arPushCss = [];

        if(!empty($matches[1])){
            $fragLinks = ['page_', 'template_'];
            foreach ($matches[1] as $link) {
                foreach ($fragLinks as $frag) {
                    if(strpos($link, $frag) !== false){
                        $arPushCss[] = $link;
                    }
                }
            }
        }

        $pushHeaderContent = "";
        foreach ($arPushCss as $link) {
            $pushHeaderContent .= '<' . $link . '>; rel=preload; as=style; type="text/css", ';
        }

        $pushHeaderContent = rtrim($pushHeaderContent, ", ");
        header("Link: " . $pushHeaderContent, false);
    }

    public static function externalScripts(){

        $oProvider = new OptionProvider();
        $optionData = $oProvider->getOptions();

        foreach ($optionData['SCRIPTS_PLACES'] as $place => $scripts) {

            $content = "\n";
            foreach ($scripts as $scriptData) {
                if(!empty($scriptData[OptionProvider::KEY_CODE_MODIF])){
                    $content .= $scriptData[OptionProvider::KEY_CODE_MODIF] . "\n";
                } elseif(!empty($scriptData[OptionProvider::KEY_CODE])){
                    $content .= $scriptData[OptionProvider::KEY_CODE] . "\n";
                }
            }

            self::$_optionData[$place] = $content;
            self::$_optionScriptDelayed = $optionData['DELAYED'];
        }  


    }

    /**
    *  Вставка кода в <head>
    */
    public static function insertHead(){
        self::$_isInsertedHead = true;
        echo '<!-- insertHead -->' . "\n" . Tools::$_optionData[ OptionProvider::KEY_PLACE_HEAD ] . "\n";
    }

    /**
    *  Вставка кода после <body>
    */
    public static function insertBeginBody(){
        self::$_isInsertedBeginBody = true;
        echo '<!-- insertBeginBody -->' . "\n" . Tools::$_optionData[ OptionProvider::KEY_PLACE_BEGIN_BODY ] . "\n";
    }

    /**
    *  Вставка кода перед </body>
    */
    public static function insertEndBody(){
        self::$_isInsertedEndBody = true;
        echo '<!-- insertEndBody -->' . "\n" . Tools::$_optionData[ OptionProvider::KEY_PLACE_END_BODY ] . "\n";
    }
    

    /**
    *  Проверка вставки кода в <head>
    */
    public static function isInsertedHead(){
        if(empty(self::$_isInsertedHead)){
            return false;
        } return true;
    }

    /**
    *  Проверка вставки кода после <body>
    */
    public static function isInsertedBeginBody(){
        if(empty(self::$_isInsertedBeginBody)){
            return false;
        } return true;
    }

    /**
    *  Проверка вставки кода перед </body>
    */
    public static function isInsertedEndBody(){
        if(empty(self::$_isInsertedEndBody)){
            return false;
        } return true;
    }

    /**
    *  Возвращает скрипт отложенной загрузки
    */
    public static function getDelayedScript(){
        $script = '<script type="text/javascript" id="delayed-scripts-js">';
            
            $delay_click = "false";
            $timeout = 0;

            $script.= 'const pmDelayClick=' . $delay_click . ';';
            if(!empty($timeout)) {
                $script.= 'const pmDelayTimer=setTimeout(pmTriggerDOMListener,' . $timeout . '*1000);';
            }
            $script.= 'const pmUserInteractions=["keydown","mousedown","mousemove","wheel","touchmove","touchstart","touchend"],pmDelayedScripts={normal:[],defer:[],async:[]},jQueriesArray=[],pmInterceptedClicks=[];var pmDOMLoaded=!1,pmClickTarget="";function pmTriggerDOMListener(){"undefined"!=typeof pmDelayTimer&&clearTimeout(pmDelayTimer),pmUserInteractions.forEach(function(e){window.removeEventListener(e,pmTriggerDOMListener,{passive:!0})}),document.removeEventListener("visibilitychange",pmTriggerDOMListener),"loading"===document.readyState?document.addEventListener("DOMContentLoaded",pmTriggerDelayedScripts):pmTriggerDelayedScripts()}async function pmTriggerDelayedScripts(){pmDelayEventListeners(),pmDelayJQueryReady(),pmProcessDocumentWrite(),pmSortDelayedScripts(),pmPreloadDelayedScripts(),await pmLoadDelayedScripts(pmDelayedScripts.normal),await pmLoadDelayedScripts(pmDelayedScripts.defer),await pmLoadDelayedScripts(pmDelayedScripts.async),await pmTriggerEventListeners(),document.querySelectorAll("link[data-pmdelayedstyle]").forEach(function(e){e.setAttribute("href",e.getAttribute("data-pmdelayedstyle"))}),window.dispatchEvent(new Event("perfmatters-allScriptsLoaded")),pmReplayClicks()}function pmDelayEventListeners(){let e={};function t(t,r){function n(r){return e[t].delayedEvents.indexOf(r)>=0?"perfmatters-"+r:r}e[t]||(e[t]={originalFunctions:{add:t.addEventListener,remove:t.removeEventListener},delayedEvents:[]},t.addEventListener=function(){arguments[0]=n(arguments[0]),e[t].originalFunctions.add.apply(t,arguments)},t.removeEventListener=function(){arguments[0]=n(arguments[0]),e[t].originalFunctions.remove.apply(t,arguments)}),e[t].delayedEvents.push(r)}function r(e,t){let r=e[t];Object.defineProperty(e,t,{get:r||function(){},set:function(r){e["perfmatters"+t]=r}})}t(document,"DOMContentLoaded"),t(window,"DOMContentLoaded"),t(window,"load"),t(window,"pageshow"),t(document,"readystatechange"),r(document,"onreadystatechange"),r(window,"onload"),r(window,"onpageshow")}function pmDelayJQueryReady(){let e=window.jQuery;Object.defineProperty(window,"jQuery",{get:()=>e,set(t){if(t&&t.fn&&!jQueriesArray.includes(t)){t.fn.ready=t.fn.init.prototype.ready=function(e){pmDOMLoaded?e.bind(document)(t):document.addEventListener("perfmatters-DOMContentLoaded",function(){e.bind(document)(t)})};let r=t.fn.on;t.fn.on=t.fn.init.prototype.on=function(){if(this[0]===window){function e(e){return e=(e=(e=e.split(" ")).map(function(e){return"load"===e||0===e.indexOf("load.")?"perfmatters-jquery-load":e})).join(" ")}"string"==typeof arguments[0]||arguments[0]instanceof String?arguments[0]=e(arguments[0]):"object"==typeof arguments[0]&&Object.keys(arguments[0]).forEach(function(t){delete Object.assign(arguments[0],{[e(t)]:arguments[0][t]})[t]})}return r.apply(this,arguments),this},jQueriesArray.push(t)}e=t}})}function pmProcessDocumentWrite(){let e=new Map;document.write=document.writeln=function(t){var r=document.currentScript,n=document.createRange();let a=e.get(r);void 0===a&&(a=r.nextSibling,e.set(r,a));var i=document.createDocumentFragment();n.setStart(i,0),i.appendChild(n.createContextualFragment(t)),r.parentElement.insertBefore(i,a)}}function pmSortDelayedScripts(){document.querySelectorAll("script[type=delayed]").forEach(function(e){e.hasAttribute("src")?e.hasAttribute("defer")&&!1!==e.defer?pmDelayedScripts.defer.push(e):e.hasAttribute("async")&&!1!==e.async?pmDelayedScripts.async.push(e):pmDelayedScripts.normal.push(e):pmDelayedScripts.normal.push(e)})}function pmPreloadDelayedScripts(){var e=document.createDocumentFragment();[...pmDelayedScripts.normal,...pmDelayedScripts.defer,...pmDelayedScripts.async].forEach(function(t){var r=t.getAttribute("src");if(r){var n=document.createElement("link");n.href=r,n.rel="preload",n.as="script",e.appendChild(n)}}),document.head.appendChild(e)}async function pmLoadDelayedScripts(e){var t=e.shift();return t?(await pmReplaceScript(t),pmLoadDelayedScripts(e)):Promise.resolve()}async function pmReplaceScript(e){return await pmNextFrame(),new Promise(function(t){let r=document.createElement("script");[...e.attributes].forEach(function(e){let t=e.nodeName;"type"!==t&&("data-type"===t&&(t="type"),r.setAttribute(t,e.nodeValue))}),e.hasAttribute("src")?(r.addEventListener("load",t),r.addEventListener("error",t)):(r.text=e.text,t()),e.parentNode.replaceChild(r,e)})}async function pmTriggerEventListeners(){pmDOMLoaded=!0,await pmNextFrame(),document.dispatchEvent(new Event("perfmatters-DOMContentLoaded")),await pmNextFrame(),window.dispatchEvent(new Event("perfmatters-DOMContentLoaded")),await pmNextFrame(),document.dispatchEvent(new Event("perfmatters-readystatechange")),await pmNextFrame(),document.perfmattersonreadystatechange&&document.perfmattersonreadystatechange(),await pmNextFrame(),window.dispatchEvent(new Event("perfmatters-load")),await pmNextFrame(),window.perfmattersonload&&window.perfmattersonload(),await pmNextFrame(),jQueriesArray.forEach(function(e){e(window).trigger("perfmatters-jquery-load")});let e=new Event("perfmatters-pageshow");e.persisted=window.pmPersisted,window.dispatchEvent(e),await pmNextFrame(),window.perfmattersonpageshow&&window.perfmattersonpageshow({persisted:window.pmPersisted})}async function pmNextFrame(){return new Promise(function(e){requestAnimationFrame(e)})}function pmClickHandler(e){e.target.removeEventListener("click",pmClickHandler),pmRenameDOMAttribute(e.target,"pm-onclick","onclick"),pmInterceptedClicks.push(e),e.preventDefault(),e.stopPropagation(),e.stopImmediatePropagation()}function pmReplayClicks(){window.removeEventListener("touchstart",pmTouchStartHandler,{passive:!0}),window.removeEventListener("mousedown",pmTouchStartHandler),pmInterceptedClicks.forEach(e=>{e.target.outerHTML===pmClickTarget&&e.target.dispatchEvent(new MouseEvent("click",{view:e.view,bubbles:!0,cancelable:!0}))})}function pmTouchStartHandler(e){"HTML"!==e.target.tagName&&(pmClickTarget||(pmClickTarget=e.target.outerHTML),window.addEventListener("touchend",pmTouchEndHandler),window.addEventListener("mouseup",pmTouchEndHandler),window.addEventListener("touchmove",pmTouchMoveHandler,{passive:!0}),window.addEventListener("mousemove",pmTouchMoveHandler),e.target.addEventListener("click",pmClickHandler),pmRenameDOMAttribute(e.target,"onclick","pm-onclick"))}function pmTouchMoveHandler(e){window.removeEventListener("touchend",pmTouchEndHandler),window.removeEventListener("mouseup",pmTouchEndHandler),window.removeEventListener("touchmove",pmTouchMoveHandler,{passive:!0}),window.removeEventListener("mousemove",pmTouchMoveHandler),e.target.removeEventListener("click",pmClickHandler),pmRenameDOMAttribute(e.target,"pm-onclick","onclick")}function pmTouchEndHandler(e){window.removeEventListener("touchend",pmTouchEndHandler),window.removeEventListener("mouseup",pmTouchEndHandler),window.removeEventListener("touchmove",pmTouchMoveHandler,{passive:!0}),window.removeEventListener("mousemove",pmTouchMoveHandler)}function pmRenameDOMAttribute(e,t,r){e.hasAttribute&&e.hasAttribute(t)&&(event.target.setAttribute(r,event.target.getAttribute(t)),event.target.removeAttribute(t))}window.addEventListener("pageshow",e=>{window.pmPersisted=e.persisted}),pmUserInteractions.forEach(function(e){window.addEventListener(e,pmTriggerDOMListener,{passive:!0})}),pmDelayClick&&(window.addEventListener("touchstart",pmTouchStartHandler,{passive:!0}),window.addEventListener("mousedown",pmTouchStartHandler)),document.addEventListener("visibilitychange",pmTriggerDOMListener);';


        $script.= '</script>';

        return $script;
    }



}