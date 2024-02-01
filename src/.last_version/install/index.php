<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Highloadblock as HL;

global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-strlen("/install/index.php"));
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

class imyie_og extends CModule {

    var $MODULE_ID = 'imyie.og';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = 'Y';

    public function imyie_og() {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}

        $this->MODULE_NAME = GetMessage('IMYIE_OG_INSTALL_NAME');
		$this->MODULE_DESCRIPTION = GetMessage('IMYIE_OG_INSTALL_DESCRIPTION');
		$this->PARTNER_NAME = GetMessage('IMYIE_OG_INSTALL_COMPANY_NAME');
        $this->PARTNER_URI  = 'http://marketplace.1c-bitrix.ru/solutions/imyie.og/';
    }

	function InstallDB($install_wizard = true)
	{
		ModuleManager::registerModule($this->MODULE_ID);

		if (Loader::IncludeModule('highloadblock')) {
			$arLangs = [
				'ru' => GetMessage('IMYIE_OG_INSTALL_HL_NAME_RU'),
				'en' => GetMessage('IMYIE_OG_INSTALL_HL_NAME_EN'),
			];

			$result = HL\HighloadBlockTable::add([
				'NAME' => 'ImyieOgBase',
				'TABLE_NAME' => 'imyie_og_base', 
			]);
			if ($result->isSuccess()) {
				$id = $result->getId();
				Option::set($this->MODULE_ID, 'hl_id', $id);
				foreach ($arLangs as $lang => $name){
					HL\HighloadBlockLangTable::add([
						'ID' => $id,
						'LID' => $lang,
						'NAME' => $name
					]);
				}
				
				$this->installFields($id);
			}
		}

		return true;
	}

	function UnInstallDB($arParams = Array())
	{
		ModuleManager::unregisterModule($this->MODULE_ID);

		if (Loader::IncludeModule('highloadblock')) {
			$hlId = Option::get($this->MODULE_ID, 'hl_id', 0);
			if (!empty($hlId)) {
				HL\HighloadBlockTable::delete($hlId);
			}
		}

		return true;
	}

	function InstallEvents()
	{
		$eventManager = EventManager::getInstance();

		$eventManager->registerEventHandler(
			'main',
			'OnProlog',
			$this->MODULE_ID,
			'Imyie\Og\Handlers',
			'OnProlog'
		);

		$eventManager->registerEventHandler(
			'main',
			'OnAdminTabControlBegin',
			$this->MODULE_ID,
			'Imyie\Og\Handlers',
			'OnAdminTabControlBegin'
		);

		return true;
	}

	function UnInstallEvents()
	{
		$eventManager = EventManager::getInstance();

		$eventManager->unRegisterEventHandler(
			'main',
			'OnProlog',
			$this->MODULE_ID,
			'Imyie\Og\Handlers',
			'OnProlog'
		);

		$eventManager->unRegisterEventHandler(
			'main',
			'OnAdminTabControlBegin',
			$this->MODULE_ID,
			'Imyie\Og\Handlers',
			'OnAdminTabControlBegin'
		);

		return true;
	}

	function InstallFiles()
	{
		return true;
	}

	function InstallPublic()
	{
        return true;
	}

	function InstallOptions()
	{
        return true;
	}

	function UnInstallFiles()
	{
		return true;
	}

	function DoInstall()
	{
		global $APPLICATION, $step;

		$this->InstallFiles();
		$this->InstallDB(false);
		$this->InstallEvents();
		$this->InstallPublic();

		return true;
	}

	function DoUninstall()
	{
		global $APPLICATION, $step;

		$this->UnInstallDB();
		$this->UnInstallFiles();
		$this->UnInstallEvents();

		return true;
	}

	function installFields($hlId)
	{
		$object = 'HLBLOCK_'.$hlId;
		
		$arUserFieldDefault = [
			'ENTITY_ID' => $object,
			'USER_TYPE_ID' => 'string',
			'MULTIPLE' => 'N',
			'MANDATORY' => '',
			'LIST_COLUMN_LABEL' => ['ru' => '', 'en' => ''],
			'LIST_FILTER_LABEL' => ['ru' => '', 'en' => ''],
		];

		$arUserFields = [
			'UF_NAME' => [
				'MANDATORY' => 'Y',
				'EDIT_FORM_LABEL' => ['ru' => GetMessage('IMYIE_OG_INSTALL_HL_FIELDS_RU__UF_NAME'), 'en' => 'Rule name'],
			],
			'UF_SITE_ID' => [
				'MANDATORY' => 'Y',
				'EDIT_FORM_LABEL' => ['ru' => GetMessage('IMYIE_OG_INSTALL_HL_FIELDS_RU__SITE_ID'), 'en' => 'Rule site id'],
			],
			'UF_URL' => [
				'MANDATORY' => 'Y',
				'EDIT_FORM_LABEL' => ['ru' => GetMessage('IMYIE_OG_INSTALL_HL_FIELDS_RU__UF_URL'), 'en' => 'Rule url'],
			],
			'UF_OG_TITLE' => [
				'MANDATORY' => 'Y',
				'EDIT_FORM_LABEL' => ['ru' => GetMessage('IMYIE_OG_INSTALL_HL_FIELDS_RU__UF_OG_TITLE'), 'en' => 'Object name'],
			],
			'UF_OG_DESCRIPTION' => [
				'EDIT_FORM_LABEL' => ['ru' => GetMessage('IMYIE_OG_INSTALL_HL_FIELDS_RU__UF_OG_DESCRIPTION'), 'en' => 'Object short description'],
			],
			'UF_OG_IMAGE' => [
				'MANDATORY' => 'Y',
				'USER_TYPE_ID' => 'file',
				'EDIT_FORM_LABEL' => ['ru' => GetMessage('IMYIE_OG_INSTALL_HL_FIELDS_RU__UF_OG_IMAGE'), 'en' => 'Object image'],
				'SETTINGS' => [
					'EXTENSIONS' => 'png,jpg,jpeg',
				],
			],
			'UF_OG_URL' => [
				'MANDATORY' => 'Y',
				'EDIT_FORM_LABEL' => ['ru' => GetMessage('IMYIE_OG_INSTALL_HL_FIELDS_RU__UF_OG_URL'), 'en' => 'Object canonical url'],
			],
			'UF_OG_SITE_NAME' => [
				'EDIT_FORM_LABEL' => ['ru' => GetMessage('IMYIE_OG_INSTALL_HL_FIELDS_RU__UF_OG_SITE_NAME'), 'en' => 'Site name'],
			],
			'UF_OG_LOCALE' => [
				'EDIT_FORM_LABEL' => ['ru' => GetMessage('IMYIE_OG_INSTALL_HL_FIELDS_RU__UF_OG_LOCALE'), 'en' => 'Site locale'],
				'SETTINGS' => [
					'DEFAULT_VALUE' => 'ru_RU',
				],
			],
			'UF_OG_TYPE' => [
				'MANDATORY' => 'Y',
				'EDIT_FORM_LABEL' => ['ru' => GetMessage('IMYIE_OG_INSTALL_HL_FIELDS_RU__UF_OG_TYPE'), 'en' => 'Object type'],
				'SETTINGS' => [
					'DEFAULT_VALUE' => 'website',
				],
			],
		];

		$ob  = new \CUserTypeEntity;
		foreach ($arUserFields as $code => $arUserField) {
			$arFields = array_merge($arUserFieldDefault, $arUserField);
			$arFields['LIST_COLUMN_LABEL'] = $arFields['LIST_FILTER_LABEL'] = $arFields['EDIT_FORM_LABEL'];
			$arFields['FIELD_NAME'] = $arFields['XML_ID'] = $code;
			$id = $ob->Add($arFields);
			unset($arFields);
		}
	}

}
