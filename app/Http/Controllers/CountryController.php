<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CountryController extends Controller
{
    public function getSoapRequest(Request $request)
    {
        $xmlData = $request->getContent();

        /**
         * convert request into something we can work with
         */
        $xml = simplexml_load_string($xmlData, NULL, NULL, "http://schemas.xmlsoap.org/soap/envelope/");

        /**
         * register your used namespace prefixes
         */
        $xml->registerXPathNamespace('soap-env', 'http://schemas.xmlsoap.org/soap/envelope/');
        $xml->registerXPathNamespace('payment-config', 'http://www.sample.com/nnf-payment-config'); // ? ns not in use
        $xml->registerXPathNamespace('authentication', 'http://www.sample.com/authentication'); // ? ns not in use

        /**
         * setup authentication variables
         */
        $secret = (string) $xml->xpath('/soapenv:Envelope/soapenv:Header/authentication:secret')[0];
        $appId = (string) $xml->xpath('/soapenv:Envelope/soapenv:Header/authentication:appId')[0];

        /**
         * setup transaction variables we need to make a request to hubtel
         */
        $amount = (float) $xml->xpath('/soapenv:Envelope/soapenv:Body/payment-config:getNNFPaymentConfig/payment-config:amount')[0];
        $cancellationUrl = (string) $xml->xpath('/soapenv:Envelope/soapenv:Body/payment-config:getNNFPaymentConfig/payment-config:cancellationUrl')[0];
        $returnUrl = (string) $xml->xpath('/soapenv:Envelope/soapenv:Body/payment-config:getNNFPaymentConfig/payment-config:returnUrl')[0];

        Log::debug($appId);
        Log::debug($secret);
        Log::debug($amount);
        Log::debug($cancellationUrl);
        Log::debug($returnUrl);

        /**
         * generate response
         */
        $response = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
             <soap:Body>
              <Response xmlns="http://xxx.gateway.xxx.abcd.com">
               <returnPaymentInitiationDetails>
                  <statusCode>200</statusCode>
                  <Message>Transaction initiated successfully</Message>
                  <transactionId>1234567</transactionId>
                  <paymentLink>https://ghanagov.com</paymentLink>
                  <expiresAt>43</expiresAt>
               </returnPaymentInitiationDetails>
              </Response>
             </soap:Body>
            </soap:Envelope>';

        return response($response)
            ->header('Content-Type', 'text/xml');
    }
}
