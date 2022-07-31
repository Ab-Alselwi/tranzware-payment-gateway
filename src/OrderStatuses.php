<?php

namespace Ab\TranzWarePaymentGateway;

class OrderStatuses 
{

    const CREATED           = 'CREATED';
    const ON_LOCK           = 'ON-LOCK';
    const ON_PAYMENT        = 'ON-PAYMENT';
    const APPROVED          = 'APPROVED';
    const CANCELED          = 'CANCELED';
    const DECLINED          = 'DECLINED';
    const REVERSED          = 'REVERSED';
    const ON_REFUND         = 'ON-REFUND';
    const REFUNDED          = 'REFUNDED';
    const PREAUTH_APPROVED  = 'PREAUTH-APPROVED';
    const EXPIRED           = 'EXPIRED';
    const ERROR             = 'ERROR';

    public static function sanitizeValue($value)
    {
        if (!preg_match('/^[A-Za-z_]+?/', $value)) {
            return '';
        }
       return str_replace('-','_',$value);
    }

    public static function isValid($orderStatus)
    {
        $allowedTypes = array_keys(static::getAllData());

        return in_array($orderStatus, $allowedTypes);
    }

    public static function fromString($orderStatus)
    {
        $orderStatus = self::sanitizeValue($orderStatus);
        return self::isValid($orderStatus) ? $orderStatus : null;
    }

    /**
        // Completion means Convert status from PRE_AUTH_APPROVED to APPROVED
        // 'an authorization transaction for a purchase with pre-authorization has been completed (funds are reserved on the account to complete the peration); ',
    **/

    public static function isCanCompletion($orderStatus){

        $allowedTypes = [
            self::PREAUTH_APPROVED,
        ];

        return in_array($orderStatus, $allowedTypes);
    }

    
    /**
        // Reverse means Convert status from PRE_AUTH_APPROVED to REVERSED , So the money will reversed to customer , it's like refund but without tax
        // 'an authorization transaction for a purchase with pre-authorization has been completed (funds are reserved on the account to complete the peration); ',
    **/

    public static function isCanReverse($orderStatus){

        $allowedTypes = [
            self::PREAUTH_APPROVED,
        ];

        return in_array($orderStatus, $allowedTypes);
    }

    /**
        // Refund means Convert status from APPROVED to Refanded , So the money will refund to customer 
        
    **/

    public static function isCanRefund($orderStatus){

        $allowedTypes = [
            self::APPROVED,
        ];

        return in_array($orderStatus, $allowedTypes);
    }


    public static function getAllData():array
    {
        return [
                 'CREATED'          => 'CREATED',
                 'ON_LOCK'          => 'ON-LOCK',
                 'ON_PAYMENT'       => 'ON-PAYMENT',
                 'APPROVED'         => 'APPROVED',
                 'CANCELED'         => 'CANCELED',
                 'DECLINED'         => 'DECLINED',
                 'REVERSED'         => 'REVERSED',
                 'ON_REFUND'        => 'ON-REFUND',
                 'REFUNDED'         => 'REFUNDED',
                 'PREAUTH_APPROVED' => 'PREAUTH-APPROVED',
                 'EXPIRED'          => 'EXPIRED',
                 'ERROR'            => 'ERROR',
            ];
    }

    public static function getAllStatusesWithInfo():array
    {

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
    }
}