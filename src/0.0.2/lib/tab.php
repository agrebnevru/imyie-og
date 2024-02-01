<?php

namespace Imyie\Og;

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Tab
{

    public static function getHlTab(): array
    {
        return [
            'DIV' => 'imyie_og_hl_tab',
            'TAB' => Loc::getMessage('IMYIE_OG_TAB_HL'),
            "ICON" => "main_user_edit",
            'TITLE' => Loc::getMessage('IMYIE_OG_TAB_HL_TITLE'),
            'CONTENT' => '<tr valign="top"><td colspan="2">' . Loc::getMessage('IMYIE_OG_TAB_HL_CONTENT') . '</td></tr>'
        ];
    }

}
