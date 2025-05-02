<?php

namespace App\Services\Platforms;

use InvalidArgumentException;

class PlatformServiceFactory
{
    public static function make(string $platformSlug)
    {
        return match ($platformSlug) {
            'shopify' => new ShopifyService(),
            'prestashop' => new PrestashopService(),
            default => throw new InvalidArgumentException("No existe un servicio para la plataforma '{$platformSlug}'"),
        };
    }
}
