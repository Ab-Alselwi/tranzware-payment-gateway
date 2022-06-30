<?php

namespace Ab\TranzWarePaymentGateway\Requests;

//use PaymentGatewayRequestSettings as RequestSettings;
use \Ab\TranzWarePaymentGateway\Requests\PaymentGatewayHTTPClient;
use Ab\TranzWarePaymentGateway\Requests\PaymentGatewayRequestInterface;
/**
 * Class PaymentGatewayOrderStatusRequest
 *
 * @package Ab\TranzWarePaymentGateway\Requests
 */
class PaymentGatewayOrderReverseRequest implements PaymentGatewayRequestInterface
{
    use PaymentGatewayRequestSettings;

    private $requestAttributes = [];
    private $debugToFile = null;

    /**
     * PaymentGatewayOrderStatusRequest constructor.
     *
     * @param string $requestUrl
     * @param string $merchantId
     * @param string $orderId
     * @param string $sessionId
     * @param string $description (optional)
     * @param int $paymentSubjectType (optional)
     * @param int $quantity (optional)
     * @param int $paymentType (optional)
     * @param int $paymentMethodType (optional)
     * @param string $tranId (optional)
     * @param int $source (optional)
     * @param string $lang (optional)
     * @param string $debugToFile (optional)
     */
    public function __construct(
        $requestUrl, $merchantId, $orderId, $sessionId,$description = 'xxxxxxxx',
        $lang = 'EN', $debugToFile = null
    ) {
        
        $this->requestAttributes =
            compact('requestUrl','merchantId',  'orderId', 'sessionId','description',
                 'lang'
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
        return new PaymentGatewayOrderReverseRequestResult($httpClient->execute());
    }

    final private function getRequestBody()
    {
        $templateFile = __DIR__ . '/templates/ReverseOrderRequestBodyTemplate.xml';
        $body = file_get_contents($templateFile);
        foreach ($this->requestAttributes AS $key => $value) {
            $body = str_replace('{{' . $key . '}}', $value, $body);
        }
        return $body;
    }
}