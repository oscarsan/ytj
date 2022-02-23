<?php

namespace App\Service;

use App\Entity\CompanyInfo;

class YtjService
{
    public function __construct()
    {
        
    }

    function getCompanyInfo(string $id): CompanyInfo
    {
        $companyInfo = new CompanyInfo;

        $companyInfo->name =  "HashdotConsulting Oy";
        $companyInfo->setId($id);

        return $companyInfo;
    }
}