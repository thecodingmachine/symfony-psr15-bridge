<?php

namespace TheCodingMachine\Psr15Bridge;

use Psr\Http\Server\MiddlewareInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * A Symfony middleware that can be used to access psr15 middlewares.
 */
class SymfonyToPsr15Bridge implements HttpKernelInterface
{
    /**
     * The psr15 middleware we bridge to.
     *
     * @var MiddlewareInterface
     */
    private $psr15Middleware;

    /**
     * @var HttpMessageFactoryInterface
     */
    private $httpMessageFactory;
    /**
     * @var HttpKernelInterface
     */
    private $nextSymfonyMiddleware;
    /**
     * @var HttpFoundationFactoryInterface
     */
    private $httpFoundationFactory;

    /**
     * @param HttpKernelInterface            $nextSymfonyMiddleware The next Symfony middleware to be called (after the psr15 middleware.
     * @param MiddlewareInterface            $psr15Middleware The psr15 middleware we bridge to.
     * @param HttpFoundationFactoryInterface $httpFoundationFactory The class in charge of translating PSR-7 request/response objects to Symfony objects. Defaults to Symfony default implementation
     * @param HttpMessageFactoryInterface    $httpMessageFactory    The class in charge of translating Symfony request/response objects to PSR-7 objects. Defaults to Symfony default implementation (that uses Diactoros)
     */
    public function __construct(HttpKernelInterface $nextSymfonyMiddleware, MiddlewareInterface $psr15Middleware, HttpFoundationFactoryInterface $httpFoundationFactory = null, HttpMessageFactoryInterface $httpMessageFactory = null)
    {
        $this->nextSymfonyMiddleware = $nextSymfonyMiddleware;
        $this->psr15Middleware = $psr15Middleware;
        $this->httpFoundationFactory = $httpFoundationFactory ?: new HttpFoundationFactory();
        $this->httpMessageFactory = $httpMessageFactory ?: new DiactorosFactory();
    }

    /**
     * Handles a Request to convert it to a Response.
     *
     * When $catch is true, the implementation must catch all exceptions
     * and do its best to convert them to a Response instance.
     *
     * @param Request $request A Request instance
     * @param int     $type    The type of the request
     *                         (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
     * @param bool    $catch   Whether to catch exceptions or not
     *
     * @return Response A Response instance
     *
     * @throws \Exception When an Exception occurs during processing
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true): Response
    {
        $psr7Request = $this->httpMessageFactory->createRequest($request);

        $psr7Response = $this->psr15Middleware->process($psr7Request,
            new Psr15ToSymfonyBridge($this->nextSymfonyMiddleware, $this->httpFoundationFactory, $this->httpMessageFactory));

        return $this->httpFoundationFactory->createResponse($psr7Response);
    }
}
