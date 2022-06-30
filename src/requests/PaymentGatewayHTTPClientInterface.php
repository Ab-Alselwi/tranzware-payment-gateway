<?php

namespace Ab\TranzWarePaymentGateway\Requests;

/**
 * Interface PaymentGatewayHTTPClientInterface
 *
 * @package Ab\TranzWarePaymentGateway\Requests
 */
interface PaymentGatewayHTTPClientInterface
{
    /**
     * PaymentGatewayHTTPClientInterface constructor.
     *
     * @param string $url
     * @param null   $body
     * @param null   $sslCertificate
     * @param bool   $strictSSL
     */
    public function __construct($url, $body = null, $sslCertificate = null, $strictSSL = true);

    /**
     * @param  string $path_to_file
     * @return void
     */
    public function setDebugToFile($path_to_file);

    /**
     * @return PaymentGatewayHTTPClientResultInterface
     */
    public function execute();
}