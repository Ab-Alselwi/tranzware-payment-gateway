<?php

namespace Ab\TranzWarePaymentGateway\Requests;

/**
 * Interface PaymentGatewayHTTPClientResultInterface
 *
 * @package Ab\TranzWarePaymentGateway\Requests
 */
interface PaymentGatewayHTTPClientResultInterface
{
    public function __construct($output, $info);

    /**
     * Returns request info (headers, status and etc)
     *
     * @return mixed
     */
    public function getInfo();

    /**
     * Returns raw http output
     *
     * @return mixed
     */
    public function getOutput();
}