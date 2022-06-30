<?php

namespace Ab\TranzWarePaymentGateway\Requests;

use \Ab\TranzWarePaymentGateway\Requests\PaymentGatewayHTTPClientResultInterface;

/**
 * Class PaymentGatewayOrderInformationRequestResult
 *
 * @package Ab\TranzWarePaymentGateway\Requests
 */
class PaymentGatewayOrderInformationRequestResult implements PaymentGatewayRequestResultInterface
{
    private $httpStatus;
    private $responseBody;
    private $status;
    private $data;

    /**
     * PaymentGatewayOrderStatusRequestResult constructor.
     *
     * @param PaymentGatewayHTTPClientResultInterface $HTTPClientResult
     */
    public function __construct(PaymentGatewayHTTPClientResultInterface $HTTPClientResult)
    {
        $this->responseBody = $HTTPClientResult->getOutput();
        $info = $HTTPClientResult->getInfo();
        $this->httpStatus = $info['http_code'];

        if (!$this->responseBody) {
            $this->status = null;
            $this->data = [];
            return;
        }

        $this->data =
            json_decode(
                json_encode(
                    (array)simplexml_load_string($this->responseBody)
                ),
                false
            );

        $response = $this->data->Response;
        $order = $response->Order;
        $this->status = $response->Status;
        $this->data = [
            'OrderId'       => $order->OrderID,
            'OrderStatus'   => $order->OrderStatus,
            'Amount'        => $order->Amount,
            'createDate'    => $order->createDate,
            'pay_date'      => $order->pay_date,
            'pay_amount'    => $order->pay_amount,
            'currency'      => $order->currency,
            'refund_amount' => $order->refund_amount,
            'refund_amount' => $order->refund_amount,
            'refund_date'   => $order->refund_date,
            'SessionId'     => $order->sessionid,

        ];
    }

    public function getHttpStatus()
    {
        return $this->httpStatus;
    }

    final public function getResponseBody()
    {
        $this->responseBody;
    }

    final public function success()
    {
        return $this->data['OrderStatus'] === 'APPROVED';
    }

    final public function getStatus()
    {
        return $this->status;
    }

    final public function getData()
    {
        return $this->data;
    }
}