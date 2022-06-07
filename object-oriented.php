<?php

require_once "common.php";

/**
  Активная модель MVC
  Где:
  - Модель — совокупность кода доступа к данным и СУБД, плюс вся бизнес-логика.
  Модель может инкапсулировать и другие модели.
  - Контроллер — отвечает лишь заприём запроса от пользователя, анализ запроса и
  выбор следующего действия системы, соответственно результатам анализа.
  В этом случае контроллер становится «тонким» и выполняет исключительно функцию
  связующего звена (glue layer) между отдельными компонентами информационной системы.
  - Представление — реализует пользовательский интерфейс

  В этом примере реализации M, V и C представлены в виде методов объекта WebDocument.
  Реализацию, где M, V, C являются отдельными объектами см. в файле search.php
 */

/** Чтобы не забыть сделать обработку ошибок *************************** */
interface HTTPErrors {

    public function raiseError($code);
}

/**
  Чтобы упростить отладку, реализация методов непоредственного получения данных
  вынесена в trait, его при необходимости можно использовать и с другими классами
 */
trait Helper {

// -------------------
    protected function getListOfCategories(): string {
        $o = "";
        $rs = $this->db->query("SELECT furl,title FROM categories ORDER BY rate DESC;");
        while ($item = $rs->fetch_assoc()) {
            $o .= "<li><a href=\"/$item[furl]/\">$item[title]</a></li>\n";
        }
        return $o; // $o != "" ? $o : $this->raiseError(404);
    }

// -------------------
    protected function getListOfArticles($cid): string {
        $rs = $this->db->query("SELECT a.title AS title, a.description AS description, a.furl AS afurl, c.furl AS cfurl 
            FROM articles a JOIN categories c USING (CID)
            WHERE c.furl='$cid' ORDER BY a.rate DESC ;");

        if (is_null($rs))
            $this->raiseError(404);

        $rs->num_rows > 0 ? $o = "" : $o = "<p>Еще никто не писал сюда.</p>";

        while ($item = $rs->fetch_assoc()) {
            $o .= "\n<article>\n<h2><a href=\"/$item[cfurl]/$item[afurl].html\">$item[title]</a></h2>\n<p>$item[description]</p>\n</article>\n";
        }
        return $o;
    }

// -------------------
    protected function getTitle($cid): string {
        $rs = $this->db->query("SELECT title, description FROM categories 
                WHERE furl='$cid';");

        $rs->num_rows != 0 ? $o = "" : $this->raiseError(404);

        $item = $rs->fetch_assoc();
        return $item["title"];
    }

// -------------------
    protected function getBreadcrumbs($category): string {
        return "<p class=\"breadcrumbs\"><a href=\"/\" title=\"\">Истоки</a> // $category</p>";
    }

}

/**
  WebDocument. Реализует методы модели, контроллера и представления
 */
class WebDocument implements HTTPErrors {

    use Helper;

    private $db = null;
    private $layout = "";
    private $placeholders = [];

    // -------------------
    public function __construct() {
        try {
            $this->layout = file_get_contents("layouts/category.html");
            $this->db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            $rs = $this->db->query("SET NAMES utf8;");
        } catch (Exception $e) {
            $e->getMessage();
            $this->raiseError(404);
        }
    }

    // -------------------
    public function doControl() {
        return isset($_GET["CID"]) ? $this->placeholders["<!--CATEGORY-ID-->"] = preg_replace("/[^a-z0-9-]+/u", "-", mb_strtolower($_GET["CID"])) : $this->raiseError(404);
    }

    // -------------------
    public function loadModel() {

        $categoryID = $this->placeholders["<!--CATEGORY-ID-->"];
        try {

            $this->placeholders["<!--LIST-OF-CATEGORIES-->"] = $this->getListOfCategories();
            $this->placeholders["<!--LIST-OF-ARTICLES-->"] = $this->getListOfArticles($categoryID);
            $this->placeholders["<!--TITLE-->"] = $this->getTitle($categoryID);
            $this->placeholders["<!--BREADCRUMBS-->"] = $this->getBreadcrumbs($this->placeholders["<!--TITLE-->"]);
            $this->placeholders["<!--SOURCECODE-->"] = highlight_file($_SERVER["SCRIPT_FILENAME"], true);
        } catch (Exception $e) {
            $e->getMessage();
            $this->raiseError(404);
        }
    }

    // -------------------
    public function renderView() {
        echo strtr($this->layout, $this->placeholders);
    }

    /**
     * Это работает не так как нужно при вызове из других скриптов ($code не обрабатывается)
     * @param type $code
     */
    public function raiseError($code) {

        switch ($code) {
            case 400: $msg = " 400 Bad Request";
                break;
            case 403: $msg = " 403 Forbidden";
                break;
            case 404: $msg = " 404 Not Found";
                break;
        }

        header("$_SERVER[SERVER_PROTOCOL] $msg", true, $code);
        include("error.php");

        die;
    }

}

/* * ******************************************************************* */


$document = new WebDocument();

$document->doControl();
$document->loadModel();
$document->renderView();


/**********************************************************************/
