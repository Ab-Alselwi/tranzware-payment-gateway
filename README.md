# TranzWare Payment Gateway (TWPG) API

This package was inspired by :

- [open-payment-solutions/tranzware-payment-gateway](https://github.com/Open-Payment-Solutions/TranzWarePaymentGatewayApi)

---

**Description:**

Library for working with **TranzWare Payment Gateway (TWPG, also known as TWEC PG)** Service <br/>
provided by CompassPlus , kapitalbank

- [Features] [kapitalbank] (https://pg.kapitalbank.az/docs)
  - Purchase
  - PreAuth (Added)
  - OrderCompletion (Added)
  - OrderInformation (Added)
  - Reverse (Added)
  - Refund (Added)
  - GetOrderStatus

---

**Installation:**

```
composer require ab-alselwi/tranzware-payment-gateway
```

---

**Usage examples:**

laravel code , you can write a service . for examples :

```php
use Ab\TranzWarePaymentGateway\PaymentGatewayRequestFactory;
use Ab\TranzWarePaymentGateway\PaymentGatewayHandlerFactory;
use Ab\TranzWarePaymentGateway\CurrencyCodes;
use Ab\TranzWarePaymentGateway\OrderTypes;
use Ab\TranzWarePaymentGateway\OrderStatuses;

class PaymentService
{

   private $keyFile, $keyPass, $certFile, $mode,$lang;
   private $requestFactory,$merchant_handler,$merchant;

   protected $url = null;
   private  $order_type=OrderTypes::PRE_AUTH;  //OrderTypes
   private  $currency=CurrencyCodes::AZN; //CurrencyCodes
   protected $order;
   private $amount;
   private string $description='';
   private array $order_details=[];

    public function __construct( $certificate = false, $key = false)
    {

       $this->setMode();

        $certs_link = base_path() . '/payment/';
		if ($this->mode == 'test'){
		        $this->merchant_handler = "https://e-commerce.kapitalbank.az:5443/Exec";
		        $this->client_handler = "https://e-commerce.kapitalbank.az/index.jsp";
		        $this->certFile = $certs_link.'/test/test.crt';
		        $this->keyFile = $certs_link.'/test/test.key';
		        $this->merchant = 'E1000010';

		 }
	  $this->setRequest();
    }

    private function setMode(){
        if (!App::environment('production')) {
            $this->mode = Config('payment.test_mode',true) ? 'test' : 'production';
        }else{
            $this->mode = 'production';
        }
    }

 	protected function setRequest(){

         $lang=request()->lang??"en";
         $url=config('app.url').'/';

         $this->requestFactory = new PaymentGatewayRequestFactory(
            $this->merchant_handler,
            $this->merchant,
            $url . $lang . '/payment/order_approved',
            $url . $lang . '/payment/order_declined',
            $url . $lang . '/payment/order_canceled',
            strtoupper($lang)
        );

         $this->requestFactory
            ->setCertificate($this->certFile, $this->keyFile)
            ->disableSSLVerification() // for dev environment or if no need to validate SSL host
            ->setDebugFile(storage_path()."/logs/payment-debug-".date('Y-m-d').".log");
    }


    public function getOrderStatus(){

        $handlerFactory = new PaymentGatewayHandlerFactory();
        $orderCallbackHandler = $handlerFactory->createOrderCallbackHandler();
        $orderStatusData=$orderCallbackHandler->handle();
        $this->setState($orderStatusData['OrderStatus']);
        return $orderStatusData;
    }

     public function getPreAuthOrder(){

        return $this->requestFactory
                    ->createOrderPreAuthRequest($this->amount, $this->currency , $this->description);
    }

    public function getPurshaseOrder(){

        return $this->requestFactory
                    ->createOrderPurchaseRequest($this->amount, $this->currency , $this->description);
    }

    public function createOrderRequest()
    {

        if($this->order_type==OrderTypes::PRE_AUTH){
            $orderRequest= $this->getPreAuthOrder();
            return $this->getExecute($orderRequest);
        }

         if($this->order_type==OrderTypes::PURCHASE){
            $orderRequest= $this->getPurshaseOrder();
             return $this->getExecute($orderRequest);
         }
       return false;
    }

    public function getExecute($orderRequest)
    {
        if(!$orderRequest){
            return false;
        }

       $orderRequestResult = $orderRequest->execute();
       if($orderRequestResult->getStatus()){
           $this->state = $orderRequestResult->getStatus();
           if ($orderRequestResult->success()) {
                return $orderRequestResult->getData();
            }
          }
        return false;

    }

   public function setOrderCompletionRequest($order_id, $session_id,$amount, $currency=CurrencyCodes::AZN, $description = '')
   {

        $request = $this->requestFactory
                        ->createOrderCompletionRequest($order_id, $session_id,$amount, $currency, $description);
        return $this->getExecute($request);
   }

   public function setOrderReverseRequest($order_id, $session_id,$description = '')
   {

        $request = $this->requestFactory->createOrderReverseRequest($order_id, $session_id,$description);
        return $this->getExecute($request);
   }

   public function setOrderRefundRequest($order_id, $session_id,$amount, $currency=null, $description = 'refund test')
   {
        $currency=$currency??$this->currency;
        $request = $this->requestFactory->createOrderRefundRequest($order_id, $session_id,$amount, $currency, $description);
        return $this->getExecute($request);
   }

   public function createOrderStatusRequest($order_id,$session_id)
    {
        $request = $this->requestFactory
                        ->createOrderStatusRequest($order_id, $session_id);

        $result = $request->execute();

        if($result->getStatus()){
            $data = $result->getData();
            $this->setState($data['OrderStatus']);
            return $data;
        }
        return false;
    }

   public function getOrderInformation($order_id,$session_id) :array|null
    {
        $request = $this->requestFactory
                        ->createOrderInformationRequest($order_id, $session_id);

        $result = $request->execute();
        if($result->getStatus()){
            $data= $result->getData();

            if(isset($data['OrderStatus'])){
                $this->setState($data['OrderStatus']);
            }

            return $data;
        }
        return null;
    }

    public function setState($state):void
    {
        $this->state = $state;
    }

    public function isApproved():bool
    {
        return $this->getState()=='approved';
    }

    public function getState():bool|string
    {

        if ($this->state === false) {
            return false;
        }

        $state = strtolower($this->state);
        switch ($state) {
            case 'canceled':
            case 'declined':
            case 'error':
                $state = 'failed';
                break;
            case 'on-lock':
            case 'on-payment':
            case 'created':
                $state = 'created';
                break;
            case 'preauth-approved':
            case 'approved':
                $state = 'approved';
                break;
        }
        return $state;
    }

    public function getStatusData(){
        return $this->getOrderStatus();
    }
}
```

now in your controller

```php
 public function __construct(PaymentService $PaymentService,$order_type=OrderTypes::PRE_AUTH)
    {
        $this->order_type=$order_type;
        $this->paymentService=$PaymentService;
    }
```

#### pay.

```php
 public function pay(Request $request)
    {
		$this->paymentService
            ->setAmount($request->amount * 100)
            ->setCurrency(CurrencyCodes::AZN)
            ->setDescription($request->title)
            ->setOrderType($this->order_type);

        $orderData=$this->paymentService->createOrderRequest();

        if($orderData){

            return redirect($orderData['PaymentUrl']);
        }
    }
```

```php

  public function approved(Request $request)
    {
    	//get returned order status
        $orderStatusData = $this->paymentService->getStatusData();
         if ($this->paymentService->isApproved()) {
         	//write your code
         }
    }

    public function declined(Request $request)
    {
        $orderStatusData = $this->paymentService->getStatusData();
        //write your code
	}

    public function canceled()
    {
        $orderStatusData = $this->paymentService->getStatusData();
        //write your code
    }
```

#### Completion.

```php
$orderRequestResult=$this->paymentService->setOrderCompletionRequest($order_id, $session_id,$amount);
```

#### Reverse.

```php
 $orderRequestResult=$this->paymentService->setOrderReverseRequest($order_id, $session_id);
```

#### Refund.

```php
$orderRequestResult=$this->paymentService->setOrderRefundRequest($order_id, $session_id, $amount ,$currency,$description);
```

#### OrderInfo.

```php
$orderStatusDataBank = $this->paymentService->createOrderStatusRequest($order_id, $session_id);
```

#### OrderDetails.

```php
$orderStatusDataBank = $this->paymentService->getOrderInformation($order_id, $session_id);
```

#### StatusesWithInfo.

```php
use Ab\TranzWarePaymentGateway\OrderStatuses;

//return  true or false
// true if current status = PREAUTH-APPROVED
OrderStatuses::isCanCompletion($order_status)

```

```php
//return  true or false
// true if current status = PREAUTH-APPROVED

 return OrderStatuses::isCanReverse($order_status) ;
```

```php
//return  true or false
// true if current status = APPROVED

return OrderStatuses::isCanRefund($order_status);
```

Statuses information

```php

     return [

				[
                    'title'=>'CREATED',
                    'code'=>'CREATED',
                    'description'=>'created (set after generating the OrderID and SessionID until the payment for the order is made)',
                ],
                [
                    'title'=>'ON-LOCK',
                    'code'=>'ON-LOCK',
                    'description'=>'blocked (to avoid duplication of payment for goods) ; Attention:-The ON-LOCK status is set at the start of the PayOrder procedure. The order has an ON-LOCK status until the end of the authorization process, after which the order status changes to APPROVED or DECLINED. The ON-LOCK status is set to an order in the PREAUTH-APPROVED status when the; Completion administrative operation is initialized . The order has the ON-LOCK status until the end of the post-authorization, after which the order status changes to APPROVED or to the original status in case of unsuccessful operation.',
                ],
                [
                    'title'=>'ON-PAYMENT',
                    'code'=>'ON-PAYMENT',
                    'description'=>'on payment (the order is being paid for); Attention! The ON-PAYMENT status is set after entering information on the card.',
                ],
                [
                    'title'=>'APPROVED',
                    'code'=>'APPROVED',
                    'description'=>'approved (payment was successful);',
                ],
                [
                    'title'=>'CANCELED',
                    'code'=>'CANCELED',
                    'description'=>'canceled (the client interrupts the operation);',
                ],
                [
                    'title'=>'DECLINED',
                    'code'=>'DECLINED',
                    'description'=>'refusal to pay (for example, if an error Prefix not found occurred during the execution of the order);',
                ],
                [
                    'title'=>'REVERSED',
                    'code'=>'REVERSED',
                    'description'=>'reversed',
                ],
                [
                    'title'=>'ON-REFUND',
                    'code'=>'ON-REFUND',
                    'description'=>'blocked for the time of the return of goods (to avoid duplication of the return of goods); Attention! The ON-REFUND status is set when the Refund administrative operation is initialized . The order has the ON REFUND status until the end of the return of the goods, after which the order status changes to REFUNDED or to the original status in case of unsuccessful operatio',
                ],
                [
                    'title'=>'REFUNDED',
                    'code'=>'REFUNDED',
                    'description'=>'goods have been returned;',
                ],
                [
                    'title'=>'PREAUTH-APPROVED',
                    'code'=>'PREAUTH-APPROVED',
                    'description'=>'an authorization transaction for a purchase with pre-authorization has been completed (funds are reserved on the account to complete the peration); ',
                ],
                [
                    'title'=>'EXPIRED',
                    'code'=>'EXPIRED',
                    'description'=>' the order has expired;',
                ],
                [
                    'title'=>'ERROR',
                    'code'=>'ERROR',
                    'description'=>'error (connection error with TWEC PG database, POS driver or TPTP terminal).',
                ],

            ];
```

See [samples](samples) folder added by (https://github.com/Open-Payment-Solutions/TranzWarePaymentGatewayApi)

---

- [Integration manual (in Russian language)](docs/Integration_Instruction_TWEC_PG.pdf)
- [Integration manual (in English language)](docs/Integration_Instruction_TWEC_PG-En.pdf)
