<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class CountryController extends Controller
{

    public function getSoapRequest(Request $request)
    {
        $xmlData = $request->getContent();

        /**
         * validate content
         */

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

        /**
         * generete and prepare post data
         */
        $transaction = new Transaction();
        $transaction->client_reference  = Str::uuid()->toString();
        $transaction->amount = $amount;
        $transaction->status = "INITIATED";

        $curl = curl_init();

        $payload = array(
            "amount" => $amount,
            "title" => "To build 6000TPY CASSAVA STARCH FACTORY and use the proceeds for charitable projects for deprived children",
            "description" => "To build an orphanage home & a school complex for children using the proceeds from the cassava starch factory",
            "clientReference" => $transaction->client_reference,
            "callbackUrl" => "https://oasis.myvork.com/api/v1/hubtelCallback",
            "cancellationUrl" => $cancellation_url,
            "returnUrl" => $return_url,
            "logo" => "https://dashboard.ordagh.com/assets/html-template/src/images/login-logo.png"
        );

        Log::debug($payload);

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
            $response = json_decode($response);
            $transaction->pay_link_url = $response->data->paylinkUrl;
            $transaction->pay_link_id = $response->data->paylinkId;
            try {
                $transaction->save();
            } catch (QueryException $e) {
                Log::debug($e);
            }

            /**
             * generate response
             */
            $response = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
             <soap:Body>
              <Response xmlns="'. url('/') .'">
               <returnPaymentInitiationDetails>
                  <statusCode>'. $response->code .'</statusCode>
                  <message>'. $response->message .'</message>
                  <transactionId>'. $response->data->clientReference .'</transactionId>
                  <paymentLink>'. $response->data->paylinkUrl .'</paymentLink>
                  <expiresAt>'. $response->data->expireIn .'</expiresAt>
               </returnPaymentInitiationDetails>
              </Response>
             </soap:Body>
            </soap:Envelope>';
        }

        return response($response)
            ->header('Content-Type', 'text/xml');
    }


    /**
     * **************************************************************************************
     * *********************** STREEP PAYMENT ***********************************************
     * **************************************************************************************
     */

    const CLIENT_ID = "JF6vaHO1l";
    const CLIENT_SECRET = "d19aa34839ffbde375e5d4dbb67268f3";

    private function getBasicAuthKey(){
//        return "Basic ".base64_encode(self::CLIENT_ID . ':' . self::CLIENT_SECRET);
        return "Basic T3JEYUdIMTkyMDpqa2xhbjl1amRqaW9qcmU5NC1qYWQ4b2lrYWpyOQ==";
    }


    public function initiateRequest(Request $request)
    {
        $xmlData = $request->getContent();

        /**
         * validate content
         */

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

        /**
         * generete and prepare post data
         */
        $transaction = new Transaction();
        $transaction->client_reference  = uniqid() . "-" .Str::uuid()->toString();
        $transaction->amount = $amount;
        $transaction->status = "INITIATED";

        $curl = curl_init();

        $transactionRefNo = uniqid();

        $transactionDetails = array(
            'merchant_key' => 'OrDaGH1920',
            'invoice' => array(
                'items' => [array(
                    'name' => 'Nurture Nature Foundation',
                    'price' => $amount,
                    'description' => 'To build an orphanage home & a school complex for children using the proceeds from the cassava starch factory',
                    'qty' => '1'
                )],
                'transaction_id' => $transactionRefNo,
                'invoice_description' => 'To build 6000TPY CASSAVA STARCH FACTORY and use the proceeds for charitable projects for deprived children',
                'total' => $amount,
                'callback_url' => "https://oasis.myvork.com/api/v1/hubtelCallback",
                'success_return_url' => $return_url,
                'cancel_return_url' => $cancellation_url,
                'landing_page_url' => $return_url
            ),
            'currency_code' => 'GHS',
            'paytype' => 'card',
            'email' => "ofori.nakawah@gmail.com"
        );

        Log::debug('TRANSACTION INITIATION DETAILS LOG >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> ' . json_encode($transactionDetails));

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://payment.apptechhubglobal.com/purchase/link",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>json_encode($transactionDetails),
            CURLOPT_HTTPHEADER => array(
                "Authorization: " . $this->getBasicAuthKey(),
                "Content-Type: application/json",
                "Cookie: XSRF-TOKEN=eyJpdiI6IlVSeTdzRUtDb2MxUTZUOFA5SCt5dXc9PSIsInZhbHVlIjoiS3M2ZHp1Skh4SVl3MWtEUGZhRFcwMXI1OVwvSXhJMFwveCtadEJ6UzZZRUhSdXNrZ2FjeHJpdDRNOHdURXBRZTZCQW0rdlZXSlE3XC9MXC9kaTVaMEVjSjN3PT0iLCJtYWMiOiIxMzc0OGI4NWU5NGJhZTQ4OTViNTExZGNjM2I5YTY5MmU0Y2U1MTNiOWVjNjFjZmUxNjIwODBiYWRjMzkyNDVlIn0%3D; laravel_session=eyJpdiI6IlpEazFwOGJscjBBRnlVcG9QNG5hN0E9PSIsInZhbHVlIjoiN2dIXC9pMEJtWFFVdnc3RnB4S3cxSnRFdWhHXC85bURuVFo5RzJ0VlRleUNKSVlKVjVka3JMSUdzdVBxdnNQODZ1XC85R3JrU1BzNzI1T3VSOUhGMW9lVWc9PSIsIm1hYyI6ImQwYjllYjU4MTcyNjE4MmI2MDkxZDZkNzFkODhlYzRhNGQ2NWU1M2M5NjI5OWQ4Yjk1YjQzMzExYWFhMTcwZjcifQ%3D%3D"
            ),
        ));

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            Log::debug("cURL Error #:" . $error) ;
        } else {
            Log::debug($response) ;
//            $response = json_decode($response);
//            $transaction->pay_link_url = $response->link;
            try {
//                $transaction->save();
            } catch (QueryException $e) {
                Log::debug($e);
            }

            /**
             * generate response
             */
            $response = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
             ';
        }

        return response($response)
            ->header('Content-Type', 'text/xml');
    }

    public function hubtelCallback(Request $request)
    {
        Log::debug("Callback >>>>>>>>>> " . $request->all());
        $client_reference = $request->data->clientReference;
        $transaction = Transaction::where("client_reference", $client_reference)
            ->where("pay_link_id", $request->data->paylinkId)
            ->first();
        if (!$transaction) {
            Log::alert("NO TRANSACTION RECORD FOUND FOR CLIENT REFERENCE >>>>>>>>>>>>>>>>>> " . $client_reference);
            return false;
        }

        $transaction->payment_type = $request->data->paymentType;
        $transaction->phone_number = $request->data->phoneNumber;
        $transaction->status = $request->responseCode;
        try {
            $transaction->update();
        } catch (QueryException $e) {
            Log::error($e);
        }
    }

    public function callback()
    {
        Log::debug("Callback >>>>>>>>>> " . $request->all());
    }
}
