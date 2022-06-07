<?php

/* * Адаптированный для использования в контрольной панели скрипт статистики 
 * просмотров страниц. Исходный вариант с комментариями - в файле http://umapalata.local/stat.php
 */

// Это основная и единственная функция, доступная извне ----------------------

function show_stat() {
    define("LOG_FILE_NAME", "access.log");

    // ------------------------------------------------------------------------
    function tabular_data($arr) {
        $total_views = array_sum($arr);
        static $r = [];
        $r[] = [key($arr), current($arr), current($arr) / $total_views * 100];
        return next($arr) == null ? $r : tabular_data($arr);
    }

    // ------------------------------------------------------------------------
    function filtered_data($dataset) { // Filtered Data
        $avg = array_sum($dataset) / count($dataset);
        $filter = function ($value) use ($avg) {
            return $value > $avg*2;
        };
        $r = array_filter($dataset, $filter);
        arsort($r);
        return $r;
    }

    // ------------------------------------------------------------------------
    function load_log($filename) {
        $halt = function () {
            print("No data");
            die(); // полная остановка скрипта. только для примера.
        };
        return file_exists($filename) ? file_get_contents($filename) : $halt();
    }

    // ------------------------------------------------------------------------
    function raw_data($filename) {
        return preg_match_all("/http[s]?:\/\/([^\/]+)\/([^\?|\"]+)/ui", load_log($filename), $r) ? $r[2] : [];
    }

    // ------------------------------------------------------------------------

    define("RAW_DATA", array_count_values(raw_data(LOG_FILE_NAME))); // ["requested URI"=>views count,...]

    $html_table_data = fn($i) => "<tr><td><a href=\"/$i[0]\">$i[0]</a></td><td>$i[1]</td><td>" . number_format($i[2], 3, ',', '') . "</td></tr>";
    $tbody = implode("\n", array_map($html_table_data, tabular_data(filtered_data(RAW_DATA))));

    $r = <<<HTML
<div class="table-responsive">        
    <table class="table table-striped">
    <thead class="thead-light"><tr><th>Страницы</th><th>Просмотры (абс.)</th><th>Просмотры (%)</th></tr></thead>
    <tbody>\n    $tbody\n    </tbody>\n
    </table>
</div>
HTML;

    return $r; // end show_stat()
}

//------------------------------------------------------------------------------
