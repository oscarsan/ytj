<?php

namespace App\Exception;


class ServerException extends \RuntimeException
{

    public function __construct(string $message)
    {
        parent::__construct("hardcore transport exception" . $message);
    }

}