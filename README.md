# thecodingmachine/symfony-httpinterop-bridge

Bridges between [Symfony HttpKernel](http://symfony.com/doc/current/components/http_kernel/introduction.html) (a.k.a. [StackPHP Middleware](http://stackphp.com/)) and [HTTP-Interop middlewares (a.k.a PSR-15 middlewares)](https://github.com/http-interop/http-middleware) 


[![Latest Stable Version](https://poser.pugx.org/thecodingmachine/symfony-httpinterop-bridge/v/stable)](https://packagist.org/packages/thecodingmachine/symfony-httpinterop-bridge)
[![Total Downloads](https://poser.pugx.org/thecodingmachine/symfony-httpinterop-bridge/downloads)](https://packagist.org/packages/thecodingmachine/symfony-httpinterop-bridge)
[![Latest Unstable Version](https://poser.pugx.org/thecodingmachine/symfony-httpinterop-bridge/v/unstable)](https://packagist.org/packages/thecodingmachine/symfony-httpinterop-bridge)
[![License](https://poser.pugx.org/thecodingmachine/symfony-httpinterop-bridge/license)](https://packagist.org/packages/thecodingmachine/symfony-httpinterop-bridge)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thecodingmachine/symfony-httpinterop-bridge/badges/quality-score.png?b=0.3)](https://scrutinizer-ci.com/g/thecodingmachine/symfony-httpinterop-bridge/?branch=0.3)
[![Build Status](https://travis-ci.org/thecodingmachine/symfony-httpinterop-bridge.svg?branch=0.3)](https://travis-ci.org/thecodingmachine/symfony-httpinterop-bridge)
[![Coverage Status](https://coveralls.io/repos/thecodingmachine/symfony-httpinterop-bridge/badge.svg?branch=0.3&service=github)](https://coveralls.io/github/thecodingmachine/symfony-httpinterop-bridge?branch=0.3)

Those adapters are built on top of the existing [symfony/psr-http-message-bridge](https://github.com/symfony/psr-http-message-bridge) that bridges Symfony and PSR-7 HTTP messages.

> This bridge is currently based on http-interop v0.3. As this is not yet approved by PHP-FIG, this might be subject to change!

## Installation

The recommended way to install symfony-httpinterop-bridge is through [Composer](http://getcomposer.org/):

```sh
composer require thecodingmachine/symfony-httpinterop-bridge
```

## Usage

By default, the Symfony HttpFoundation and HttpKernel are used.
For PSR-7, the [Zend-Diactoros](https://github.com/zendframework/zend-diactoros) implementation is used.
These implementations can be changed if needed.

### Wrapping a HttpKernel

```php
<?php

// Use the HttpInteropToSymfonyBridge adapter
$httpInteropMiddleware = new HttpInteropToSymfonyBridge($yourHttpKernel);

// Handling PSR-7 requests
$psr7Response = $httpInteropMiddleware->process($psr7Request, $dummyNextPsr7Middleware);
```

**Important:** Symfony Http Kernels do not have the notion of "next" middlewares. Therefore, the "next" PSR-7 middleware
you pass to the `process` method will never be called.

### Wrapping a PSR-7 callback


```php
<?php

// Use the HttpInteropToSymfonyBridge adapter
$symfonyKernel = new SymfonyToHttpInteropBridge($nextSymfonyMiddleware, $yourHttpInteropMiddleware);

// Handling Symfony requests
$symfonyResponse = $symfonyKernel->handle($symfonyRequest);
```

Note: the adapter's contructor takes 2 middlewares: the "next" Symfony middleware that will be called by the "delegate"
http-interop feature and the http-interop middleware to be wrapped.

### Symfony and the immutable PSR-7 requests

A big difference between PSR-7 requests and Symfony requests is that PSR-7 requests are immutable. On the other hand, Symfony request/response objects are expected to be mutable.
As such, it is common in Symfony to store requests in a "request stack" object, modify them and retrieve them later. This is a use case that is typically not recommended (and actually impossible) with PSR-7.

The bridge needs to deal with this. When the `$nextSymfonyMiddleware` is called, the initial Symfony request that was passed to the `SymfonyToHttpInteropBridge` is modified to adapt to any changes performed by the PSR-15 middleware.
So while the PSR-7 request objects are immutable, the Symfony request object is the same instance that is modified.

## Other known middleware adapters

I initially planned to submit this project as a PR to [h4cc/stack-psr7-bridge](https://github.com/h4cc/stack-psr7-bridge/) (that was developed before the notion of "PSR-7 middleware" was standardized in PSR-15).
I soon realized that this was in fact a complete rewrite so I decided to create a new project for it.
