<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Vspace\Optimization\DataProviders\OptionProvider;

Loc::loadMessages(__FILE__);

// получаем идентификатор модуля
$request = HttpApplication::getInstance()->getContext()->getRequest();
$module_id = htmlspecialchars($request['mid'] != '' ? $request['mid'] : $request['id']);
// подключаем наш модуль
Loader::includeModule($module_id);

$keyScriptCode    = OptionProvider::KEY_CODE; 
$keyScriptPlace   = OptionProvider::KEY_PLACE;
$keyScriptDelayed = OptionProvider::KEY_DELAYED;
$keyScriptTime    = OptionProvider::KEY_TIME;
$keyScriptCss     = OptionProvider::KEY_CSS;

$arPlaces = [
    OptionProvider::KEY_PLACE_HEAD        => '<head>',
    OptionProvider::KEY_PLACE_BEGIN_BODY  => 'после <body>',
    OptionProvider::KEY_PLACE_END_BODY    => 'перед </body>'
];

/*
 * Параметры модуля со значениями по умолчанию
 */
$aTabs = array(
    // 0 => array(
    //     /*
    //      * Вкладка «Основные настройки»
    //      */
    //     'DIV'     => 'edit1',
    //     'TAB'     => Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_GENERAL'),
    //     'TITLE'   => Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_GENERAL'),
    //     'OPTIONS' => array(
    //         // push css
    //         Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_HEADERS'),
    //         array(
    //             'headers_push_css',
    //             Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_PUSH_CSS'),
    //             '',
    //             array('checkbox')
    //         ),
    //     )
    // ),
    0 => array(
        /*
         * Вкладка «Внешние скрипты»
         */
        'DIV'     => 'edit2',
        'TAB'     => Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_EXTERNAL_SCRIPTS'),
        'TITLE'   => Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_EXTERNAL_SCRIPTS_R'),
        'OPTIONS' => array(
            // Общие настройки для внешних скриптов
            Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_EXTERNAL_SCRIPTS_GENERAL'),
           
            // Rocket Loader Off
            // array(
            //     'rocket_loader',
            //     Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_EXTERNAL_SCRIPTS_RL'),
            //     'Y',
            //     array('checkbox')
            // ),

            ['note' => "При включенние отложенной загрузки к скрипту будет добавлен атрибут data-cfasync='false' который игнорирует выполнение скрипта Rocket Loader'ом"],
            
            // Метрика
            Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_EXTERNAL_SCRIPTS_YM'),
            array(
                $keyScriptCode . '_ym',
                Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_EXTERNAL_SCRIPTS_YM_VALUE'),
                '',
                array('textarea', 12, 60)
            ),
            array(
                $keyScriptPlace .'_ym',
                Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_EXTERNAL_SCRIPTS_PLACE'),
                '',
                array('selectbox', $arPlaces)
            ),
            array(
                $keyScriptDelayed . '_ym',
                Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_EXTERNAL_SCRIPTS_DELAYED'),
                '',
                array('checkbox')
            ),
            array(
                $keyScriptTime . '_ym',
                Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_EXTERNAL_SCRIPTS_TIME'),
                '',
                array('text', 5)
            ),

            // Google Tag Manager
            Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_EXTERNAL_SCRIPTS_GTM'),
            array(
                $keyScriptCode . '_gtm',
                Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_EXTERNAL_SCRIPTS_GTM_VALUE'),
                '',
                array('textarea', 12, 60)
            ),
            array(
                $keyScriptPlace . '_gtm',
                Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_EXTERNAL_SCRIPTS_PLACE'),
                '',
                array('selectbox', $arPlaces)
            ),
            array(
                $keyScriptDelayed . '_gtm',
                Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_EXTERNAL_SCRIPTS_DELAYED'),
                '',
                array('checkbox')
            ),
            array(
                $keyScriptTime . '_gtm',
                Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_EXTERNAL_SCRIPTS_TIME'),
                '',
                array('text', 5)
            )
        )
    ),
);

for($i = 1; $i < 6; $i++){
    // Внешние скрипты список
    $aTabs[0]["OPTIONS"][] = Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_EXTERNAL_SCRIPTS_ITEM') . ' ' . $i;

    $aTabs[0]["OPTIONS"][] = [
        $keyScriptCode . '_' . $i,
        Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_EXTERNAL_SCRIPTS_CODE'),
        '',
        array('text', 60)
    ];

    if($i == 1){
        $aTabs[0]["OPTIONS"][] = [
            $keyScriptCss . '_' . $i,
            Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_EXTERNAL_SCRIPTS_CSS'),
            '',
            array('text', 60)
        ];
    }

    $aTabs[0]["OPTIONS"][] = [
        $keyScriptPlace . '_' .  $i,
        Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_EXTERNAL_SCRIPTS_PLACE'),
        '',
        array('selectbox', $arPlaces)
    ];

    $aTabs[0]["OPTIONS"][] = [
        $keyScriptDelayed . '_' . $i,
        Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_EXTERNAL_SCRIPTS_DELAYED'),
        '',
        array('checkbox')
    ];

    $aTabs[0]["OPTIONS"][] = [
        $keyScriptTime . '_' . $i,
        Loc::getMessage('VSPACE_OPT_OPTIONS_TAB_EXTERNAL_SCRIPTS_TIME'),
        '',
        array('text', 5)
    ];
}

/*
 * Создаем форму для редактирования параметров модуля
 */
$tabControl = new CAdminTabControl(
    'tabControl',
    $aTabs
);

$tabControl->begin();
?>
<form action="<?= $APPLICATION->getCurPage(); ?>?mid=<?=$module_id; ?>&lang=<?= LANGUAGE_ID; ?>" method="post">
    <?= bitrix_sessid_post(); ?>
    <?php
    foreach ($aTabs as $aTab) { // цикл по вкладкам
        if ($aTab['OPTIONS']) {
            $tabControl->beginNextTab();
            __AdmSettingsDrawList($module_id, $aTab['OPTIONS']);
        }
    }
    $tabControl->buttons();
    ?>
    <input type="submit" name="apply" 
           value="<?= Loc::GetMessage('VSPACE_OPT_OPTIONS_INPUT_APPLY'); ?>" class="adm-btn-save" />
</form>

<?php
$tabControl->end();

/*
 * Обрабатываем данные после отправки формы
 */
if ($request->isPost() && check_bitrix_sessid()) {

    foreach ($aTabs as $aTab) { // цикл по вкладкам
        foreach ($aTab['OPTIONS'] as $arOption) {
            if (!is_array($arOption)) { // если это название секции
                continue;
            }
            if ($arOption['note']) { // если это примечание
                continue;
            }
            if ($request['apply']) { // сохраняем введенные настройки
                $optionValue = $request->getPost($arOption[0]);
                Option::set($module_id, $arOption[0], is_array($optionValue) ? implode(',', $optionValue) : $optionValue);
            }
        }
    }

    LocalRedirect($APPLICATION->getCurPage().'?mid='.$module_id.'&lang='.LANGUAGE_ID);

}
?>