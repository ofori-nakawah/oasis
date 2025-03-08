<?php


namespace App\Traits;

/**
 * Trait ResponseWrapper
 * @package App\Traits
 *
 * Set responses that can be used globally throughout the app
 * Modify response codes and messages only
 */
trait Utils
{
    public function convertDateStringToDateTime($dateString)
    {
        $date = new \DateTime($dateString, new \DateTimeZone('UTC'));
        return $date->format('Y-m-d H:i:s');
    }

}
