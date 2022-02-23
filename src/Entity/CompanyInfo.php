<?php
// api/src/Entity/Company.php

namespace App\Entity;


class CompanyInfo
{
    private ?string $id = '';

    private ?string $name = '';

    private string $website = '';

    private string $current_address = '';

    private string $current_business_line = '';


    public function getId(): ?string
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    public function getCurrentAddress(): ?string
    {
        return $this->current_address;
    }
    public function setCurrentAddress($current_address)
    {
        $this->current_address = $current_address;
    }

    public function getCurrentBusinessLine(): ?string
    {
        return $this->current_business_line;
    }
    public function setCurrentBusinessLine($current_business_line)
    {
        $this->current_business_line = $current_business_line;
    }

}