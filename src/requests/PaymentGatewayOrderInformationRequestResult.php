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
       /* logger(  json_decode(
                json_encode($this->responseBody)));*/
        if (!$this->responseBody) {
            $this->status = null;
            $this->data = [];
            return;
        }

        $this->data =
            json_decode(
                json_encode(
                    (array)simplexml_load_string('<?xml version="1.0" encoding="UTF-8"?>'.$this->responseBody)
                ),
                false
            );
          
        $this->data=$this->data->row;
        return;
        $order = $this->data->row;
    
        $this->data = [
            'id'                    => $order->id,
            'OrderId'               => $order->OrderID??$order->id,
            'SessionId'             => $order->SessionID,
            'createDate'            => $order->createDate,
            'lastUpdateDate'        => $order->lastUpdateDate,
            'payDate'               => $order->payDate,
            'Amount'                => $order->Amount,
            'Currency'              => $order->Currency,
            'OrderLanguage'         => $order->OrderLanguage,
            'Description'           => $order->Description,
            'ApproveURL'            => $order->ApproveURL,
            'CancelURL'             => $order->CancelURL,
            'DeclineURL'            => $order->DeclineURL,
            'OrderStatus'           => $order->Orderstatus,
            'twoId'                 => $order->twoId,
            'RefundAmount'          => $order->RefundAmount,
            'RefundCurrency'        => $order->RefundCurrency,
            'Fee'                   => $order->Fee,
            'RefundDate'            => $order->RefundDate,
            'TWODate'               => $order->TWODate,
            'TWOTime'               => $order->TWOTime,
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
        return $this->data['OrderStatus'] ;
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