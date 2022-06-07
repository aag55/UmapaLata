<?php
session_start();

require "common.php";

$actions = ["listOfArticles" => "Все статьи",
    "newArticle" => "<span class=\"glyphicon glyphicon-plus\"></span>",
    "listOfCategories" => "Категории",
    "newCategory" => "<span class=\"glyphicon glyphicon-plus\"></span>",
    "editSQL" => "SQL", "showStat" => "Статистика"];

$menu = implode(" ", array_map(fn($k, $v) => "<li><a href=\"/cpanel/$k\">$v</a></li>", array_keys($actions), array_values($actions)));

/* * ************************************************************************** */
$link = function () {
    $link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    mysqli_query($link, "SET NAMES utf8;") or die(mysqli_error($link));
    return $link;
};


/* * ************************************************************************** */
$showStat = function () {
    require_once 'stat2cpanel.php';
    return ["Статистика просмотров", show_stat()];
};

/* * ************************************************************************** */
$deleteArticle = function () use ($link) {
    $id = (int)mb_substr($_SERVER["REQUEST_URI"], mb_strpos($_SERVER["REQUEST_URI"], "?") + 1);

    $sql = "DELETE FROM articles WHERE AID=$id;";

    return ["Удаление статьи", "<pre>$sql</pre>"];
};

/* * ************************************************************************** */
$deleteCategory = function () use ($link) {
    $id = (int)mb_substr($_SERVER["REQUEST_URI"], mb_strpos($_SERVER["REQUEST_URI"], "?") + 1);

    $sql = "DELETE FROM categories WHERE CID=$id;";

    return ["Удаление категории (но лучше этого не делать, связи можно порвать)", "<pre>$sql</pre>"];
};

/* * ************************************************************************** */
$updateArticle = function () use ($link) {

    return [null, "<pre>" . print_r($_POST, true) . "</pre>"];
};

/* * ************************************************************************** */
$addArticle = function () use ($link) {

    return ["Добавление статьи", "<pre>" . print_r($_POST, true) . "</pre>"];
};

/* * ************************************************************************** */
$newArticle = function () {

$form = <<<HTML
<form  class="form-horizontal" action="addArticle" method="post">
    <div class="row">
    <div class="col-sm-8 m1">
        <label for="title">Название</label><input id="title" type="text" class="form-control" name="title" value=""/></label>
        <label for="desc">Описание/Реферат</label><input id="desc" type="text" class="form-control" name="description" value=""/>
        <label for="keys">Ключевые слова/Метки</label><input id="keys" type="text" class="form-control" name="keywords"  value=""/>   
        <label for="furl">ЧПУ</label><input id="furl" type="text" class="form-control" name="furl" value=""/>
    </div>
        
    <div class="col-sm-4 m1">
        <label for="cid">Категория (ID)</label><input readonly id="cid" type="text" class="form-control" name="cid" value=""/>
        <label for="uid">Автор (ID)</label><input readonly id="uid" type="text" class="form-control" name="uid" value=""/>
        <label for="lastmod">Дата публикации/обновления</label><input  id="lastmod" type="month" class="form-control" name="lastmod" value=""/>           
        <label for="rate">Популярность</label><input readonly id="rate" type="text" class="form-control" name="rate" value=""/>                   
    </div>     
    </div>
        
    <div class="row">
        <div class="col-sm-12 m1"><label for="cont">Текст статьи <span style="font-weight: normal !important;">(Разрешенные теги: p, img, ul, ol, li ...)</span> </label><textarea class="form-control" rows="12" name="content" placeholder="Allowed tags: p, img, ul, ol, li ..." ></textarea></div>
    </div>
            
    <div class="row">
        <div class="col-sm-12 m1" style="display: flex; text-align: center;">
            <div><img src="/images/addimg.png" width="200" height="200" /></div>
            <div><img src="/images/addimg.png" width="200" height="200" /></div>
            <div><img src="/images/addimg.png" width="200" height="200" /></div>
            <div><img src="/images/addimg.png" width="200" height="200" /></div>
            <div><img src="/images/addimg.png" width="200" height="200" /></div>
        </div>
    <label for="inImage">Choose Image</label><input id="inImage" onclick="ff()" type="file" class="form-control" name="image" />     
    </div>          
    <div class="row">
        <div class="col-sm-8 m1"></div>
        <div class="col-sm-4 m1"><button type="submit" class="btn btn-block btn-success">Сохранить</button></div>       
    </div>    
</form>
HTML;

    return ["Новая статья", $form];
};

/* * ************************************************************************** */
$editArticle = function () use ($link) {
    $id = (int)mb_substr($_SERVER["REQUEST_URI"], mb_strpos($_SERVER["REQUEST_URI"], "?") + 1);

    $sql = "SELECT * FROM articles WHERE AID=$id;";
    $rs = mysqli_fetch_all(mysqli_query($link(), $sql), MYSQLI_ASSOC);
    $article = $rs[0];
    $form = <<<HTML
<form  class="form-horizontal" action="updateArticle" method="post">
    <div class="row">
    <div class="col-sm-8 m1">
        <label for="title">Название</label><input id="title" type="text" class="form-control" name="title" value="$article[title]"/></label>
        <label for="desc">Описание/Реферат</label><input id="desc" type="text" class="form-control" name="description" value="$article[description]"/>
        <label for="keys">Ключевые слова/Метки</label><input id="keys" type="text" class="form-control" name="keywords"  value="$article[keywords]"/>   
        <label for="furl">ЧПУ</label><input id="furl" type="text" class="form-control" name="furl" value="$article[furl]"/>
    </div>
        
    <div class="col-sm-4 m1">
            <!--Сюда выбрать подстановку из таблицы "категории"-->
        <label for="cid">Категория (ID)</label><input readonly id="cid" type="text" class="form-control" name="cid" value="$article[CID]"/>
        <label for="uid">Автор (ID)</label><input readonly id="uid" type="text" class="form-control" name="uid" value="$article[UID]"/>
        <label for="lastmod">Дата публикации/обновления</label><input readonly id="lastmod" type="text" class="form-control" name="lastmod" value="$article[pubdate]"/>           
        <label for="rate">Популярность</label><input readonly id="rate" type="text" class="form-control" name="rate" value="$article[rate]"/>                   
    </div>     
    </div>
        
    <div class="row">
        <div class="col-sm-12 m1"><label for="cont">Текст статьи <span style="font-weight: normal !important;">(Разрешенные теги: p, img, ul, ol, li ...)</span> </label><textarea class="form-control" rows="12" name="content" placeholder="Allowed tags: p, img, ul, ol, li ..." >$article[content]</textarea></div>
    </div>
        
    <div class="row">
        <div class="col-sm-2 m1"><a class="btn btn-block btn-danger" href="deleteArticle?$article[AID]">Удалить <span class="glyphicon glyphicon-trash"></span></a></div>
        <div class="col-sm-6 m1"></div>
        <div class="col-sm-4 m1"><button type="submit" class="btn btn-block btn-success">Сохранить изменения</button></div>       
    </div>    
</form>
HTML;

    return ["Редактирование", $form];
};

/* * ************************************************************************** */
$listOfArticles = function () use ($link) {
    $sql = "SELECT AID, title, description, furl FROM articles ORDER BY pubdate DESC;";
    $rs = mysqli_fetch_all(mysqli_query($link(), $sql), MYSQLI_ASSOC);

    $f = function ($i) {
        return "<li><a href=\"editArticle?$i[AID]\">$i[title]</a></li>";
    };
    return ["Список статей", "<ul>" . implode("\n", array_map($f, $rs)) . "</ul>"];
};

/* * ************************************************************************** */
$listOfCategories = function () use ($link) {
    $sql = "SELECT CID, title, description, furl FROM categories ORDER BY 2;";
    $rs = mysqli_fetch_all(mysqli_query($link(), $sql), MYSQLI_ASSOC);

    $f = function ($i) {
        return "<li><a href=\"editCategory?$i[CID]\">$i[title]</a></li>";
    };
    return ["Список категорий", "<ul>" . implode("\n", array_map($f, $rs)) . "</ul>"];
};

/* * ************************************************************************** */
$editCategory = function () use ($link) {
    $id = (int)mb_substr($_SERVER["REQUEST_URI"], mb_strpos($_SERVER["REQUEST_URI"], "?") + 1);

    $sql = "SELECT * FROM categories WHERE CID=$id;";
    $rs = mysqli_fetch_all(mysqli_query($link(), $sql), MYSQLI_ASSOC);
    $category = $rs[0];

    $o = print_r($category, true);

    $form = <<<HTML
    <form action="updateCategory" method="POST">
    <div class="row">
    <div class="col-sm-8 m1">
        <label for="title">Название</label><input id="title" type="text" class="form-control" name="title" value="$category[title]"/></label>
        <label for="desc">Описание/Реферат</label><input id="desc" type="text" class="form-control" name="description" value="$category[description]"/>
        <label for="keys">Ключевые слова/Метки</label><input id="keys" type="text" class="form-control" name="keywords"  value="$category[keywords]"/>   
        <label for="furl">ЧПУ</label><input id="furl" type="text" class="form-control" name="furl" value="$category[furl]"/>
    </div>
     <div class="col-sm-4 m1">  
        Сюда можно статистику показать (например, количество статей в категории...)
         </div>
    </div> 
   <div class="row">
        <div class="col-sm-2 m1"><a class="btn btn-block btn-danger" href="deleteCategory?$category[CID]">Удалить <span class="glyphicon glyphicon-trash"></span></a></div> 
        <div class="col-sm-4 m1"><button type="submit" class="btn btn-block btn-success">Сохранить изменения</button></div> 
            <div class="col-sm-6 m1"></div>
    </div>    
    </form>
HTML;
    return ["Редактирование категории", $form];
};

/* * ************************************************************************** */
$newCategory = function () {
    $form = <<<HTML
    <form action="addCategory" method="POST">
    <div class="row">
    <div class="col-sm-8 m1">
        <label for="title">Название</label><input id="title" type="text" class="form-control" name="title" value=""/></label>
        <label for="desc">Описание/Реферат</label><input id="desc" type="text" class="form-control" name="description" value=""/>
        <label for="keys">Ключевые слова/Метки</label><input id="keys" type="text" class="form-control" name="keywords"  value=""/>   
        <label for="furl">ЧПУ</label><input id="furl" type="text" class="form-control" name="furl" value=""/>
    </div>
     <div class="col-sm-4 m1">  
        Какая-то доп информация, справка или типа того.
         </div>
    </div> 
   <div class="row">
        <div class="col-sm-2 m1"></div>        
        <div class="col-sm-4 m1"><button type="submit" class="btn btn-block btn-success">Сохранить изменения</button></div> 
            <div class="col-sm-6 m1"></div>
    </div>    
    </form>
HTML;
    return ["Редактирование категории", $form];
};

/* * ************************************************************************** */
$editSQL = function () {
    $form = <<<HTML
    <form action="runSQL" method="POST">
       
    <div class="row">
        <div class="col-sm-12 m1"><label for="query">Строка запроса SQL</label><textarea class="form-control" rows="12" name="query" placeholder="Делайте это, если уверены в том, что понимаете то, что делаете." ></textarea></div>
    </div>
        
    <div class="row">
        <div class="col-sm-4 m1"></div>
        <div class="col-sm-4 m1"><button type="submit" class="btn btn-block btn-success">Выполнить запрос</button></div>
         <div class="col-sm-4 m1"></div>
    </div> 
    </form>
HTML;
    return ["Выполнить запрос SQL", $form];
};

/* * ************************************************************************** */
$runSQL = function () use ($link) {
    $post = print_r($_POST, true);
    $sql = "SQL-query string is here: $post";

    return ["Выполнение запроса", $sql];
};

/* * ************************************************************************** */
$addCategory = function () use ($link) {
    $post = print_r($_POST, true);
    $sql = "INSERT INTO categories SET $post";

    return ["Добавление категории $_POST[title]", $sql];
};

/* * ************************************************************************** */
$updateCategory = function () use ($link) {
    $post = print_r($_POST, true);
    $sql = "UPDATE categories SET $post";

    return ["Update Category", $sql];
};

/* * ************************************************************************** */
$logIn = function () use ($link) {
$html =<<<HTML
    <div class="dachboard">
        <div class="p-8"><a href="/cpanel/listOfArticles"><i class="fa fa-files-o"></i>Статьи</a></div>
        <div class="p-8"><a href="/cpanel/listOfCategories"><i class="fa fa-folder-o"></i>Категории</a></div>
        <div class="p-8"><a href="/cpanel/showStat"><i class="fa fa-bar-chart-o" ></i>Статистика</a></div>
    </div>
HTML;     

    return isset($_SESSION["USER"]) ? ["", $html] : header("Location: /entrance.php?login");
};

/* * ************************************************************************** */
$logOut = function () {
    return header("Location: /entrance.php?logout");
};
/* * ************************************************************************** */

if (empty($_GET)) 
    header("Location: /cpanel/logIn");

$body = @is_callable(${key($_GET)}) ?  call_user_func(${key($_GET)}, null) :  ["Something is going wrong(","No function!"];

?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title>УП :: ПУ</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
        <!-- <script src="/cpanel.js"></script> --> 
        <style>
            .m1 {margin: 1rem 0 !important;}
            .dachboard {display: flex;   justify-content: space-around; height: 260px; align-content: space-around;}
            .dachboard > div {text-align: center; width: 200px; height: 200px; border: solid 1px #EEE; border-radius: 10px; padding-top: 30px; box-shadow: 4px 4px 10px gray;}
            .dachboard > div i {font-size:12rem; display: block;}
            .dachboard > div a {font-size:2rem; text-decoration: none;}
        </style>
    </head>
    <body>
        <nav class="navbar navbar-inverse">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand" href="/">УмапаЛата</a>
                </div>
                <ul class="nav navbar-nav">
<?= $menu; ?>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="#"><span class="glyphicon glyphicon-user"></span> <?= $_SESSION["USER"]["NAME"]; ?></a></li>
                    <li><a href="logOut"><span class="glyphicon glyphicon-log-out"></span> Выйти</a></li>
                </ul>
            </div>
        </nav>

        <div class="container mt-3">  
            <h1><?= $body[0]; ?></h1>            
<?= $body[1]; ?>
        </div>
    </body>
</html>
