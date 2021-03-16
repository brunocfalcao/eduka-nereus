<?php

namespace Eduka\Nereus\Services;

class Analytics
{
    public static function __callStatic($method, $args)
    {
        return AnalyticsService::new()->{$method}(...$args);
    }
}

class AnalyticsService
{
    public function __construct()
    {
        //
    }

    public static function new(...$args)
    {
        return new self(...$args);
    }
}
