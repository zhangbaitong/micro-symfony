<?php

use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\Reference;


//init container
$sc = new DependencyInjection\ContainerBuilder();
$sc->setParameter('routes', include __DIR__.'/app.php');
$sc->setParameter('caches', __DIR__.'/../cache');
$sc->setParameter('debug', true);

//resolver
$sc->register('controllerResolver', 'Symfony\Component\HttpKernel\Controller\ControllerResolver');
$sc->register('argumentResolver', 'Symfony\Component\HttpKernel\Controller\ArgumentResolver');
//dispatcher
$sc->register('listener.contentlength', 'Simplex\ContentLengthListener');
$sc->register('listener.google', 'Simplex\GoogleListener');
$sc->register('listener.response', 'Simplex\StringResponseListener');
$sc->register('dispatcher', 'Symfony\Component\EventDispatcher\EventDispatcher')
    ->addMethodCall('addSubscriber', array(new Reference('listener.contentlength')))
    ->addMethodCall('addSubscriber', array(new Reference('listener.google')))
    ->addMethodCall('addSubscriber', array(new Reference('listener.response')));
//matcher
$sc->register('context', 'Symfony\Component\Routing\RequestContext');
$sc->register('matcher', 'Symfony\Component\Routing\Matcher\UrlMatcher')
    ->setArguments(array('%routes%', new Reference('context')));
//framework
$sc->register('framework', 'Simplex\Framework')
    ->setArguments(array(
        new Reference('dispatcher'),
        new Reference('matcher'),
        new Reference('controllerResolver'),
        new Reference('argumentResolver'),
    ))
;
//framework with cache
$sc->register('cacheStore', 'Symfony\Component\HttpKernel\HttpCache\Store')
	->setArguments(array('%caches%'));
$sc->register('esi', 'Symfony\Component\HttpKernel\HttpCache\Esi');
$sc->register('framework_cache', 'Symfony\Component\HttpKernel\HttpCache\HttpCache')
    ->setArguments(array(
        new Reference('framework'),
        new Reference('cacheStore'),
        new Reference('esi'),
        array('%debug%'),
    ));

return $sc;