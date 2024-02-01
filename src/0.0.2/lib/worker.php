<?php

namespace Imyie\Og;

use Bitrix\Main\Loader;
use Bitrix\Main\Entity;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Highloadblock as HL;

class Worker
{

    public const CACHE_TIME = (3600 * 24 * 7);

    private static $instance;
    private $arOg = [];

    public function __construct()
    {
    }

    public function exec(): void
    {
        if (false === Loader::includeModule('highloadblock')) {
            return;
        }

        $request = Application::getInstance()->getContext()->getRequest();

        $requestPage = $request->getRequestedPage();
        $hlId = Option::get('imyie.og', 'hl_id', 0);

        if (empty($requestPage) || empty($hlId)) {
            return;
        }

        $hlblock = HL\HighloadBlockTable::getById($hlId)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entityDatClass = $entity->getDataClass();

        $arSizes = [
            'width' => 1200,
            'height' => 1200,
        ];

        $arSelect = [
            'UF_OG_TITLE',
            'UF_OG_DESCRIPTION',
            'UF_OG_IMAGE',
            'UF_OG_URL',
            'UF_OG_SITE_NAME',
            'UF_OG_LOCALE',
            'UF_OG_TYPE',
        ];

        $query = $entityDatClass::query();
        $query->setSelect($arSelect)
            ->setFilter(['UF_SITE_ID' => SITE_ID, 'UF_URL' => $requestPage])
            ->setLimit(1)
            ->setCacheTtl(self::CACHE_TIME);
        $res = $query->exec();
        if ($arRule = $res->fetch()) {
            if (
                empty($arRule['UF_OG_TITLE'])
                || empty($arRule['UF_OG_IMAGE'])
                || empty($arRule['UF_OG_TYPE'])
                || empty($arRule['UF_OG_URL'])
            ) {
                return;
            }

            $arImage = \CFile::ResizeImageGet($arRule['UF_OG_IMAGE'], $arSizes, BX_RESIZE_IMAGE_PROPORTIONAL, true);
            $arImage['src'] = \CHTTP::URN2URI($arImage['src']);

            $arFields = [
                'og:title' => $arRule['UF_OG_TITLE'],
                'og:image' => $arImage['src'],
                'og:image:width' => $arImage['width'],
                'og:image:height' => $arImage['height'],
                'og:url' => $arRule['UF_OG_URL'],
                'og:type' => $arRule['UF_OG_TYPE'],
            ];

            if (!empty($arRule['UF_OG_DESCRIPTION'])) {
                $arFields['og:description'] = $arRule['UF_OG_DESCRIPTION'];
            }

            if (!empty($arRule['UF_OG_SITE_NAME'])) {
                $arFields['og:site_name'] = $arRule['UF_OG_SITE_NAME'];
            }

            if (!empty($arRule['UF_OG_LOCALE'])) {
                $arFields['og:locale'] = $arRule['UF_OG_LOCALE'];
            }

            $this->setOg($arFields);
        }

        $this->addMeta();
    }

    public function setOg(array $data = []): void
    {
        $this->arOg = $data;
    }

    public function getOg(): array
    {
        return $this->arOg;
    }

    public function addMeta(): void
    {
        $data = $this->getOg();

        if (empty($data)) {
            return;
        }

        $asset = Asset::getInstance();

        foreach ($data as $propertyName => $value) {
            if (empty($value)) {
                continue;
            }

            $asset->addString('<meta property="' . $propertyName . '" content="' . $value . '" />', $bUnique = true);
        }
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Worker();
        }

        return self::$instance;
    }

}
