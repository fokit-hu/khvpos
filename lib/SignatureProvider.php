<?php declare(strict_types=1);

namespace KHBankTools\PaymentGateway;

class SignatureProvider
{
    private string $privateKeyPath;
    private string $privateKeyPassphrase;
    private $privateKey;
    
    public function __construct(string $privateKeyPath, string $privateKeyPassphrase = '', bool $lazyKeyload = true)
    {
        if (!\is_file($privateKeyPath)) {
            throw new \LogicException(sprintf('Private Key not exists at path: "%s".', $privateKeyPath));
        }
        
        if (!\function_exists('openssl_sign')) {
            throw new \LogicException('OpenSSL extension is required.');
        }

        if ($lazyKeyload === true) {
            $this->privateKeyPath = $privateKeyPath;
            $this->privateKeyPassphrase = $privateKeyPassphrase;
        }
        else {
            $this->privateKey = self::doLoadKey(file_get_contents($privateKeyPath), $privateKeyPassphrase);
        }
    }
    
    private static function doLoadKey(string $privateKeyString, string $passphrase = '')/* : resource */
    {
        $privateKey = \openssl_get_privatekey($privateKeyString, $passphrase);
        
        if ($privateKey === false) {
            $error = self::getLastOpenSSLErrorMessage();
            throw new \LogicException(sprintf('Failed to read private key. OpenSSL error: "%s".', $error));
        }
        
        return $privateKey;
    }
    
    private static function getLastOpenSSLErrorMessage(): string
    {
        $buffer = '';
        while ($message = \openssl_error_string()) {
            $buffer .= $message;
        }
        
        return $buffer;
    }
    
    private function loadKey(): void
    {
        if ($this->privateKey !== null) {
            return;
        }
        
        $this->privateKey = self::doLoadKey(file_get_contents($this->privateKeyPath), $this->privateKeyPassphrase);
    }
    
    public function __destruct()
    {
        if (is_resource($this->privateKey) && PHP_VERSION_ID < 80000) {
            \openssl_free_key($this->privateKey);
        }
    }
    
    private function getFieldSignOrder(): array
    {
        return [
            PaymentRequestArguments::MERCHANT_ID_REQUEST_ARGUMENT_NAME => 'merchantId',
            PaymentRequestArguments::TRANSACTION_ID_REQUEST_ARGUMENT_NAME => 'transactionId',
            PaymentRequestArguments::PAYMENT_TYPE_REQUEST_ARGUMENT_NAME => 'paymentType',
            PaymentRequestArguments::AMOUNT_REQUEST_ARGUMENT_NAME => 'amount',
            PaymentRequestArguments::CURRENCY_REQUEST_ARGUMENT_NAME => 'currency',
        ];
    }
    
    private function buildQueryToSign(PaymentRequestArguments $arguments): string
    {
        $buffer = '';
        foreach ($this->getFieldSignOrder() as $requestArgumentName => $fieldName)
        {
            $buffer .= '&'. $requestArgumentName .'='. $arguments->{'get'.ucfirst($fieldName)}();
        }

        return ltrim($buffer, '&');
    }
    
    public function sign(PaymentRequestArguments $arguments): string
    {
        $this->loadKey();
        
        $queryString = $this->buildQueryToSign($arguments);
        
        $signature = '';
        
        \openssl_sign($queryString, $signature, $this->privateKey, OPENSSL_ALGO_SHA1);

        return \bin2hex($signature);
    }
}