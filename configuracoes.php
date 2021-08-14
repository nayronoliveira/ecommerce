<?php
switch ($_SERVER['SERVER_NAME']) {
    case 'localhost':
        define('DIR', $_SERVER['DOCUMENT_ROOT'] . "/" . explode("/", $_SERVER['REQUEST_URI'])[1]);
        break;
    
    default:
        define('DIR', $_SERVER['DOCUMENT_ROOT']);
        break;
}