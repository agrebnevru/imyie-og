<?php

use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Highloadblock as HL;

Loc::loadMessages(__FILE__);

class imyie_og extends CModule
{

    public $MODULE_ID = 'imyie.og';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_CSS;
    public $MODULE_GROUP_RIGHTS = 'Y';

    public function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . "/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage('IMYIE_OG_INSTALL_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('IMYIE_OG_INSTALL_DESCRIPTION');
        $this->PARTNER_NAME = GetMessage('IMYIE_OG_INSTALL_COMPANY_NAME');
        $this->PARTNER_URI = 'https://agrebnev.ru/';
    }

    public function InstallDB($install_wizard = true)
    {
        ModuleManager::registerModule($this->MODULE_ID);

        if (Loader::IncludeModule('highloadblock')) {
            $arLangs = [
                'ru' => GetMessage('IMYIE_OG_INSTALL_HL_NAME_RU'),
                'en' => GetMessage('IMYIE_OG_INSTALL_HL_NAME_EN'),
            ];

            $result = HL\HighloadBlockTable::add(
                [
                    'NAME' => 'ImyieOgBase',
                    'TABLE_NAME' => 'imyie_og_base',
                ]
            );
            if ($result->isSuccess()) {
                $id = $result->getId();
                Option::set($this->MODULE_ID, 'hl_id', $id);
                foreach ($arLangs as $lang => $name) {
                    HL\HighloadBlockLangTable::add(
                        [
                            'ID' => $id,
                            'LID' => $lang,
                            'NAME' => $name
                        ]
                    );
                }

                $this->installFields($id);
            }
        }

        return true;
    }

    public function UnInstallDB($arParams = array())
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

    public function InstallEvents()
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

    public function UnInstallEvents()
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

    public function InstallFiles()
    {
        return true;
    }

    public function InstallPublic()
    {
        return true;
    }

    public function InstallOptions()
    {
        return true;
    }

    public function UnInstallFiles()
    {
        return true;
    }

    public function DoInstall()
    {
        global $APPLICATION, $step;

        $this->InstallFiles();
        $this->InstallDB(false);
        $this->InstallEvents();
        $this->InstallPublic();

        return true;
    }

    public function DoUninstall()
    {
        global $APPLICATION, $step;

        $this->UnInstallDB();
        $this->UnInstallFiles();
        $this->UnInstallEvents();

        return true;
    }

    public function installFields($hlId)
    {
        $object = 'HLBLOCK_' . $hlId;

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
                'EDIT_FORM_LABEL' => [
                    'ru' => GetMessage('IMYIE_OG_INSTALL_HL_FIELDS_RU__UF_NAME'),
                    'en' => 'Rule name'
                ],
            ],
            'UF_SITE_ID' => [
                'MANDATORY' => 'Y',
                'EDIT_FORM_LABEL' => [
                    'ru' => GetMessage('IMYIE_OG_INSTALL_HL_FIELDS_RU__SITE_ID'),
                    'en' => 'Rule site id'
                ],
            ],
            'UF_URL' => [
                'MANDATORY' => 'Y',
                'EDIT_FORM_LABEL' => ['ru' => GetMessage('IMYIE_OG_INSTALL_HL_FIELDS_RU__UF_URL'), 'en' => 'Rule url'],
            ],
            'UF_OG_TITLE' => [
                'MANDATORY' => 'Y',
                'EDIT_FORM_LABEL' => [
                    'ru' => GetMessage('IMYIE_OG_INSTALL_HL_FIELDS_RU__UF_OG_TITLE'),
                    'en' => 'Object name'
                ],
            ],
            'UF_OG_DESCRIPTION' => [
                'EDIT_FORM_LABEL' => [
                    'ru' => GetMessage('IMYIE_OG_INSTALL_HL_FIELDS_RU__UF_OG_DESCRIPTION'),
                    'en' => 'Object short description'
                ],
            ],
            'UF_OG_IMAGE' => [
                'MANDATORY' => 'Y',
                'USER_TYPE_ID' => 'file',
                'EDIT_FORM_LABEL' => [
                    'ru' => GetMessage('IMYIE_OG_INSTALL_HL_FIELDS_RU__UF_OG_IMAGE'),
                    'en' => 'Object image'
                ],
                'SETTINGS' => [
                    'EXTENSIONS' => 'png,jpg,jpeg',
                ],
            ],
            'UF_OG_URL' => [
                'MANDATORY' => 'Y',
                'EDIT_FORM_LABEL' => [
                    'ru' => GetMessage('IMYIE_OG_INSTALL_HL_FIELDS_RU__UF_OG_URL'),
                    'en' => 'Object canonical url'
                ],
            ],
            'UF_OG_SITE_NAME' => [
                'EDIT_FORM_LABEL' => [
                    'ru' => GetMessage('IMYIE_OG_INSTALL_HL_FIELDS_RU__UF_OG_SITE_NAME'),
                    'en' => 'Site name'
                ],
            ],
            'UF_OG_LOCALE' => [
                'EDIT_FORM_LABEL' => [
                    'ru' => GetMessage('IMYIE_OG_INSTALL_HL_FIELDS_RU__UF_OG_LOCALE'),
                    'en' => 'Site locale'
                ],
                'SETTINGS' => [
                    'DEFAULT_VALUE' => 'ru_RU',
                ],
            ],
            'UF_OG_TYPE' => [
                'MANDATORY' => 'Y',
                'EDIT_FORM_LABEL' => [
                    'ru' => GetMessage('IMYIE_OG_INSTALL_HL_FIELDS_RU__UF_OG_TYPE'),
                    'en' => 'Object type'
                ],
                'SETTINGS' => [
                    'DEFAULT_VALUE' => 'website',
                ],
            ],
        ];

        $ob = new \CUserTypeEntity();
        foreach ($arUserFields as $code => $arUserField) {
            $arFields = array_merge($arUserFieldDefault, $arUserField);
            $arFields['LIST_COLUMN_LABEL'] = $arFields['LIST_FILTER_LABEL'] = $arFields['EDIT_FORM_LABEL'];
            $arFields['FIELD_NAME'] = $arFields['XML_ID'] = $code;
            $id = $ob->Add($arFields);
            unset($arFields);
        }
    }

}
