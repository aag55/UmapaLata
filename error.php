<!--

Серверные скрипты. Язык PHP. Общий синтаксис, библиотеки: Генерация страниц на стороне сервера (шапка, контент, футер). Элементы SEO. Структурная парадигма и спагетти. Шаблонизация

Интеграция HTML и  PHP: phpinfo(). Переменные окружения. Отправка заголовков (Last-Modified, Location, Content-Type, коды состояния (403, 404)) , print_r, var_dump, debug_backtrace. Шаблонизация

-->
<?php
/**
 * Пользовательская страница ошибок  
 * 
 * [смотри директиву ErrorDocument в файле /.htaccess] 
 * 
 * Страница иллюстрирует применение антипаттерна "спагетти". Кроме того, что код смешан с разметкой, еще и использованы внедренные и встроенные стили, чтобы сделать сопровождение сущим адом. 
 * Используется альтернативный синтаксис PHP.
 *  
 */
/**
 * @var int $code
 */
//$code = (int) $_SERVER["QUERY_STRING"];
$_SERVER["QUERY_STRING"] != "" ? $code = (int) $_SERVER["QUERY_STRING"] : true;

/*
switch ($code) {
    case 400: $msg = "HTTP/1.1 $code Bad Request";
        break;
    case 403: $msg = "HTTP/1.1 $code Forbidden";
        break;
    case 404: $msg = "HTTP/1.1 $code Not Found";
        break;
}
header($msg);
 * 
 */
?>

<?php if (true): ?>
    <!DOCTYPE html>
    <html lang="ru">
        <head>
            <title>Ошибка <?= $code; ?></title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">

            <link rel="preconnect" href="https://fonts.googleapis.com" />
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
            <link href="https://fonts.googleapis.com/css2?family=Lora:ital@0;1&family=Ubuntu+Condensed&display=swap" rel="stylesheet"> 
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />

            <style>
                body {text-align: center;}
                .wrapper {margin: 0 auto;}
            </style>

        </head>
        <body>
            <div class="wrapper"  style="text-align: center; max-width: 960px;">
                <div style="font-size: 24vw; text-align: center; font-family: 'Ubuntu Condensed', serif; color: #274472;">
    <?= $code; ?>&nbsp;:(
                </div>
                <form action="/search.php">
                    <p>Порыться с пристрастием: </p>
                    <input type="search" name="q" id="query" placeholder ="слова сюда" style="width: 80%;"/>
                    <button><i class="fa fa-search"></i></button>             
                </form>

                <p style="text-align: center;">&#127279;&nbsp;2022,&nbsp;<a href="<?= "$_SERVER[REQUEST_SCHEME]://$_SERVER[SERVER_NAME]/"; ?>" title="home">UMAPALATA</a> &middot; <a href="https://creativecommons.org/licenses/by-sa/4.0/" title="">CC&nbsp;BY-SA</a></p>
            </div>
        </body>
    </html>
<?php else: ?>
    <div style="text-align: center; padding-top: 15%;"><img src="/static/page-is-loading.gif" alt="Be patient, page is loading..." width="50" height="50" /></div>
<?php endif; 