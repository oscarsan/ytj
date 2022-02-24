<?php

namespace App\Exception;


class YtjServiceException extends \RuntimeException
{

    protected int $http_code;

    public function __construct(string $message, int $http_code)
    {
        $this->http_code = $http_code;
        parent::__construct($message);
    }

    public function getHttpCode(): ?int{
        return $this->http_code;
    }

}