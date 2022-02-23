<?php
// api/src/Entity/Company.php

namespace App\Entity;


class CompanyInfo
{
    private ?string $id = '';

    public ?string $name = null;

    public string $website = '';

    public string $current_address = '';

    public string $current_business_line = '';


    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
    
    public function setId($id) 
    {
        $this->id = $id;
    }
}