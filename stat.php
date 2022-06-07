<?php
/**Простой скрипт статистики по просмотрам страниц сайта (http://umapalata.local/stat.php),
 * реализованный в функциональном стиле.
 * 
 * Задача: сформировать таблицу, отображающую адреса страниц и количество 
 * обращений в абсолютном и процентном выражении, на основе данных из файла access.log
 * 
 * Как работает (декларативный подход основан на вопросе "Что нужно получить?", 
 * а не "Как это сделать?"):
 * * Получаем (что?) исходные данные из файла в виде строки.
 * * Фильтруем строку, выбирая (что?) только URL'ы и сохраняя их 
 *   в массив (["page", "page.html", "cat/page", ...]).
 * * Считаем (что?) количество дублирующихся элементов в массиве (просмотры), 
 *   сохраняем их в массив в виде ["page"=>number, "page.html"=>number,...].
 * * Преобразуем (что?) массив к виду [["page",number,percent],["page.html",number,percent],...].
 * * Формируем (что?) строки таблицы из массива, используя теги HTML.  
 * * Выводим гипертекст
 */

/** Данные (литерал и вычисленное выражение) как константы  */
define("LOG_FILE_NAME", "access.log");
define("RAW_DATA", array_count_values(raw_data(LOG_FILE_NAME))); // ["requested URI"=>views count,...]

/** Функция высшего порядка в стрелочной записи как данные (). Возвращает строки массива в виде html */
$html_table_data = fn($i) => "<tr><td>$i[0]</td><td>$i[1]</td><td>" . number_format($i[2], 3, ',', '') . "</td></tr>";
// Программа как результат вычисления (последовательный вызов функций)
$tbody = implode("\n", array_map($html_table_data, tabular_data(filtered_data(RAW_DATA))));

/**
 * Преобразует массив и дополняет набор данных вычисленным значением (%)
 * 
 * @param mixed[] $arr массив вида ["page"=>number, "page.html"=>number,...]
 * 
 * @return mixed[] Возвращает массив вида [["page",number,percent],["page.html",number,percent],...].
 *                  где percent - вычисленное значение
 */
function tabular_data($arr) {
    $total_views = array_sum($arr);
    static $r = [];
    $r[] = [key($arr), current($arr), current($arr) / $total_views * 100];
    return next($arr) == null ? $r : tabular_data($arr);
}

/**
 * Выбрать только нужные строки из массива (к-во просмотров больше среднего значения)
 * 
 * @param mixed[] $dataset массив вида ["page", "page.html", "cat/page", ...]
 * 
 * @return mixed[]  массив вида ["page"=>number, "page.html"=>number,...].
 * 
 */
function filtered_data($dataset) { // Filtered Data
    $avg = array_sum($dataset) / count($dataset);
    $filter = function ($value) use ($avg) {
        return $value > $avg;
    };
    $r = array_filter($dataset, $filter);
    arsort($r);
    return $r;
}

/**
 * Загрузка исходных данных
 * 
 * @param string $filename имя лог-файла. ожидается access.log
 * 
 * @return string  Весь лог в виде текста
 * 
 */
function load_log($filename) {
    $halt = function () {
        print("No data");
        die(); // полная остановка скрипта. только для примера.
    };
    return file_exists($filename) ? file_get_contents($filename) : $halt();
}

/**
 * Выборка адресов страниц и сохранение в массиве
 * 
 * @param string $filename имя лог-файла. ожидается access.log
 * 
 * @return mixed[] массив вида ["page", "page.html", "cat/page", ...]
 * 
 */
function raw_data($filename) {
    return preg_match_all("/http[s]?:\/\/([^\/]+)\/([^\?|\"]+)/ui", load_log($filename), $r) ? $r[2] : [];
}

//------------------------------------------------------------------------------
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Lambdas</title>
        <!-- comment <link href="layouts/styles.css" rel="stylesheet" type="text/css" />-->

        <style>
            table, th, td {
                border: solid 1px silver;
                border-collapse: collapse;
                }
            </style>
        </head>
        <body >
            <table>
                <thead><tr><th>Страницы</th><th>Просмотры (абс.)</th><th>Просмотры (%)</th></tr></thead>
                <tbody>
                    <?php echo $tbody; ?>
                </tbody>                
            </table>
            <?php highlight_file($_SERVER["SCRIPT_FILENAME"]); ?>
        </body>
    </html>
