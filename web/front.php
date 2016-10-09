<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

function render_template(Request $request)
{
    extract($request->attributes->all(), EXTR_SKIP);
    ob_start();
    include sprintf(__DIR__.'/../src/pages/%s.php', $_route);
    return new Response(ob_get_clean());
}

$request = Request::createFromGlobals();
// $request = Request::create('/is_leap_year/2012');

$sc = include __DIR__.'/../src/container.php';
// $framework = $sc->get('framework');
$framework = $sc->get('framework_cache');
// $framework = Simplex\Framework::getInstance();

$response = $framework->handle($request);
$response->send();
// echo $response;
