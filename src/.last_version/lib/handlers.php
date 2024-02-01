<?php

namespace Imyie\Og;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;

class Handlers
{

    public function OnProlog(): void
    {
        if (!defined('SITE_ID') || SITE_ID == false) {
            return;
        }

        $request = Application::getInstance()->getContext()->getRequest();

        if ($request->isAdminSection() == true) {
            return;
        }

        Worker::getInstance()->exec();
    }

    public function OnAdminTabControlBegin(&$form): void
    {
        $request = Application::getInstance()->getContext()->getRequest();

        $requestPage = $request->getRequestedPage();
        $hlId = Option::get('imyie.og', 'hl_id', 0);

        if (
            $request->isAdminSection() == true
            && $requestPage == '/bitrix/admin/highloadblock_row_edit.php'
            && $request->getQuery('ENTITY_ID') == $hlId
        ) {
            $form->tabs[] = Tab::getHlTab();
        }
    }

}
