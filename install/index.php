<?
use \Bitrix\Main\Localization\Loc;

Class Vspace_optimization extends CModule
{

    var $MODULE_ID = "vspace.optimization";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_FOLDER = "local";

    public function __construct()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if(strpos($mystring, $this->MODULE_FOLDER) !== false)
            $this->MODULE_FOLDER = "bitrix";


        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
            $this->MODULE_VERSION      = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME        = Loc::getMessage('VSPACE_OPT_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('VSPACE_OPT_MODULE_DESCRIPTION');
    }

    function uninstallDB(){
        global $APPLICATION, $DB;

        $this->errors = false;
        $this->errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"] . '/' . $this->MODULE_FOLDER . '/modules/' . $this->MODULE_ID . '/install/db/uninstall.sql');
        
        if($this->errors !== false){
            $APPLICATION->ThrowException(implode("", $this->errors));
            return false;
        }

        return;
    }

    function DoInstall()
    {
        global $APPLICATION;
        //$this->InstallFiles();
        //$this->installDB();
        RegisterModule($this->MODULE_ID);
 
        RegisterModuleDependences("main", "OnEndBufferContent", $this->MODULE_ID, "\Vspace\Optimization\Tools", "addPushHeaderCSS");

        $pageTitle = Loc::getMessage("VSPACE_OPT_MODULE_INSTALL") . ' ' . $this->MODULE_ID;
        $APPLICATION->IncludeAdminFile($pageTitle, $_SERVER["DOCUMENT_ROOT"] . '/' . $this->MODULE_FOLDER . '/modules/' . $this->MODULE_ID . '/install/step.php');
    }

    function DoUninstall()
    {
        global $APPLICATION;
        //$this->UnInstallFiles();
       // $this->uninstallDB();
        UnRegisterModule($this->MODULE_ID);

        UnRegisterModuleDependences("main", "OnEndBufferContent", $this->MODULE_ID, "\Vspace\Optimization\Tools", "addPushHeaderCSS");

        $pageTitle = Loc::getMessage("VSPACE_OPT_MODULE_UNINSTALL") . ' ' . $this->MODULE_ID;
        $APPLICATION->IncludeAdminFile($pageTitle, $_SERVER["DOCUMENT_ROOT"] . '/' . $this->MODULE_FOLDER . '/modules/' . $this->MODULE_ID . '/install/unstep.php');
    }

}

?>