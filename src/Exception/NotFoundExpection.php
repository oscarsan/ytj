<?php

namespace App\Exception;


class NotFoundException extends \RuntimeException
{

    public function __construct(string $id)
    {
        parent::__construct("no company found with that id " . $id);
    }

}