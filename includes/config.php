<?php

session_start();

setlocale(LC_ALL, 'pt_BR');

define("PROJECT", "Rogério Vilas Boas - Cirurgião Plástico");
define("DOMINIO", "rogeriovilasboas");
define("BAR", "/");

switch ($_SERVER["HTTP_HOST"]) {
    case 'localhost':
        define("SQL_HOST", "localhost");
        define("SQL_DB", "rvb");
        define("SQL_USER", "root");
        define("SQL_PASS", "root");
        define("PATH", "rogeriovilasboas");
        define("DIRROOT", __DIR__);
        define("WWWROOT", 'http://' . $_SERVER["HTTP_HOST"] . BAR . PATH);
        define("DOCUMENTROOT", $_SERVER['DOCUMENT_ROOT']);
        define("REQUESTURI", str_replace(PATH, '', $_SERVER['REQUEST_URI']));
        define("EMAIL", "Eduardo <ematavirgem@gmail.com>");
        $hostIndicacao = '[DEV] ';
        break;

    default:
        define("SQL_HOST", "localhost");
        define("SQL_DB", "u782133977_rvb");
        define("SQL_USER", "u782133977_admin");
        define("SQL_PASS", "rogeriovboas");
        define("PATH", ""); // . '/' .  PATH
        define("DIRROOT", __DIR__);
        define("WWWROOT", 'http://' . $_SERVER["HTTP_HOST"]);
        define("DOCUMENTROOT", $_SERVER['DOCUMENT_ROOT']);
        define("REQUESTURI", str_replace(PATH, '', $_SERVER['REQUEST_URI']));
        define("EMAIL", PROJECT . "<benxs@" . DOMINIO . ".com.br>");
        $hostIndicacao = '';
        break;
}

define("TITLE", "{$hostIndicacao}" . PROJECT);
define("MODULO_INICIAL", "conteudo");
define("MENU", "../layout/menu.html");
define("BREADCRUMB", " &rsaquo; ");

$_SESSION['filemanager'] = DOCUMENTROOT . BAR . PATH;
$_SESSION['wwwroot'] = WWWROOT;