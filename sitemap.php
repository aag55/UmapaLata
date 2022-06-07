<?php 

require "common.php";

header("Content-type: text/xml;charset=UTF-8");

/* Все подробности о протоколе SiteMap - на сайте https://www.sitemaps.org/ */
?>
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<url>
<?php echo "<loc>".SITE."/</loc>"; ?>
</url>
<?php

$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$sql = "SET NAMES utf8;";
mysqli_query($link, $sql) or die(mysqli_error($link));

/*
Этот запрос возвращает пустые категории, а непустые - только с вложенными страницами.
Т.е. для непустых категорий не будет ссылок на начальные страницы (где список статей в категории).
Это несколько бесполезно с точки зрения пользователя. Стоит переделать, возможно через UNION или RIGHT JOIN. 
*/

$sql = "SELECT c.furl AS category, a.furl AS article, a.pubdate AS lastmod 
			FROM categories c 
			LEFT JOIN articles a USING (CID) 
			ORDER BY 2,1 ;";
			
$ds = mysqli_query($link, $sql) or die(mysqli_error($link));

$rs = mysqli_fetch_all($ds, MYSQLI_ASSOC);

foreach ($rs as $row) {
	!is_null($row["article"]) ? $a = "$row[article].html" : $a = ""; 
	!is_null($row["lastmod"]) ? $l = "<lastmod>".date("Y-m-d", strtotime($row["lastmod"]))."</lastmod>\n" : $l = "";
	/* Еще здесь могут быть changefreq и priority */
	echo "<url>\n<loc>".SITE."/$row[category]/$a</loc>\n$l</url>\n";
	}

?>
</urlset>