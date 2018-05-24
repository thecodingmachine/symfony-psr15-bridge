<?php

namespace TheCodingMachine\Psr15Bridge;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SymfonyToHttpInteropBridgeTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        // Symfony middleware that returns 'foo'
        $nextSymfonyMiddleware = new class implements HttpKernelInterface
         {
             public function handle(SymfonyRequest $request, $type = self::MASTER_REQUEST, $catch = true)
             {
                 return new SymfonyResponse('foo');
             }
         };

        // HttpInterop middleware that appends 'bar' to the body
        $middlewareInterface = new class implements MiddlewareInterface
         {
             public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
             {
                 $response = $delegate->process($request, $delegate);
                 $response->getBody()->write('bar');

                 return $response;
             }
         };

        $bridge = new SymfonyToHttpInteropBridge($nextSymfonyMiddleware, $middlewareInterface);

        $request = SymfonyRequest::create('/', 'GET');
        $response = $bridge->handle($request);

        $this->assertEquals('foobar', $response->getContent());
    }
}
