<?php


namespace TheCodingMachine\HttpInteropBridge;


use Interop\Http\Middleware\DelegateInterface;
use Interop\Http\Middleware\ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SymfonyToHttpInteropBridgeTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        // Symfony middleware that returns 'foo'
        $nextSymfonyMiddleware = new class implements HttpKernelInterface {
            public function handle(SymfonyRequest $request, $type = self::MASTER_REQUEST, $catch = true)
            {
                return new SymfonyResponse('foo');
            }
        };

        // HttpInterop middleware that appends 'bar' to the body
        $serverMiddlewareInterface = new class implements ServerMiddlewareInterface {
            public function process(ServerRequestInterface $request, DelegateInterface $delegate)
            {
                $response = $delegate->process($request);
                $response->getBody()->write('bar');
                return $response;
            }
        };

        $bridge = new SymfonyToHttpInteropBridge($nextSymfonyMiddleware, $serverMiddlewareInterface);

        $request = SymfonyRequest::create('/', 'GET');
        $response = $bridge->handle($request);

        $this->assertEquals('foobar', $response->getContent());
    }
}
