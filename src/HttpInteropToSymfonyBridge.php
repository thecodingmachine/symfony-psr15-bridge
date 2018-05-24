<?php

namespace TheCodingMachine\Psr15Bridge;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * A http-interop middleware that can be used to access Symfony middlewares.
 *
 * Note: Symfony middlewares do not have the notion of "next" middleware built-in, so the wrapped middleware will
 * ALWAYS return a response and NEVER pass the request to the "next" middleware.
 */
class HttpInteropToSymfonyBridge implements MiddlewareInterface, RequestHandlerInterface
{
    /**
     * @var HttpKernelInterface
     */
    private $symfonyMiddleware;
    /**
     * @var HttpMessageFactoryInterface
     */
    private $httpMessageFactory;
    /**
     * @var HttpFoundationFactoryInterface
     */
    private $httpFoundationFactory;

    /**
     * @param HttpKernelInterface            $symfonyMiddleware     The next Symfony middleware to be called (after the http-interop middleware.
     * @param HttpFoundationFactoryInterface $httpFoundationFactory The class in charge of translating PSR-7 request/response objects to Symfony objects. Defaults to Symfony default implementation
     * @param HttpMessageFactoryInterface    $httpMessageFactory    The class in charge of translating Symfony request/response objects to PSR-7 objects. Defaults to Symfony default implementation (that uses Diactoros)
     */
    public function __construct(HttpKernelInterface $symfonyMiddleware, HttpFoundationFactoryInterface $httpFoundationFactory = null, HttpMessageFactoryInterface $httpMessageFactory = null)
    {
        $this->symfonyMiddleware = $symfonyMiddleware;
        $this->httpFoundationFactory = $httpFoundationFactory ?: new HttpFoundationFactory();
        $this->httpMessageFactory = $httpMessageFactory ?: new DiactorosFactory();
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface|null $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {
        return $this->handle($request);
    }

    /**
     * Handle the request and return a response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $symfonyRequest = $this->httpFoundationFactory->createRequest($request);

        $symfonyResponse = $this->symfonyMiddleware->handle($symfonyRequest);

        return $this->httpMessageFactory->createResponse($symfonyResponse);
    }
}
