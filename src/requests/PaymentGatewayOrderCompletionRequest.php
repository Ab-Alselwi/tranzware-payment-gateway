<?php

namespace Ab\TranzWarePaymentGateway\Requests;

use \Ab\TranzWarePaymentGateway\OrderTypes;
use \Ab\TranzWarePaymentGateway\Requests\PaymentGatewayRequestSettings as RequestSettings;
use \Ab\TranzWarePaymentGateway\Requests\PaymentGatewayHTTPClient;

/**
 * Class PaymentGatewayOrderRequest
 *
 * @package Ab\TranzWarePaymentGateway\Requests
 */
class PaymentGatewayOrderCompletionRequest implements PaymentGatewayRequestInterface
{
    use RequestSettings;

    private $requestAttributes = [];
    private $debugToFile = null;

    /**
     * PaymentGatewayOrderRequest constructor.
     *
     * @param string $requestUrl
     * @param string $merchantId
     * @param string $orderId
     * @param string $sessionId
     * @param float  $amount
     * @param string $currency
     * @param string $description
     * @param string $lang
     * @param string $debugToFile
     */
    public function __construct(
        $requestUrl, $merchantId,$orderId, $sessionId, $amount, $currency,
        $description = '', $lang = 'EN', $debugToFile = null
    ) {
        $this->requestAttributes =
            compact(
                'requestUrl', 'merchantId', 'orderId', 'sessionId', 'amount', 'currency',
                'description', 'lang'
            );
        $this->debugToFile = $debugToFile;
    }

    public function execute()
    {
        $ssl = [
            'key' => $this->sslKey,
            'keyPass' => $this->sslKeyPass,
            'cert' => $this->sslCertificate
        ];
        $httpClient = new PaymentGatewayHTTPClient(
            $this->requestAttributes['requestUrl'],
            $this->getRequestBody(),
            $ssl,
            $this->strictSSL
        );
        if ($this->debugToFile) {
            $httpClient->setDebugToFile($this->debugToFile);
        }
        return new PaymentGatewayOrderCompletionRequestResult($httpClient->execute());
    }

    final private function getRequestBody()
    {
        $templateFile = __DIR__ . '/templates/OrderCompletionRequestBodyTemplate.xml';
        $body = file_get_contents($templateFile);
        foreach ($this->requestAttributes AS $key => $value) {
            $body = str_replace('{{' . $key . '}}', $value, $body);
        }
        return $body;
    }
}