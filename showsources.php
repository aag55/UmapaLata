<?php

$body = isset($_GET["fn"]) ? 
    function() {
        return ["h1"=>$_GET["fn"],"content"=>highlight_file($_GET["fn"], true)];    
    } : 
    function() {
        $files = glob( "*.php");
        $o = "";

        foreach ($files as $f) {
        $o .="<li><a href=\"?fn=$f\">$f</a></li>\n";
        }
        return ["h1"=>"Все PHP-скрипты проекта","content"=>"<ul id=\"top\">$o</ul>"];        
    };
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>УП :: Коды</title>
    <!-- 
    <link rel="stylesheet" href="/highlight/styles/default.min.css">
    <script src="/highlight/highlight.min.js"></script>
    <script>hljs.highlightAll();</script>
    -->
    <style>
        h1 {font-weight: normal; font-size: 2.4rem; color: gray;}
        .code {border-top: solid 1px gray; border-bottom: solid 1px gray; font-size: 1rem;}
        #top > li {margin-bottom: .5rem; }
    </style>
</head>
<body>
    <div class="container">        
        <h1><?=$body()["h1"]; ?></h1>
        <div class="code"><?=$body()["content"]; ?></div>  
        <p><a href="/showsources.php">К списку файлов</a> | <a href="#top">К началу страницы</a> | <a href="/">На сайт</a></p>
    </div>
</body>
</html>