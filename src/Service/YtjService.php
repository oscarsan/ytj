<?php

namespace App\Service;

use App\Entity\CompanyInfo;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use App\Exception\YtjServiceException;

class YtjService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    function getCompanyInfo(string $id): CompanyInfo
    {


        $response = null;
        $statusCode = null;
        try {
            $response = $this->client->request(
                'GET',
                'https://avoindata.prh.fi/bis/v1/'.$id
            );
            $statusCode = $response->getStatusCode();
        }
        catch (TransportExceptionInterface $e){
            throw $e;
        }

        if ($statusCode == 404){
            throw new YtjServiceException('not company found in ytj with '.$id, 404);
        }else{

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

    function validate_company_id($company_id) {
        // Some old company id's have only 6 digits. They should be prefixed with 0.
        if (preg_match("/^[0-9]{6}\\-[0-9]{1}/", $company_id)) {
          $company_id = '0' . $company_id;
        }
      
        // Ensure that the company ID is entered in correct format.
        if (!preg_match("/^[0-9]{7}\\-[0-9]{1}/", $company_id)) {
          return FALSE;
        }
      
        list($id, $checksum) = explode('-', $company_id);
        $checksum = (int) $checksum;
      
        $total_count = 0;
        $multipliers = [7, 9, 10, 5, 8, 4, 2];
        foreach ($multipliers as $key => $multiplier) {
          $total_count = $total_count + $multiplier * $id[$key];
        }
      
        $remainder = $total_count % 11;
      
        // Remainder 1 is not valid.
        if ($remainder === 1) {
          return FALSE;
        }
      
        // Remainder 0 leads into checksum 0.
        if ($remainder === 0) {
          return $checksum === $remainder;
        }
      
        // If remainder is not 0, the checksum should be remainder deducted from 11.
        return $checksum === 11 - $remainder;
      }
}