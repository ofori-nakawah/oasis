<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class TransactionController extends Controller
{

    public function initiate_transaction(Request $request)
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
        $cancellation_url = (string) $xml->xpath('/soapenv:Envelope/soapenv:Body/payment-config:getNNFPaymentConfig/payment-config:cancellationUrl')[0];
        $return_url = (string) $xml->xpath('/soapenv:Envelope/soapenv:Body/payment-config:getNNFPaymentConfig/payment-config:returnUrl')[0];

        Log::debug($appId);
        Log::debug($secret);
        Log::debug($amount);
        Log::debug($cancellation_url);
        Log::debug($return_url);

        $curl = curl_init();

        $payload = array(
            "amount" => $amount,
            "title" => "To build 6000TPY CASSAVA STARCH FACTORY and use the proceeds for charitable projects for deprived children",
            "description" => "To build an orphanage home & a school complex for children using the proceeds from the cassava starch factory",
            "clientReference" => Str::uuid()->toString(),
            "callbackUrl" => "https://nnf.shopinary.shop/api/v1/transactions/initiate-transaction",
            "cancellationUrl" => $cancellation_url,
            "returnUrl" => $return_url,
            "logo" => "https://dashboard.ordagh.com/assets/html-template/src/images/login-logo.png"
        );

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Basic " . base64_encode("bxvjoezq:desbqwqg")
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_URL => "https://devp-reqsendmoney-230622-api.hubtel.com/request-money/233245563498",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            Log::debug("cURL Error #:" . $error) ;
        } else {
            Log::debug($response) ;
        }

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

    public function callback(Request $request)
    {
        Log::debug("Callback >>>>>>>>>> " . $request->all());
    }
}
