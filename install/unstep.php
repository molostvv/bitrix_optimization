<?	
	if(!check_bitrix_sessid())
		return;
?>

<? echo CAdminMessage::ShowNote('Модуль ' . $this->MODULE_ID . ' успешно удален из системы'); ?>