<?php
require_once('vendor/autoload.php');

use \Ab\TranzWarePaymentGateway\PaymentGatewayHandlerFactory;

$handlerFactory = new PaymentGatewayHandlerFactory();
$orderCallbackHandler = $handlerFactory->createOrderCallbackHandler();

$orderStatusData = $orderCallbackHandler->handle();

var_dump($orderStatusData);
