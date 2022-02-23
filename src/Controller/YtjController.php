<?php
// src/Controller/YtjController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\YtjService;

use App\Exception\NotFoundException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class YtjController extends AbstractController
{
    public function __construct(
    private readonly SerializerInterface $serializer,
    private YtjService $ytjService)
    {
    }
    /**
     * @Route("/ytj/{id}", name="ytj_indentifier")
     */
    public function number(string $id): Response
    {

        $companyInfo = null;

        try {
            $companyInfo = $this->ytjService->getCompanyInfo($id);
        } catch (TransportExceptionInterface $e){
            $response = new Response('error', 500);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } catch (NotFoundException){
             $response = new Response('Record not found', 404);
             $response->headers->set('Content-Type', 'application/json');
             return $response;
        }

        $data = array(
           'id' => $companyInfo->getId(),
           'name' => $companyInfo->getName(),
           'website' => $companyInfo->getwebsite(),
           'current_address' => $companyInfo->getCurrentAddress(),
           'current_business_line' => $companyInfo->getCurrentBusinessLine(),
        );

        $response = new Response($this->json($data), 200);
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }
}