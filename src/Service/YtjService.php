<?php

namespace App\Service;

use App\Entity\CompanyInfo;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class YtjService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    function getCompanyInfo(string $id): CompanyInfo
    {
        $response = $this->client->request(
            'GET',
            'https://avoindata.prh.fi/bis/v1/'.$id
        );

        $statusCode = $response->getStatusCode();
        $content = $response->getContent();

        $data = json_decode($response->getContent(), true);

        $companyInfo = new CompanyInfo;
        $companyInfo->setName($data['results'][0]['name']);
        $companyInfo->setWebsite($data['results'][0]['name']);
        $companyInfo->setCurrentAddress(
            $data['results'][0]['addresses'][0]['street'].', '
            .$data['results'][0]['addresses'][0]['city'].', '
            .$data['results'][0]['addresses'][0]['postCode']);
        $companyInfo->setCurrentBusinessLine($data['results'][0]
        ['businessLines'][0]['code']);
        $companyInfo->setId($id);

        return $companyInfo;
    }
}