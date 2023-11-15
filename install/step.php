<? 
	if(!check_bitrix_sessid())
		return;
?>

<? echo CAdminMessage::ShowNote('Модуль ' . $MODULE_ID . ' установлен'); ?>