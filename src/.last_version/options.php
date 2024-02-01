<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Highloadblock as HL;

Loc::loadMessages(__FILE__);

$request = Application::getInstance()->getContext()->getRequest();

$hlId = Option::get('imyie.og', 'hl_id', 0);

if (Loader::includeModule('highloadblock') && $request->getQuery('clear_hl_cache') == 'Y') {
	$hlblock = HL\HighloadBlockTable::getById($hlId)->fetch();
	HL\HighloadBlockTable::compileEntity($hlblock)->cleanCache();
	LocalRedirect('settings.php?mid='.$mid.'&lang='.LANG);
}

$aTabs = [];
$aTabs[] = [
	'DIV' => 'imyie_og_tab_settings',
	'TAB' => Loc::getMessage('IMYIE_TAB_NAME'),
	'ICON' => '',
	'TITLE' => Loc::getMessage('IMYIE_TAB_TITLE'),
];

$arAllOptions = [];

/************************ tab ***************************/
$arAllOptions['imyie_og_tab_settings'][] = [
	'',
	Loc::getMessage('IMYIE_OPTIONS_DATA_MANAGE'),
	'<a href="/bitrix/admin/highloadblock_rows_list.php?ENTITY_ID='.$hlId.'" target="_blank">'.Loc::getMessage('IMYIE_OPTIONS_DATA_MANAGE_LINK').'</a>',
	['statichtml']
];
$arAllOptions['imyie_og_tab_settings'][] = [
	'',
	Loc::getMessage('IMYIE_OPTIONS_CLEAR_CACHE'),
	'<a href="settings.php?mid='.$mid.'&clear_hl_cache=Y">'.Loc::getMessage('IMYIE_OPTIONS_CLEAR_CACHE_LINK').'</a><br>'.Loc::getMessage('IMYIE_OPTIONS_CLEAR_CACHE_NOTE'),
	['statichtml']
];
$arAllOptions['imyie_og_tab_settings'][] = [
	'hl_id',
	Loc::getMessage('IMYIE_OPTIONS_HL_ID'),
	$hlId,
	['text'],
	'Y'
];
// $arAllOptions['imyie_og_tab_settings'][] = array(
// 	'where_show',
// 	Loc::getMessage('IMYIE_OPTIONS_WHERE_SHOW'),
// 	'only_main',	
// 	[
// 		'selectbox',
// 		[
// 			'only_main' => Loc::getMessage('IMYIE_OPTIONS_WHERE_SHOW__ONLY_MAIN'),
// 			'repeat_inside' => Loc::getMessage('IMYIE_OPTIONS_WHERE_SHOW__REPEAT_INSIDE'),
// 		]
// 	],
// );
// $arAllOptions['imyie_og_tab_settings'][] = [
// 	'record_id_for_main',
// 	Loc::getMessage('IMYIE_OPTIONS_RECORD_ID_FOR_MAIN'),
// 	null,
// 	['text']
// ];
/************************ tab ***************************/

if (
	(isset($_REQUEST['save']) || isset($_REQUEST['apply']))
	&& check_bitrix_sessid()
) {
	__AdmSettingsSaveOptions($mid, $arAllOptions['imyie_og_tab_settings']);

    LocalRedirect('settings.php?mid='.$mid.'&lang='.LANG);
}

$tabControl = new \CAdminTabControl('tabControl', $aTabs);

?><form method="post" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>"><?
	echo bitrix_sessid_post();

	$tabControl->Begin();

	$tabControl->BeginNextTab();
	__AdmSettingsDrawList($mid, $arAllOptions['imyie_og_tab_settings']);

	$tabControl->Buttons([]);
	$tabControl->End();

?></form>
