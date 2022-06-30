<?php

namespace Ab\TranzWarePaymentGateway\Requests;


use \Ab\TranzWarePaymentGateway\Requests\PaymentGatewayHTTPClientResultInterface;
/**
 * Class PaymentGatewayOrderInformationRequestResult
 *
 * @package Ab\TranzWarePaymentGateway\Requests
 */
class PaymentGatewayOrderReverseRequestResult implements PaymentGatewayRequestResultInterface
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
       
        $this->status = $response->Status;
       // dd($response);
        $this->data = [
            //'OrderId'       => $order->OrderID,
            'OrderStatus'   => $response->Status
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