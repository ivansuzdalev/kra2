<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\VkUsersService;


class ApiController extends AbstractController
{

    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @Route("/get-users", name="get_users")
     */
    public function getUsers(ManagerRegistry $doctrine): Response
    {
        $serviceOb = new VkUsersService($this->client, $doctrine);

        $parsedData = $serviceOb->getApiUsers();
        if (count($parsedData) > 0) {
            $serviceOb->writeUsersData($parsedData);
        }

        return $this->render('api/getUsers.html.twig', [
            'controller_name' => 'API controller',
        ]);
    }

}