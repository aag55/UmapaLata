<?php

/**
 * index.php - an entry point into UmapaLata
 * 
 * @package UmapaLata\HomePage
 * @copyright no(c) 2022, aag.omsk_at_gmail.com
 * @license http://creativecommons.org CC BY-SA 
 * 
 */

main();

/**
 * "main" is a function (procedure) to include scripts that depends on requested URL
 *  
 * @return void
 */
function main(): void {
    switch (true) {
        case ($_SERVER["REQUEST_URI"] === "/") && empty($_SERVER["QUERY_STRING"]) :
            require_once "structural.php";
            break;
        case (count($_GET) == 1) && (isset($_GET["CID"])):
            /**
             * $_GET MUST be filtered before use
             */
            require_once "object-oriented.php";
            break;
        case (count($_GET) == 2) && isset($_GET["CID"]) && isset($_GET["AID"]):
            /**
             * $_GET MUST be filtered before use
             */
            require_once "functional.php";
            break;

        //check "/index.php"  todo: send 301 & redirect to "/"
        default: echo "HERE IS" . var_dump($_SERVER, $_GET);
    }
}


