<?php

namespace Ab\TranzWarePaymentGateway;

use Ab\TranzWarePaymentGateway\Handlers\PaymentGatewayOrderCallbackHandler;

/**
 * Class PaymentGatewayHandlerFactory
 *
 * @package Ab\TranzWarePaymentGateway
 */
class PaymentGatewayHandlerFactory implements PaymentGatewayHandlerFactoryInterface
{
    /**
     * @return Handlers\PaymentGatewayOrderCallbackHandler
     */
    final public function createOrderCallbackHandler()
    {
        return new PaymentGatewayOrderCallbackHandler();
    }
}