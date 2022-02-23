<?php
// src/Controller/YtjController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\CompanyInfo;
use App\Service\YtjService;
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

        $companyInfo = $this->ytjService->getCompanyInfo($id);

        $data = array(
            'id' => $companyInfo->getId(),
            'name' => $companyInfo->getName(),
        );
        return $this->json($data);
        //$response = new Response(json_encode($data), 200);
        //$response->headers->set('Content-Type', 'application/json');
        //return $response;

    }
}