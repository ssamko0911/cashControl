<?php declare(strict_types=1);

namespace App\Exception;

use Exception;
use Throwable;

class CurrencyApiClientException extends Exception
{
    public string $publicMessage = 'Sorry, we are unable to process your request. Please try again later.';

    public function __construct(string $message, int $code, Throwable $previous = null){
        parent::__construct($message, $code, $previous);
    }
}