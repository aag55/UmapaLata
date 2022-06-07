<?php

/**
 * Скрипт отображения определенной статьи из заданной категории
 * (например, http://umapalata.local/mental-optica/tonika-na-pervyy-vzglyad-odnosloyna.html)
 * Идентификция статьи по AID (Article ID) и CID (Category ID).
 * 
 * Реализация в функциональном стиле.
 */
require_once "common.php";

// filter input data
//$_GET = array_map(fn($v) => preg_replace("/[^a-z0-9-]+/u","-",mb_strtolower($v)),$_GET); // php 7.4+
$_GET = array_map(function ($v) {
    return preg_replace("/[^a-z0-9-]+/u", "-", mb_strtolower($v));
}, $_GET);

$articleID = $_GET["AID"];
$categoryID = $_GET["CID"];

$link = connectToDB();

/* * ******************************************************************* */

$models = [
    "MAIN_CONTENT" =>
    "SELECT 
            a.title AS __TITLE__,
            a.keywords AS __KEYWORDS__,
            a.description AS __DESCRIPTION__,
            a.content AS __CONTENT__,
            CONCAT('<a href=\"/\">Истоки</a> // <a href=\"/',c.furl,'/\">',c.title,'</a> // ',a.title) AS __BREADCRUMBS__
        FROM articles a 
        JOIN categories c USING (CID)     
        WHERE a.furl = '$articleID';",
    "UPDATE_PAGE_RATE" =>
    "UPDATE articles SET rate = rate+1 WHERE furl = '$articleID'",
    "RELATED_PAGES" =>
    "SELECT 
            a.title AS title,
            a.furl AS afurl,
            c.furl AS cfurl,
            a.rate AS rate,
            a.pubdate AS pubdate
    FROM articles a
    JOIN categories c USING (CID)
    WHERE c.furl = '$categoryID' AND a.furl <> '$articleID';",
    "LIST_OF_CATEGORIES" =>
    "SELECT title, furl 
        FROM categories 
        ORDER BY rate DESC;"
];

/* * Основной код */

$layout = file_get_contents("layouts/article.html"); // if (file_exists) !!! 

printf("%s\n", strtr($layout, prepareToUse(rawData($models, $link))));

/* * ******************************************************************* */

function connectToDB() {
    $r = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $sql = "SET NAMES utf8;";
    $rs = mysqli_query($r, $sql) or die(mysqli_error($r));
    return $r;
}

/* * ******************************************************************* */

function MAIN_CONTENT($data) {
    if (count($data) == 0)
        raiseError("404");
    $r = $data[0];

    $tags = function ($tag) {
        $t = trim($tag);
        return "<li><a href=\"/search.php?q=" . urlencode($t) . "\">$t</a></li> ";
    };

    $r["__TAGS__"] = implode("", array_map($tags, explode(",", $r["__KEYWORDS__"])));
    // $out["__BREADCRUMBS__"] = see model;
    return $r;
}

/* * ******************************************************************* */

function UPDATE_PAGE_RATE($data) {
    return $data;
}

/* * ******************************************************************* */

function RELATED_PAGES($data) {
    $r["__RELATED_PAGES__"] = "";
    foreach ($data as $li) {
        $r["__RELATED_PAGES__"] .= "<li><a href=\"/$li[cfurl]/$li[afurl].html\">$li[title]</a></li>\n";
    }
    return $r;
}

/* * ******************************************************************* */

function LIST_OF_CATEGORIES($data) {
    // можно так, обычное замыкание:
    $toList = function ($i) {
        return "<li><a href=\"/$i[furl]/\">$i[title]</a></li>";
    };
    $r["__CATEGORIES__"] = implode("\n", array_map($toList, $data));
    //
    // или так, с использованием стрелочных функций (тоже замыкание) (PHP v7.4+):
    //$out["__CATEGORIES__"] = implode("\n", array_map(fn($i) => "<li><a href=\"/$i[furl]/\">$i[title]</a></li>", $data)); //php 7.4+
    return $r;
}

/* * ******************************************************************* */

function rawData($models, $link) {
    foreach ($models as $model => $query) {
        $rs = mysqli_query($link, $query) or die(mysqli_error($link));
        is_object($rs) ? $r[$model] = mysqli_fetch_all($rs, MYSQLI_ASSOC) : $r[$model] = [];
    }
    return $r;
}

/* * ******************************************************************* */

function prepareToUse($rawdata) {
    $r = [];
    foreach ($rawdata as $key => $value) {
        $r = array_merge($r, call_user_func($key, $value));
    }
    return $r;
}
