<?php
session_start();

define("ACTION", ["login", "logout"]);

$action = implode(array_intersect(ACTION, array_keys($_GET)));

$performAction = $action ?
        call_user_func($action) :
        function () {
            return isset($_SESSION['USER']) ?
                header("location:/cpanel/") : 
                header("location:?login"); 
        };

define("USERS", "topsecretdata");

//---------------------------------------------------------------------
function register() {
    // Самостоятельно. См. файл /test/php-auth.php
}

//---------------------------------------------------------------------
function login() {
    return isset($_REQUEST["username"]) && isset($_REQUEST["password"]) ?
            function () {
                $filterData = function ($data) { // см. также filter_input_array
                    return htmlspecialchars(stripslashes(trim($data)));
                };
                // очистить данные пользователя
                $user = $filterData($_REQUEST["username"]);
                $pass = $filterData($_REQUEST["password"]);

                // тут место подключения к БД и запроса к таблице users
                $list_of_users = file_get_contents(USERS);
                // если есть пользователь, то сохранить его в сессии, иначе - см. ЗАДАНИЕ в ветке false	
                preg_match("/(\d+):($user):([^\s]+):(0|1)/uim", $list_of_users, $matches) && password_verify($pass, $matches[3]) ?
                        $_SESSION["USER"] = ["ID" => (int) $matches[1], "NAME" => $user] : // Пользователь ОК
                        null; // ЗАДАНИЕ: реализовать обработку ситуации, когда учетная запись не найдена 
                // return только для "красивости", т.к. header возвращает void, а Location вообще перенаправляет запрос
                return header("Location:" . htmlspecialchars($_SERVER["PHP_SELF"]));
                die(); // не выполнится, но на всякий случай		
            } : function () { // Показать форму ввода данных
                return <<<HEREDOC
<form action="?login" method="POST">
  <div class="form-group">
    <label for="email">Логин (email):</label>
    <input type="email" class="form-control" placeholder="Почтовый адрес" name="username" id="email">
  </div>
  <div class="form-group">
    <label for="pwd">Пароль:</label>
    <input type="password" class="form-control" placeholder="Пароль" name="password" id="pwd">
  </div>
  <div class="form-group form-check">
    <label class="form-check-label">
      <input class="form-check-input" type="checkbox" name="remember"> Запомнить
    </label>
  </div>
  <button type="submit" class="btn btn-primary">Войти</button>
</form> 
HEREDOC;
            };
}

//---------------------------------------------------------------------
function logout() {
    unset($_SESSION["USER"]); // закончить сессию, сбросить данные и перейти в начало
    return header("Location:/");
    die();
}
?>

<!DOCTYPE HTML>  
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
        <script src="/cpanel.js"></script> 
        <style>
            body {background-color: lightgray;}
            .container {max-width: 600px; margin-top: 10%;}      
            form {border: solid 1px #EEE; padding: 1rem; border-radius: 5px; background-color: white;
            box-shadow: 4px 4px 10px gray, -2px -2px 10px gray;}
        </style> 
    </head>
    <body>  
        <div class="container">
        <?php echo $performAction(); ?>
        </div>
    </body>
</html>

