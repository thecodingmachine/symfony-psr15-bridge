<?php

namespace TheCodingMachine\HttpInteropBridge;

use Interop\Http\Middleware\DelegateInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * @var Request
     */
    private $symfonyRequest;

    /**
     * @param DelegateInterface $delegate
     */
    public function __construct(HttpKernelInterface $next, HttpFoundationFactoryInterface $httpFoundationFactory, HttpMessageFactoryInterface $httpMessageFactory, Request $symfonyRequest)
    {
        $this->next = $next;
        $this->httpFoundationFactory = $httpFoundationFactory;
        $this->httpMessageFactory = $httpMessageFactory;
        $this->symfonyRequest = $symfonyRequest;
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
        $newSymfonyRequest = $this->httpFoundationFactory->createRequest($request);

        // Now, let's modify the old mutable "symfonyRequest" using the new values
        $this->symfonyRequest->attributes = $newSymfonyRequest->attributes;
        $this->symfonyRequest->request = $newSymfonyRequest->request;
        $this->symfonyRequest->query = $newSymfonyRequest->query;
        $this->symfonyRequest->server = $newSymfonyRequest->server;
        $this->symfonyRequest->files = $newSymfonyRequest->files;
        $this->symfonyRequest->cookies = $newSymfonyRequest->cookies;
        $this->symfonyRequest->headers = $newSymfonyRequest->headers;

        $symfonyResponse = $this->next->handle($this->symfonyRequest);

        return $this->httpMessageFactory->createResponse($symfonyResponse);
    }
}
