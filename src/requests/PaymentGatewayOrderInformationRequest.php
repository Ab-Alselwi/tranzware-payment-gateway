<?php

namespace Ab\TranzWarePaymentGateway\Requests;

use \Ab\TranzWarePaymentGateway\Requests\PaymentGatewayRequestSettings as RequestSettings;
use \Ab\TranzWarePaymentGateway\Requests\PaymentGatewayHTTPClient;

/**
 * Class PaymentGatewayOrderStatusRequest
 *
 * @package Ab\TranzWarePaymentGateway\Requests
 */
class PaymentGatewayOrderInformationRequest implements PaymentGatewayRequestInterface
{
    use RequestSettings;

    private $requestAttributes = [];
    private $debugToFile = null;

    /**
     * PaymentGatewayOrderStatusRequest constructor.
     *
     * @param string $merchantId
     * @param string $requestUrl
     * @param string $orderId
     * @param string $sessionId
     * @param string $lang (optional)
     * @param string $debugToFile (optional)
     */
    public function __construct(
        $requestUrl, $merchantId, $orderId, $sessionId,
        $lang = 'EN', $debugToFile = null
    ) {
        $this->requestAttributes =
            compact('merchantId', 'requestUrl', 'orderId', 'sessionId', 'lang');
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
        return new PaymentGatewayOrderInformationRequestResult($httpClient->execute());
    }

    final private function getRequestBody()
    {
        $templateFile = __DIR__ . '/templates/OrderInformationRequestBodyTemplate.xml';
        $body = file_get_contents($templateFile);
        foreach ($this->requestAttributes AS $key => $value) {
            $body = str_replace('{{' . $key . '}}', $value, $body);
        }
        return $body;
    }
}