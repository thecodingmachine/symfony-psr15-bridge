# thecodingmachine/symfony-httpinterop-bridge

Bridges between [Symfony HttpKernel](http://symfony.com/doc/current/components/http_kernel/introduction.html) (a.k.a. [StackPHP Middleware](http://stackphp.com/)) and [HTTP-Interop middlewares (a.k.a PSR-15 middlewares)](https://github.com/http-interop/http-middleware) 


[![Latest Stable Version](https://poser.pugx.org/thecodingmachine/symfony-httpinterop-bridge/v/stable)](https://packagist.org/packages/thecodingmachine/symfony-httpinterop-bridge)
[![Total Downloads](https://poser.pugx.org/thecodingmachine/symfony-httpinterop-bridge/downloads)](https://packagist.org/packages/thecodingmachine/symfony-httpinterop-bridge)
[![Latest Unstable Version](https://poser.pugx.org/thecodingmachine/symfony-httpinterop-bridge/v/unstable)](https://packagist.org/packages/thecodingmachine/symfony-httpinterop-bridge)
[![License](https://poser.pugx.org/thecodingmachine/symfony-httpinterop-bridge/license)](https://packagist.org/packages/thecodingmachine/symfony-httpinterop-bridge)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thecodingmachine/symfony-httpinterop-bridge/badges/quality-score.png?b=0.4)](https://scrutinizer-ci.com/g/thecodingmachine/symfony-httpinterop-bridge/?branch=0.4)
[![Build Status](https://travis-ci.org/thecodingmachine/symfony-httpinterop-bridge.svg?branch=0.4)](https://travis-ci.org/thecodingmachine/symfony-httpinterop-bridge)
[![Coverage Status](https://coveralls.io/repos/thecodingmachine/symfony-httpinterop-bridge/badge.svg?branch=0.4&service=github)](https://coveralls.io/github/thecodingmachine/symfony-httpinterop-bridge?branch=0.4)

Those adapters are built on top of the existing [symfony/psr-http-message-bridge](https://github.com/symfony/psr-http-message-bridge) that bridges Symfony and PSR-7 HTTP messages.

> This bridge is currently based on http-interop v0.4. As this is not yet approved by PHP-FIG, this might be subject to change!

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

## Other known middleware adapters

I initially planned to submit this project as a PR to [h4cc/stack-psr7-bridge](https://github.com/h4cc/stack-psr7-bridge/) (that was developed before the notion of "PSR-7 middleware" was standardized in PSR-15).
I soon realized that this was in fact a complete rewrite so I decided to create a new project for it.
