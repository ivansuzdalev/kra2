<?php

namespace App\Controller;

use App\Entity\VkTokens;
use App\Service\VkUsersService;
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
use App\Service\TranslitToCyr;


class VkUsersController extends AbstractController
{

    public function getAndWhereCriteriaByFilter($criteria, $field_name, $request, $date = false): Criteria
    {


        $cmd = $request->query->get($field_name . 'Cmd');

        $value = $request->query->get($field_name);
        
        if (count(explode("From", $field_name)) == 2) {
            $field_name = explode("From", $field_name)[0];
        }

        if (count(explode("To", $field_name)) == 2) {
            $field_name = explode("To", $field_name)[0];
        }


        if ($date && !empty($value)) {
            try {
                $value = new DateTime($value);
            } catch (Exception $e) {
                print_r($e);
            }
        }

        if ($date && $cmd == 'contains') {
            $cmd = 'off';
        }
        if ($date && $cmd == 'nte') {
            $cmd = 'off';
        }

        switch ($cmd) {
            case 'contains':
                $criteria->andWhere(Criteria::expr()->contains($field_name, $value));
                break;
            case 'eq':
                $criteria->andWhere(Criteria::expr()->eq($field_name, $value));
                break;
            case 'neq':
                $criteria->andWhere(Criteria::expr()->neq($field_name, $value));
                break;
            case 'gt':
                $criteria->andWhere(Criteria::expr()->gt($field_name, $value));
                break;
            case 'gte':
                $criteria->andWhere(Criteria::expr()->gte($field_name, $value));
                break;
            case 'lt':
                $criteria->andWhere(Criteria::expr()->lt($field_name, $value));
                break;
            case 'lte':
                $criteria->andWhere(Criteria::expr()->lte($field_name, $value));
                break;
            case 'isNull':
                $criteria->andWhere(Criteria::expr()->isNull($field_name));
                break;
            case 'isNotNull':
                $criteria->andWhere(Criteria::expr()->neq($field_name, null));
                break;
            case 'nte':
                $criteria->andWhere(Criteria::expr()->neq($field_name, ''));
                break;
            default:
                break;

        }


        return $criteria;
    }

    /**
     * @Route("/write-token", name="write_token")
     */
    public function accessToken(Request $request, EntityManagerInterface $em): Response
    {

        $accessToken = $request->query->get('access_token');
        if (!empty($accessToken)) {
            $tokens = new VkTokens();
            $tokens = $tokens->setToken($accessToken);
            $tokens = $tokens->setDatetime(new DateTime('now'));
            echo $tokens->getToken();
            $em->persist($tokens);
            $em->flush();
            return new Response(json_encode(array('success' => True)));
        }
        return $this->render('vk_users/index.html.twig', [
            'controller_name' => 'VkUsersController',
        ]);
    }

    /**
     * @Route("/vk/users/get-token", name="app_get_token")
     */
    public function getToken(): Response
    {
        return $this->render('vk_users/getToken.html.twig', []);
    }


    /**
     * @Route("/vk/users/view", name="app_vk_users_view")
     */
    public function view(EntityManagerInterface $em, Request $request, VkUsersService $vkUsersService): Response
    {
        $id = $request->query->get('id');
        $userData = $vkUsersService->getUserData($id, $em);
        $userData['id'] = $id;
        return $this->render('vk_users/view.html.twig', ['userData'=>$userData]);
    }

    /**
     * @param $request
     * @return Criteria
     */
    public function createFilterCriteria($request): Criteria
    {
        $criteria = Criteria::create();
        $criteria = $this->getAndWhereCriteriaByFilter($criteria, 'userId', $request);
        $criteria = $this->getAndWhereCriteriaByFilter($criteria, 'firstName', $request);
        $criteria = $this->getAndWhereCriteriaByFilter($criteria, 'lastName', $request);
        $criteria = $this->getAndWhereCriteriaByFilter($criteria, 'nickname', $request);
        $criteria = $this->getAndWhereCriteriaByFilter($criteria, 'maidenName', $request);
        $criteria = $this->getAndWhereCriteriaByFilter($criteria, 'city', $request);
        $criteria = $this->getAndWhereCriteriaByFilter($criteria, 'country', $request);
        $criteria = $this->getAndWhereCriteriaByFilter($criteria, 'mobilePhone', $request);
        $criteria = $this->getAndWhereCriteriaByFilter($criteria, 'lastSeenFrom', $request, true);
        $criteria = $this->getAndWhereCriteriaByFilter($criteria, 'lastSeenTo', $request, true);
        $criteria = $this->getAndWhereCriteriaByFilter($criteria, 'screenName', $request);
        $criteria = $this->getAndWhereCriteriaByFilter($criteria, 'online', $request);
        $criteria = $this->getAndWhereCriteriaByFilter($criteria, 'skype', $request);
        return $this->getAndWhereCriteriaByFilter($criteria, 'military', $request);
    }

    /**
     * @Route("/vk/users/export-to-csv", name="app_vk_users_export_to_csv")
     */
    public function exportDataToCsv(EntityManagerInterface $em, Request $request)
    {

        $repository = $em->getRepository(VkUsers::class);
        $criteria = $this->createFilterCriteria($request);
        $articles = $repository->matching($criteria);
        $transliterate = new TranslitToCyr();
        $lists = [];
        if(count($articles) > 0) {
            foreach ($articles as $index=>$article) {
                $record = $article->getRecordData();
                $row = [];
                $row['city'] = $record['city']['title'] ?? '';
                $row['cityCyr'] = isset($record['city']['title']) ? $transliterate->translitToCyr($record['city']['title']) : '';
                $row['firstName'] = $record['first_name'] ?? '';
                $row['firstNameCyr'] = isset($record['first_name']) ? $transliterate->translitToCyr($record['first_name']) : '';
                $row['lastName'] = $record['last_name'] ?? '';
                $row['lastNameCyr'] = isset($record['last_name']) ? $transliterate->translitToCyr($record['last_name']) : '';
                $row['bdate'] = $record['bdate'] ?? '';
                $row['phone'] = $record['mobile_phone'] ?? '';
                $row['universityName'] = $record['university_name'] ?? '';
                $row['faculty_name'] = $record['faculty_name'] ?? '';
                $lists[] = $row;
            }
        }

        $fp = fopen('php://temp', 'w');
        foreach ($lists as $row) {
            fputcsv($fp, $row);
        }

        rewind($fp);
        $response = new Response(stream_get_contents($fp));
        fclose($fp);

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="testing.csv"');

        return $response;
    }
    /**
     * @Route("/vk/users/list", name="app_vk_users")
     */
    public function list(EntityManagerInterface $em, Request $request): Response
    {
        $repository = $em->getRepository(VkUsers::class);
        $countInPage = $request->query->get('count_in_page');

        if ($countInPage < 1) {
            $countInPage = 50;
        }

        $currentPage = $request->query->get('page');

        if ($currentPage < 1) {
            $currentPage = 1;
        }

        $nearbyPagesLimit = 7;
        $criteria = $this->createFilterCriteria($request);
        $all = $repository->matching($criteria);
        $records_count = count($all);
        $nbPages = round($records_count / $countInPage);
        $criteria->setMaxResults($countInPage)->setFirstResult($countInPage * ($currentPage - 1));
        $articles = $repository->matching($criteria);

        if(count($articles) > 0) {
            $citiesRepository = $em->getRepository(Cities::class);
            foreach ($articles as $index=>$article) {
                $cityArr = $citiesRepository->findBy(array('cityId'=>$article->getCity()));
                $cityName = isset($cityArr[0]) ? $cityArr[0]->getTitle():'';
                $articles[$index]->setCityName($cityName . ' ' . $article->getCity());
            }
        }

        $params = $request->query->all();

        return $this->render('vk_users/list.html.twig', [
            'controller_name' => 'VkUsersController',
            'articles' => $articles,
            'countInPage' => $countInPage,
            'currentPage' => $currentPage,
            'nbPages' => $nbPages,
            'nearbyPagesLimit' => $nearbyPagesLimit,
            'params' => $params,
            'records_count' => $records_count

        ]);
    }

    /**
     * @Route("/vk/users/update-user-data", name="app_vk_user_update")
     */
    public function update(EntityManagerInterface $em, Request $request, VkUsersService $vkUsersService): Response
    {

        $id = $request->query->get('id');
        $userData = $vkUsersService->getUserData($id, $em);
        $userResponse = $vkUsersService->getApiUserById($userData['UserId']);
        $userDataFromResponse = $vkUsersService->extractUserDataFromResponse($userResponse);

        if (isset($userDataFromResponse['id'])) {
            $vkUsersService->writeUserData($userDataFromResponse);
        }
        return $this->redirectToRoute('app_vk_users_view', ['id'=>$id]);
    }
}
