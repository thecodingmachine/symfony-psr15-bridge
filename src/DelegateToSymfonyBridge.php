<?php

namespace TheCodingMachine\HttpInteropBridge;

use Interop\Http\Middleware\DelegateInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class DelegateToSymfonyBridge implements DelegateInterface
{
    /**
     * @var HttpKernelInterface
     */
    private $next;

    /**
     * @var HttpFoundationFactoryInterface
     */
    private $httpFoundationFactory;
    /**
     * @var HttpMessageFactoryInterface
     */
    private $httpMessageFactory;

    /**
     * @param DelegateInterface $delegate
     */
    public function __construct(HttpKernelInterface $next, HttpFoundationFactoryInterface $httpFoundationFactory, HttpMessageFactoryInterface $httpMessageFactory)
    {
        $this->next = $next;
        $this->httpFoundationFactory = $httpFoundationFactory;
        $this->httpMessageFactory = $httpMessageFactory;
    }

    /**
     * Dispatch the next available middleware and return the response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    public function process(RequestInterface $request)
    {
        $symfonyRequest = $this->httpFoundationFactory->createRequest($request);

        $symfonyResponse = $this->next->handle($symfonyRequest);

        return $this->httpMessageFactory->createResponse($symfonyResponse);
    }
}
