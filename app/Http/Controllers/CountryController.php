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

    const CALLBACK_URL = "https://webhook.site/298880f0-8bda-480d-b822-733c0cc6c153";
    const RETURN_URL = "https://google.com";
    const CANCELLATION_URL = "https://facebook.com";
    const SECRET = "1917c20a-7ca7-43e4-b562-4dd22d0be19b";
    const KEY = "32179735-648f-4281-9c8e-ae24a10b303d";
    const CLIENT_ID = "JF6vaHO1l";
    const CLIENT_SECRET = "d19aa34839ffbde375e5d4dbb67268f3";

    private function getBasicAuthKey(){
//        return "Basic T3JEYUdIMTkyMDpqa2xhbjl1amRqaW9qcmU5NC1qYWQ4b2lrYWpyOQ==";
        return "Basic " . base64_encode(self::CLIENT_ID . ':' . self::CLIENT_SECRET);
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
        $xml->registerXPathNamespace('donation-details', 'http://www.sample.com/nnf-donation-details'); // ? ns not in use
        $xml->registerXPathNamespace('authentication', 'http://www.sample.com/authentication'); // ? ns not in use

        /**
         * setup authentication variables
         */
        $secret = (string) $xml->xpath('/soapenv:Envelope/soapenv:Header/authentication:secret')[0];
        $key = (string) $xml->xpath('/soapenv:Envelope/soapenv:Header/authentication:key')[0];

        /**
         * setup transaction variables we need to make a request to hubtel
         */
        $amount = (float) $xml->xpath('/soapenv:Envelope/soapenv:Body/donation-details:donationDetails/donation-details:amount')[0];
        $email = (string) $xml->xpath('/soapenv:Envelope/soapenv:Body/donation-details:donationDetails/donation-details:email')[0];

        Log::debug($key);
        Log::debug($secret);
        Log::debug($amount);
        Log::debug($email);

        /**
         * Authenticate user
         */
        if ($key !== self::KEY || $secret !== self::SECRET) {
            $response = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
             <soap:Body>
              <Response xmlns="'. url('/') .'/initiate-request-response">
               <returnPaymentInitiationResponse>
                  <statusCode>401</statusCode>
                  <message>Unauthorized</message>
               </returnPaymentInitiationResponse>
              </Response>
             </soap:Body>
            </soap:Envelope>';
            return response($response)
                ->header('Content-Type', 'text/xml');
        }

        /**
         * generete and prepare post data
         */
        $transaction = new Transaction();
        $transaction->client_reference  = uniqid() . "-" .Str::uuid()->toString();
        $transaction->amount = $amount;
        $transaction->email = $email;
        $transaction->status = "INITIATED";

        $transactionRefNo = $transaction->client_reference;

        $transactionDetails = array(
            'merchant_key' => 'JF6vaHO1l',
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
                'callback_url' => "https://oasis.myvork.com/api/v1/callback",
                'success_return_url' => self::RETURN_URL,
                'cancel_return_url' => self::CANCELLATION_URL,
                'landing_page_url' => self::RETURN_URL
            ),
            'currency_code' => 'USD',
            'paytype' => 'card',
            'email' => $email
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
            Log::error("cURL Error #:" . $error) ;
            $response = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
             <soap:Body>
              <Response xmlns="'. url('/') .'/initiate-request-response">
               <returnPaymentInitiationResponse>
                  <statusCode>500</statusCode>
                  <message>We encounted an issue while initiating transaction. Please try again later.</message>
               </returnPaymentInitiationResponse>
              </Response>
             </soap:Body>
            </soap:Envelope>';
        } else {
            Log::debug($response) ;
            $response = json_decode($response);
            $transaction->pay_link_url = $response->link;
            $transaction->status = "PENDING";
            try {
                $transaction->save();
            } catch (QueryException $e) {
                Log::error("ERROR UPDATING TRANSACTION PAY URL AND STATUS >>>>>>>>> " . $e);
            }

            /**
             * generate response
             */
            $response = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
             <soap:Body>
              <Response xmlns="'. url('/') .'/initiate-request-response">
               <returnPaymentInitiationResponse>
                  <statusCode>200</statusCode>
                  <message>Transaction initiated successfuly via Apptechhub (Streep)</message>
                  <transactionId>'. $transactionRefNo .'</transactionId>
                  <paymentLink>'. $transaction->pay_link_url .'</paymentLink>
               </returnPaymentInitiationResponse>
              </Response>
             </soap:Body>
            </soap:Envelope>';
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

    public function callback(Request $request)
    {
        $callbackResponse = json_decode($request);
        if ($callbackResponse === "" || $callbackResponse === null) {
            Log::error(' TRANSACTION CALLBACK DATA IS NULL');
        } else{
            Log::debug("Callback >>>>>>>>>> " . $callbackResponse);
            if ($callbackResponse->Data){
                $clientReference = $callbackResponse->Data->Transaction->TransactionId;
                $responseCode = $callbackResponse->Data->ResponseCode;
                $responseDescription = $callbackResponse->Data->ResponseMessage;
                $responseTransaction = $callbackResponse->Data->Transaction;

                $transaction = Transaction::where("client_reference", $clientReference)->first();
                if ($transaction){
                    $transaction->response_code = $responseCode;
                    $transaction->response_description = $responseDescription;
                    $transaction->amount = $responseTransaction->TransactionAmount;
                    $transaction->charges = 0;
                    $transaction->amount_after_charges = $responseTransaction->TransactionNet;

                    Log::info('UPDATE TRANSACTION DATA LOG >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>' . $transaction);

                    /**
                     * update the transaction status
                     */
                    if ($transaction->response_code === "0" || $transaction->response_code === 0) {
                        $transaction->status = "SUCCESSFUL";
                    } else {
                        $transaction->status = "FAILED";
                    }

                    $transaction->update();

                    /**
                     * Initiator Transaction alerts
                     */
                    $response = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
                 <soap:Body>
                  <Response xmlns="'. url('/') .'/request-callback">
                   <returnPaymentInitiationDetails>
                      <statusCode>'. $responseCode .'</statusCode>
                      <message>'. $responseDescription .'</message>
                    <transactionId>'. $clientReference .'</transactionId>
                   </returnPaymentInitiationDetails>
                  </Response>
                 </soap:Body>
                </soap:Envelope>';

                    /**
                     * Post the response back to the endpoint
                     */
                    $url = self::CALLBACK_URL;
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POSTFIELDS,
                        "xmlRequest=" . $response);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
                    $response = curl_exec($ch);
                    $error = curl_error($ch);
                    curl_close($ch);

                    if ($error) {
                        Log::error("ERROR SENDING CALLBACK TO INITIATOR >>>>>>>>>>>>>>>>>> " . $error);
                    } else {
                        Log::info("SUCCESSFUL CALLBACK PUSH");
                    }
                } else {
                    /**
                     * figure out a way to handle this
                     */
                }
            }
        }
    }
}
