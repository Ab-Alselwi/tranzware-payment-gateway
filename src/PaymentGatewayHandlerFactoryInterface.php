<?php

namespace Ab\TranzWarePaymentGateway;

use \Ab\TranzWarePaymentGateway\Handlers\PaymentGatewayHandlerInterface;

/**
 * Interface PaymentGatewayHandlerFactoryInterface
 *
 * @package Ab\TranzWarePaymentGateway
 */
interface PaymentGatewayHandlerFactoryInterface
{
    /**
     * Returns a new instance of PaymentGatewayHandlerInterface
     * that will handle callbacks after order creation
     *
     * @return PaymentGatewayHandlerInterface
     */
    public function createOrderCallbackHandler();
}