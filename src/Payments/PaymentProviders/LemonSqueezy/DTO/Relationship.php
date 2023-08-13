<?php

namespace Eduka\Nereus\Payments\PaymentProviders\LemonSqueezy\DTO;

class Relationship
{
    public array $store;
    public array $customer;
    public array $order_items;
    public array $license_keys;
    public array $subscriptions;
    public array $discount_redemptions;
}
