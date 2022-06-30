<?php

namespace Ab\TranzWarePaymentGateway\Requests;

/**
 * Class PaymentGatewayHTTPClientResult
 *
 * @package Ab\TranzWarePaymentGateway\Requests
 */
class PaymentGatewayHTTPClientResult implements PaymentGatewayHTTPClientResultInterface
{
    private $info;
    private $output;

    /**
     * PaymentGatewayHTTPClientResult constructor.
     *
     * @param mixed $output
     * @param mixed $info
     */
    public function __construct($output, $info)
    {
        $this->info = $info;
        $this->output = $output;
    }

    /**
     * @return mixed
     */
    final public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return mixed
     */
    final public function getInfo()
    {
        return $this->info;
    }
}