<?php

namespace App\Service;

use App\Entity\CompanyInfo;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use App\Exception\YtjServiceException;
use Exception;
use Psr\Log\LoggerInterface;

class YtjService
{
    private $client;
    private $logger;

    public function __construct(HttpClientInterface $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    function getCompanyInfo(string $id): CompanyInfo
    {
        if (!$this->validate_company_id($id)){
            throw new YtjServiceException('not a valid company id '. $id, 406);
        }

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

        if ($statusCode != 200){
            throw new YtjServiceException('error from ytj service for '.$id, $statusCode);
        }else{

            $data = json_decode($response->getContent(), true);

            $companyInfo = new CompanyInfo;

            try {
                $companyInfo->setName($data['results'][0]['name']);
                $companyInfo->setWebsite($this->get_web_site($data['results'][0]['contactDetails']));
                $companyInfo->setCurrentAddress(
                    $this->get_current_address($data['results'][0]['addresses']));
                $companyInfo->setCurrentBusinessLine(
                    $this->get_current_business_line($data['results'][0]['businessLines']));
                $companyInfo->setId($id);
            }
            catch (Exception $e){
                throw new YtjServiceException('Error parsing information for '.$id, 500);
            }

            return $companyInfo;
        }
    }

    function get_current_address(array $addresses): string{
        if (empty($addresses)) return '';

        usort($addresses, fn ($a, $b) => strtotime($b["registrationDate"]) - strtotime($a["registrationDate"]));

        return $addresses[0]['street'].', '
        .$addresses[0]['city'].', '
        .$addresses[0]['postCode'];
    }

    function get_current_business_line(array $business_lines): string{
        if (empty($business_lines)) return '';
        usort($business_lines, fn ($a, $b) => strtotime($b["registrationDate"]) - strtotime($a["registrationDate"]));

        return $business_lines[0]['code'];
    }

    function get_web_site(array $contact_details): string{
        if (empty($contact_details)) return '';

        $filtered_details = array_filter($contact_details,
        fn($contact_detail) => $contact_detail["type"] == "www-adress");

        if (empty($filtered_details)) return '';
        usort($filtered_details, fn ($a, $b) => strtotime($b["registrationDate"]) - strtotime($a["registrationDate"]));

        return $filtered_details[0]['value'];
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