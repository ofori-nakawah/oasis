<?php
namespace App\Traits;

/**
 * Trait ResponseWrapper
 * @package App\Traits
 *
 * Set responses that can be used globally throughout the app
 * Modify response codes and messages only
 */
trait Responses
{
    /**
     * @var array[]
     * Only modify data here
     */
    private $responses = [
        "input_validation_error" => [
            "message" => "Data validation error. Kindly check inputted data and try again.",
            "code" => 402
        ],
        "success" => [
            "message" => "Operation was successful",
            "code" => 200
        ],
        "not_found" => [
            "message" => "We could not find resource requested.",
            "code" => 404
        ],
        "db_operation_error" => [
            "message" => "Oops. Error while processing request.",
            "code" => 502
        ],
        "general_error" => [
            "message" => "Oops. An error occurred. Kindly try again later.",
            "code" => 502
        ],
        "unauthorized" => [
            "message" => "Unauthorized access.",
            "code" => 401
        ]
    ];

    /**
     * @param $response
     * @param $payload
     * @param string|null $message
     * @return \Illuminate\Http\JsonResponse
     * Response processing engine. Do not touch
     */
    private function process_response($response, $payload, string $message = null){
        return response()->json([
            "message" => ($message) ? $message : $response["message"],
            "status_code" => $response["code"],
            "data" => $payload
        ]);
    }

    /**
     * @param $payload
     * @param string|null $message
     * @return \Illuminate\Http\JsonResponse
     * Response types
     * Update this together with $response
     */
    public function data_validation_error_response($payload, string $message = null){return $this->process_response($this->responses["input_validation_error"], $payload, $message);}
    public function success_response($payload, string $message = null){return $this->process_response($this->responses["success"], $payload, $message);}
    public function not_found_response($payload, string $message = null){return $this->process_response($this->responses["not_found"], $payload, $message);}
    public function db_operation_error_response($payload, string $message = null){return $this->process_response($this->responses["db_operation_error"], $payload, $message);}
    public function unauthorized_response($payload, string $message = null){return $this->process_response($this->responses["unauthorized"], $payload, $message);}
    public function general_error_response($payload, string $message = null){return $this->process_response($this->responses["general_error"], $payload, $message);}
}
