<?
namespace Vspace\Optimization;

use Bitrix\Main\Context;
use Vspace\Optimization\DataProviders\OptionProvider;

class Tools {

    static private $_isInsertedHead;
    static private $_isInsertedBeginBody;
    static private $_isInsertedEndBody;

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
        var_dump($oProvider->getOptions());


    }

    /**
    *  Вставка кода в <head>
    */
    public static function insertHead(){
        self::$_isInsertedHead = true;
        echo '<!-- insertHead -->' . "\n";
    }

    /**
    *  Вставка кода после <body>
    */
    public static function insertBeginBody(){
        self::$_isInsertedBeginBody = true;
        echo '<!-- insertBeginBody -->' . "\n";
    }

    /**
    *  Вставка кода перед </body>
    */
    public static function insertEndBody(){
        self::$_isInsertedEndBody = true;
        echo '<!-- insertEndBody -->' . "\n";
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


}