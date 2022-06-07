<?php

require_once "common.php";

$q = strip_tags($_GET["q"]);
$app = APP; // Articles Per Page

isset($_GET["p"]) ? $limit = abs((($_GET["p"] - 1) * $app)) . ",$app" : $limit = "0,$app";

define("SEARCH_RESULT", "SELECT a.title AS title, a.description AS description, "
        . "CONCAT('/',c.furl,'/',a.furl,'.html') AS furl "
        . "FROM articles a JOIN categories c USING (CID) "
        . "WHERE a.keywords "
        . "LIKE \"%$q%\" LIMIT $limit;");

define("NUM_ROWS", "SELECT count(*) as rowcount "
        . "FROM articles a JOIN categories c USING (CID) "
        . "WHERE a.keywords "
        . "LIKE \"%$q%\";");

/* * ******************************************************************* */
class DB {

    protected $link = null;

    public function __construct() {
    	$this->link = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    	$this->link->query("SET NAMES utf8");
    	return $this->link ;
    }

}

/* * ******************************************************************* */

trait Paginator {

    public function Paginate($q) {
        $db = new DB();
        $numrows = ($db->link->query(NUM_ROWS))->fetch_all(MYSQLI_ASSOC);
        isset($_GET["p"]) ? $page = (int) $_GET["p"] : $page = 0;

        return paginator($numrows[0]["rowcount"], APP, urlencode($q), $page);
    }

}

/* * ******************************************************************* */

class Model extends DB {

    use Paginator;

    private $sql = SEARCH_RESULT;
    private $data = [];

    public function __construct($sql = null) {
        parent::__construct();

        isset($sql) ? $this->sql = $sql : false;

        $q = trim(strip_tags($_GET["q"])); // +UNION+SELECT+*+FROM+users; :/

        $this->data["__QUERY__"] = $q;
        $this->data["__TITLE__"] = "Результаты поиска || $q";

        $this->data["__CONTENT__"] = "";
        $rs = ($this->link->query($this->sql))->fetch_all(MYSQLI_ASSOC);

        foreach ($rs as $record) {
            $this->data["__CONTENT__"] .= "<h2><a href=\"$record[furl]\">$record[title]</a></h2>\n";
            $this->data["__CONTENT__"] .= "<p>$record[description] <a href=\"$record[furl]\"><i class=\"fa fa-angle-double-right\"></i></a></p>\n";
        };

        $this->data["__PAGINATION__"] = $this->Paginate($q);        
    }

    public function data() {
        return count($this->data) > 0 ? $this->data : ["title" => "Not Found", "description" => ""];
    }

}

/* * ******************************************************************* */

class View {

    private $placeholders = [];
    private $layout = "";

    public function __construct($view) {
        $this->layout = file_get_contents($view);
        ;
    }

    public function layout() {
        return file_get_contents("layouts/header.html") . $this->layout . file_get_contents("layouts/footer.html");
    }

}

/* * ******************************************************************* */

class Controller {

    private $data = null;
    private $view = "";

    public function loadModel($model) {
        $this->data = $model->data();
    }

    public function prepareView($view) {
        $this->view = $view->layout();
    }

    public function renderView() {
        echo strtr($this->view, $this->data);
    }

}

/* * ******************************************************************* */

$c = new Controller();
$c->loadModel(new Model());
$c->prepareView(new View("layouts/search.html"));
$c->renderView();
