<?
namespace Vspace\Optimization;

class Tools {

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


}