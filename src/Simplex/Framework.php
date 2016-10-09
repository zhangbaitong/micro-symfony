<?php

namespace Simplex;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel;


class Framework implements HttpKernelInterface
{
	private $dispatcher;
    protected $matcher;
    protected $controllerResolver;
    protected $argumentResolver;

    public static function getInstance(){
        $routes = include __DIR__.'/../app.php';
        //matcher
        $context = new Routing\RequestContext();
        $matcher = new Routing\Matcher\UrlMatcher($routes, $context);
        //resolver
        $controllerResolver = new ControllerResolver();
        $argumentResolver = new ArgumentResolver();
        //dispatcher
        $dispatcher = new EventDispatcher();
        // $dispatcher->addSubscriber(new Simplex\ContentLengthListener());
        // $dispatcher->addSubscriber(new Simplex\GoogleListener());
        // $dispatcher->addSubscriber(new HttpKernel\EventListener\ExceptionListener('Calendar\\Controller\\ErrorController::exceptionAction'));
        // $dispatcher->addSubscriber(new HttpKernel\EventListener\ResponseListener('UTF-8'));
        // $dispatcher->addSubscriber(new HttpKernel\EventListener\StreamedResponseListener());
        // $dispatcher->addSubscriber(new Simplex\StringResponseListener());

        $framework = new Framework($dispatcher, $matcher, $controllerResolver, $argumentResolver);
        $framework = new HttpKernel\HttpCache\HttpCache(
            $framework,
            new HttpKernel\HttpCache\Store(__DIR__.'/../cache')
            // new HttpKernel\HttpCache\Esi(),
            // array('debug' => true)
        );

        return $framework;
    }

    public function __construct(
    	EventDispatcher $dispatcher,
    	UrlMatcherInterface $matcher,
    	ControllerResolverInterface $controllerResolver,
    	ArgumentResolverInterface $argumentResolver)
    {
    	$this->dispatcher = $dispatcher;
        $this->matcher = $matcher;
        $this->controllerResolver = $controllerResolver;
        $this->argumentResolver = $argumentResolver;
    }

    public function handle(
    	Request $request,
        $type = HttpKernelInterface::MASTER_REQUEST,
        $catch = false)
    {
        // RouterListener implementation
        // $requestStack = new RequestStack();
        // $dispatcher->addSubscriber(new HttpKernel\EventListener\RouterListener($matcher, $requestStack));
        $this->matcher->getContext()->fromRequest($request);

        try {
            $request->attributes->add($this->matcher->match($request->getPathInfo()));

            $controller = $this->controllerResolver->getController($request);
            $arguments = $this->argumentResolver->getArguments($request, $controller);

            $response = call_user_func_array($controller, $arguments);
        } catch (ResourceNotFoundException $e) {
            $response = new Response('Not Found', 404);
        } catch (\Exception $e) {
            $response = new Response('An error occurred', 500);
        }

        // dispatch a response event
        $this->dispatcher->dispatch('response', new ResponseEvent($response, $request));

		// $date = date_create_from_format('Y-m-d H:i:s', '2005-10-15 10:00:00');
		// $response->setCache(array(
		//     'public'        => true,
		//     'etag'          => 'abcde',
		//     'last_modified' => $date,
		//     'max_age'       => 10,
		//     's_maxage'      => 10,
		// ));
		//validation model
		// $response->setETag('whatever_you_compute_as_an_etag');
		// if ($response->isNotModified($request)) {
		//     return $response;
		// }
		// $response->setContent('The computed content of the response');

        return $response;
    }
}