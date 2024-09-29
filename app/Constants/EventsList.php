<?php

namespace App\Constants;

enum EventsList: string
{
        case APPLICATION_CONFIRMED = 'APPLICATION_CONFIRMED';
        case APPLICATION_DECLINED = 'APPLICATION_DECLINED';
        case JOB_CANCELLED = 'JOB_CANCELLED';
        case JOB_CLOSED = 'JOB_CLOSED';
        case QUOTE_SUBMITTED = 'QUOTE_SUBMITTED';
        case QUOTE_RECEIVED = 'QUOTE_RECEIVED';
        case SUCCESSFUL_JOB_APPLICATION = 'SUCCESSFUL_JOB_APPLICATION';

        public function toString(): string
        {
                return match ($this) {
                        self::APPLICATION_CONFIRMED => 'APPLICATION_CONFIRMED',
                        self::APPLICATION_DECLINED => 'APPLICATION_DECLINED',
                        self::SUCCESSFUL_JOB_APPLICATION  => 'SUCCESSFUL_JOB_APPLICATION',
                        self::JOB_CLOSED  => 'JOB_CLOSED',
                        self::QUOTE_SUBMITTED  => 'QUOTE_SUBMITTED',
                        self::QUOTE_RECEIVED  => 'QUOTE_RECEIVED',
                        self::JOB_CANCELLED  => 'JOB_CANCELLED',
                        default => '',
                };
        }
}
