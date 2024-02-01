<?php

$MESS['IMYIE_OG_TAB_HL'] = 'Инструкция';
$MESS['IMYIE_OG_TAB_HL_TITLE'] = 'Краткая справочная информация';
$MESS['IMYIE_OG_TAB_HL_CONTENT'] = '
<strong>Название правила</strong> - Просто название по которому вы будете ориентироваться.<br>
<strong>ID сайта</strong> - ID сайта, на котором будет использоваться правило (обычно s1). Идентификаторы сайтов можно увидеть <a href="/bitrix/admin/site_admin.php" target="_blank">тут</a>.<br>
<strong>URL страницы правила</strong> - Страница, в код которой будут добавлены og-теги этого правила <sup style="color:red;">1</sup>.<br>
<strong>Название объекта</strong> - <i>og:title</i> &mdash; название объекта.<br>
<strong>Краткое описание объекта</strong> - <i>og:description</i> &mdash; краткое описание объекта.<br>
<strong>Изображение, описывающее объект</strong> - <i>og:image</i> &mdash; URL изображения, описывающего объект.<br>
<strong>Канонический URL объекта</strong> - <i>og:url</i> &mdash; канонический URL объекта, который будет использован в качестве постоянного идентификатора.<br>
<strong>Название сайта</strong> - <i>og:site_name</i> &mdash; название вашего сайта. Если у вас один сайт, то для удобства вы можете установить значение по умолчанию. в настройках поля.<br>
<strong>Язык сайта</strong> - <i>og:locale</i> &mdash; язык описания объекта в формате <i>язык_страна</i> (например ru_RU или en_US).<br>
<strong>Тип объекта</strong> - <i>og:type</i> &mdash; тип объекта, например, video.movie (фильм). Если у вас несколько объектов на странице, выберите один из них (главный). В зависимости от типа можно указать дополнительные свойства.<br>
<br>
<hr>
<br>
<strong style="color:red;">1</strong> &mdash; путь всегда должен содержать имя исполняемого файла в конце (обычно index.php).<br>
Примеры <u>URL страницы правила</u>:
<ul>
    <li><i>/index.php</i> &mdash; главная страница сайта;</li>
    <li><i>/catalog/shoes/index.php</i> &mdash; раздел каталога товаров;</li>
    <li><i>/catalog/shoes/slippers-pink-paradise/index.php</i> &mdash; детальная страница товара;</li>
    <li><i>/news/index.php</i> &mdash; раздел с новостями;</li>
    <li><i>/news/waterproof_connection/index.php</i> &mdash; детальная страница новости;</li>
</ul>
Url с параметрами не поддерживаются.<br>
<br>
<hr>
<br>
Получить подробную информацию по og-микроразметке вы можете, например, у <a href="https://yandex.ru/support/webmaster/open-graph/intro-open-graph.html" target="_blank">Яндекс</a> или <a href="https://ogp.me/" target="_blank">на официальном сайте (англ)</a>.<br>
';
