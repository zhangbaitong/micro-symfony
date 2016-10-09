<?php

use Symfony\Component\Routing;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



$routes = new Routing\RouteCollection();
$routes->add('leap_year', new Routing\Route('/is_leap_year/{year}', array(
    'year' => null,
    '_controller' => 'Calendar\\Controller\\LeapYearController::indexAction',
)));
$routes->add('bye', new Routing\Route('/bye', array(
    '_controller' => function (Request $request) {
        return render_template($request);
    }
)));
$routes->add('hello', new Routing\Route('/hello', array(
    'name' => 'World',
    '_controller' => function (Request $request) {
        return render_template($request);
    }
)));
$routes->add('hello', new Routing\Route('/hello/{name}', array(
    'name' => 'World',
    '_controller' => function (Request $request) {
        // $foo will be available in the template
        $request->attributes->set('foo', 'bar');

        $response = render_template($request);

        // change some header
        $response->headers->set('Content-Type', 'text/plain');

        return $response;
    }
)));

return $routes;