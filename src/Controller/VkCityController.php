<?php

namespace App\Controller;

use App\Entity\VkTokens;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\VkUsers;
use App\Entity\Cities;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class VkCityController extends AbstractController
{

     
    /**
     * @Route("/vk/cities/list", name="app_vk_cities")
     */
    public function list(EntityManagerInterface $em, Request $request): Response
    {
        $repository = $em->getRepository(Cities::class);
        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->contains('city_id', 1938562));

        $articles = $repository->matching($criteria);

        return $this->render('vk_users/cities.html.twig', [
            'articles' => $articles
        ]);
    }
}
