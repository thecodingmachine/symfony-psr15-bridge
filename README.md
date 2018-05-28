# thecodingmachine/symfony-psr15-bridge

Bridges between [Symfony HttpKernel](http://symfony.com/doc/current/components/http_kernel/introduction.html) (a.k.a. [StackPHP Middleware](http://stackphp.com/)) and [HTTP-Interop middlewares (a.k.a PSR-15 middlewares)](https://github.com/psr15/http-middleware) 


[![Latest Stable Version](https://poser.pugx.org/thecodingmachine/symfony-psr15-bridge/v/stable)](https://packagist.org/packages/thecodingmachine/symfony-psr15-bridge)
[![Total Downloads](https://poser.pugx.org/thecodingmachine/symfony-psr15-bridge/downloads)](https://packagist.org/packages/thecodingmachine/symfony-psr15-bridge)
[![Latest Unstable Version](https://poser.pugx.org/thecodingmachine/symfony-psr15-bridge/v/unstable)](https://packagist.org/packages/thecodingmachine/symfony-psr15-bridge)
[![License](https://poser.pugx.org/thecodingmachine/symfony-psr15-bridge/license)](https://packagist.org/packages/thecodingmachine/symfony-psr15-bridge)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thecodingmachine/symfony-psr15-bridge/badges/quality-score.png?b=0.4)](https://scrutinizer-ci.com/g/thecodingmachine/symfony-psr15-bridge/?branch=0.4)
[![Build Status](https://travis-ci.org/thecodingmachine/symfony-psr15-bridge.svg?branch=0.4)](https://travis-ci.org/thecodingmachine/symfony-psr15-bridge)
[![Coverage Status](https://coveralls.io/repos/thecodingmachine/symfony-psr15-bridge/badge.svg?branch=0.4&service=github)](https://coveralls.io/github/thecodingmachine/symfony-psr15-bridge?branch=0.4)

Those adapters are built on top of the existing [symfony/psr-http-message-bridge](https://github.com/symfony/psr-http-message-bridge) that bridges Symfony and PSR-7 HTTP messages.

> This bridge is currently based on psr15 v0.4. As this is not yet approved by PHP-FIG, this might be subject to change!

## Installation

The recommended way to install symfony-psr15-bridge is through [Composer](http://getcomposer.org/):

```sh
composer require thecodingmachine/symfony-psr15-bridge
```

## Usage

By default, the Symfony HttpFoundation and HttpKernel are used.
For PSR-7, the [Zend-Diactoros](https://github.com/zendframework/zend-diactoros) implementation is used.
These implementations can be changed if needed.

### Wrapping a HttpKernel

```php
<?php

// Use the Psr15ToSymfonyBridge adapter
$psr15Middleware = new Psr15ToSymfonyBridge($yourHttpKernel);

// Handling PSR-7 requests
$psr7Response = $psr15Middleware->process($psr7Request, $dummyNextPsr7Middleware);
```

**Important:** Symfony Http Kernels do not have the notion of "next" middlewares. Therefore, the "next" PSR-7 middleware
you pass to the `process` method will never be called.

### Wrapping a PSR-7 callback


```php
<?php

// Use the Psr15ToSymfonyBridge adapter
$symfonyKernel = new SymfonyToPsr15Bridge($nextSymfonyMiddleware, $yourPsr15Middleware);

// Handling Symfony requests
$symfonyResponse = $symfonyKernel->handle($symfonyRequest);
```

Note: the adapter's contructor takes 2 middlewares: the "next" Symfony middleware that will be called by the "delegate"
psr15 feature and the psr15 middleware to be wrapped.

## Other known middleware adapters

I initially planned to submit this project as a PR to [h4cc/stack-psr7-bridge](https://github.com/h4cc/stack-psr7-bridge/) (that was developed before the notion of "PSR-7 middleware" was standardized in PSR-15).
I soon realized that this was in fact a complete rewrite so I decided to create a new project for it.
