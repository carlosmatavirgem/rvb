<?php

require_once('image.class.php');

function pe($var = null)
{
    echo '<pre>';
    print_r($var);
    die();
}

function _getParam($param, $default = null)
{
    if (isset($_GET[$param])) {
        return $_GET[$param];
    } else {
        return $default;
    }
}

$pog = explode('&', $_SERVER['QUERY_STRING']);

foreach ($pog as $param) {
    $param = explode('=', $param);
    $_GET[$param[0]] = $param[1];
}

$src = _getParam('src');
$output = _getParam('output', 'jpeg');
$w = _getParam('w');
$h = _getParam('h');
$zc = _getParam('zc', 2);
$zoom = _getParam('zoom', 2);
$align = _getParam('align', 'center');

$image = new Image($output);
$image->setDefaultPath('images/indisponivel.jpg')
        ->setSource($src)
        ->setSave(true)
        ->setSave(false)
        ->setZoom($zoom)
        ->setCachePath('images/cache')
        ->setMaxSize($w, $h, $zc)
        ->setBackgroundColor('#FFF')
        ->setAlign($align)
        ->output();