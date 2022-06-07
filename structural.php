<?php

/**
 *  Императивный (процедурный) стиль + шаблоны + немного спагетти
 * 
 * Как это работает: 
 * 1. Изначально пустой массив $content передается по ссылке из одной процедуры в другую, где последовательно заполняется необходимыми данными (основное содержимое, список категорий, список новых статей, список популярных статей).  
 * 2. Готовый ассоциативный массив данных в виде пар "__ПЛАШКАМАКЕТА__=>содержимое" + файл макета страницы ($template) обрабатываются функцией strtr, которая заменяет плашки макета на реальное содержимое.
 * 3. Сформированная страница возвращается клиенту
 *  
 * Здесь и далее: Макет (layout) <- Плашки/Блоки (patterns, blocks, placeholders)
 */

require_once "common.php";

$content = array();

$link = connectToDB();

getMainContent($link, $content);
getCategories($link, $content);
getNewestArticles($link, $content, 6);
getTopRatedArticles($link, $content, 6);

$template = file_get_contents("layouts/header.html").file_get_contents("layouts/index.html").file_get_contents("layouts/footer.html");

echo strtr($template, $content);

/**********************************************************************/
/**
 * Create new connection with default values described in common.php
 * 
 * @return $link valid DB link
 */
function connectToDB() {
    // check and 
    /*
     * header('HTTP/1.1 503 Service Temporarily Unavailable');
     * header('Status: 503 Service Temporarily Unavailable');
     * header('Retry-After: 300');//300 seconds
     */
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $sql = "SET NAMES utf8;";
    $rs = mysqli_query($link, $sql) or die(mysqli_error($link));

    return $link;
}

/**********************************************************************/

function getMainContent($dblink, &$data) {

    $sql = "SELECT 
                title AS __TITLE__, 
                keywords AS __KEYWORDS__,
                description AS __DESCRIPTION__,
                content AS __CONTENT__
            FROM articles WHERE AID = 0";

    $rs = mysqli_query($dblink, $sql) or die(mysqli_error($dblink));
    $row = mysqli_fetch_assoc($rs);

    $data = array_merge($data, $row);

    return 0;
}

/**********************************************************************/

function getCategories($dblink, &$data) {

    $sql = "SELECT 
                furl AS __LINK__, 
                title AS __TITLE__ 
            FROM categories 
            ORDER BY rate DESC";

    $rs = mysqli_query($dblink, $sql);

    $o = "";
    foreach ($rs as $item) {
        $o .= "<li><a href=\"/$item[__LINK__]/\">$item[__TITLE__]</a></li>\n";
    };

    // $data = array_merge($data, ["__CATEGORIES__" => $o]);
    $data["__CATEGORIES__"] = $o;

    return 0;
}

/**********************************************************************/

function getNewestArticles($dblink, &$data, $count) {
    $sql = "SELECT 
                a.title AS __TITLE__, 
                a.furl AS __LINK__, 
                UNIX_TIMESTAMP(a.pubdate) AS __PUBDATE__,
                c.title AS cTitle,
                c.furl AS cLink 
            FROM articles a 
            JOIN categories c USING (CID) 
            ORDER BY pubdate DESC
            LIMIT 0,$count";

    $rs = mysqli_query($dblink, $sql) or die(mysqli_error($dblink));

    $o = "";
    while ($row = mysqli_fetch_assoc($rs)) {
        $o .= "<li>" . date("d.m.Y H:i", $row["__PUBDATE__"]) . " <a href=\"/$row[cLink]/$row[__LINK__].html\" >$row[__TITLE__]</a>  </li>\n";
    };

    $data["__NEWEST_ARTICLES__"] = $o;

    return 0;
}

/**********************************************************************/

function getTopRatedArticles($dblink, &$data, $count) {
    $sql = "SELECT 
                a.title AS __TITLE__, 
                a.furl AS __LINK__, 
                a.rate AS __RATE__,
                c.title AS cTitle,
                c.furl AS cLink 
            FROM articles a 
            JOIN categories c USING (CID) 
            ORDER BY a.rate DESC
            LIMIT 0,$count";

    $rs = mysqli_query($dblink, $sql);
    $o = "";
    while ($row = mysqli_fetch_assoc($rs)) {
        $o .= "<li><a href=\"/$row[cLink]/$row[__LINK__].html\" title=\"Рейтинг: [$row[__RATE__]]\">$row[__TITLE__]</a></li>\n";
    };

    $data["__TOP_RATED_ARTICLES__"] = $o;

    return 0;
}

?>
