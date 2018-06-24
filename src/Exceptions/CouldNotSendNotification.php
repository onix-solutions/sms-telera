<?php

namespace OnixSolutions\SmsTelera\Exceptions;

use Exception;
use DomainException;

class CouldNotSendNotification extends Exception
{
    /**
     * Thrown when content length is greater than 800 characters.
     *
     * @return static
     */
    public static function contentLengthLimitExceeded()
    {
        return new static(
            'Notification was not sent. Content length may not be greater than 800 characters.'
        );
    }

    /**
     * Thrown when we're unable to communicate with smsc.telera.
     *
     * @param  DomainException  $exception
     *
     * @return static
     */
    public static function smscRespondedWithAnError(DomainException $exception)
    {
        return new static(
            "smsc.telera responded with an error '{$exception->getCode()}: {$exception->getMessage()}'"
        );
    }

    /**
     * Thrown when we're unable to communicate with smsc.telera.
     *
     * @param  Exception  $exception
     *
     * @return static
     */
    public static function couldNotCommunicateWithSmsc(Exception $exception)
    {
        return new static("The communication with smsc.telera failed. Reason: {$exception->getMessage()}");
    }
}
