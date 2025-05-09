<?php

namespace KuCoin\UniversalSDK\Model;

interface InterceptorInterface
{
    /**
     * Return a Guzzle middleware callable that can be pushed to the HandlerStack.
     *
     * @return callable
     */
    public function middleware(): callable;
}