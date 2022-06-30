<?php

namespace Ab\TranzWarePaymentGateway;

/**
 * Interface PaymentGatewayUrlProviderInterface
 *
 * @package Ab\TranzWarePaymentGateway
 */
interface PaymentGatewayUrlProviderInterface
{
    /**
     * @param string $url
     *
     * @return $this
     */
    public function setGatewayUrl($url);

    /**
     * @return string
     */
    public function getGatewayUrl();

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setOnOrderApprovedUrl($url);

    /**
     * @return string
     */
    public function getOnOrderApprovedUrl();

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setOnOrderDeclinedUrl($url);

    /**
     * @return string
     */
    public function getOnOrderDeclinedUrl();

    /**
     * @param string $url
     *
     * @return this
     */
    public function setOnOrderCanceledUrl($url);

    /**
     * @return string
     */
    public function getOnOrderCanceledUrl();
}