<?php

namespace App\Service;

use App\Entity\Cities;
use App\Entity\VkUsers;
use App\Entity\VkTokens;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
  use PhpParser\Node\Scalar\String_;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class VkUsersService
{
    private HttpClientInterface $client;
    private ManagerRegistry $doctrine;
    private string $apiVersion = '5.131';

    public function __construct(HttpClientInterface $client, ManagerRegistry $doctrine)
    {
        $this->client = $client;
        $this->doctrine = $doctrine;
    }

    public function writeUserData($userData): void
    {
        $entityManager = $this->doctrine->getManager();
        $existVkUsers = $entityManager->getRepository(VkUsers::class)->findBy(['userId' => $userData['id']]);
        if (!$existVkUsers) {
            $modelVkUsers = new VkUsers();
        } else {
            $modelVkUsers = $existVkUsers[0];
        }

        $modelVkUsers->setUserId($userData['id'] ?? '');
        $modelVkUsers->setNickname($userData['nickname'] ?? '');
        $modelVkUsers->setMaidenName($userData['maiden_name'] ?? '');

        $dtObj = null;
        if (isset($userData['bdate']) && DateTime::createFromFormat('d.m.Y', $userData['bdate'])) {
            try {
                $dtObj = new DateTime($userData['bdate']);
            } catch (Exception $e) {
                var_dump($e);
            }
        }

        $modelVkUsers->setBdate($dtObj);

        if (isset($userData['city'])) {
            $city = $userData['city']['id'];
        } else {
            $city = 0;
        }
        $modelVkUsers->setCity($city);


        if (isset($userData['country'])) {
            $country = $userData['country']['id'];
        } else {
            $country = 0;
        }
        $modelVkUsers->setCountry($country);
        $modelVkUsers->setPhotoMaxOrig($userData['photo_max_orig'] ?? '');

        if ($userData['has_photo'] == 1) {
            $has_photo = True;
        } else {
            $has_photo = False;
        }
        $modelVkUsers->setHasPhoto($has_photo);


        if ($userData['has_mobile'] == 1) {
            $has_mobile = True;
        } else {
            $has_mobile = False;
        }
        $modelVkUsers->setHasMobile($has_mobile);
        $modelVkUsers->setMobilePhone($userData['mobile_phone'] ?? '');
        $modelVkUsers->setHomePhone($userData['home_phone'] ?? '');

        $last_seen = null;
        if (isset($userData['last_seen']['time'])) {
            $date = new DateTime();
            $date->setTimestamp($userData['last_seen']['time']);
            $last_seen = $date;
        }
        $modelVkUsers->setLastSeen($last_seen);

        $modelVkUsers->setScreenName($userData['screen_name'] ?? '');

        if ($userData['online'] == 1) {
            $online = True;
        } else {
            $online = False;
        }
        $modelVkUsers->setOnline($online);
        $modelVkUsers->setFirstName($userData['first_name'] ?? '');
        $modelVkUsers->setLastName($userData['last_name'] ?? '');
        $modelVkUsers->setSkype($userData['skype'] ?? '');


        if (isset($userData['military'])) {
            $military = True;
        } else {
            $military = False;
        }
        $modelVkUsers->setMilitary($military);
        $modelVkUsers->setTwitter($userData['twitter'] ?? '');
        $modelVkUsers->setRecordData($userData);
        $modelVkUsers->setDatetime(new DateTime('now'));

        if (!$existVkUsers) {
            $this->getPhoto($modelVkUsers->getPhotoMaxOrig(), $modelVkUsers->getUserId() . '.jpg');
        }

        $entityManager->persist($modelVkUsers);
        $entityManager->flush();
    }

    public function writeUsersData($parsedData): int
    {
        $recordsCounter = 0;
        foreach ($parsedData as $container) {
            if (isset($container['response']) && isset($container['response']['items']) && count($container['response']) > 0) {
                foreach ($container['response']['items'] as $userData) {
                    $this->writeUserData($userData);
                    $recordsCounter++;

                }
            }
        }
        return $recordsCounter;
    }

    public function getPhoto($url, $filename): void
    {
        $content = file_get_contents($url);
        //Store in the filesystem.
        $file = realpath(dirname(__FILE__) . '/../../public/photos/') . '/' . $filename;
        var_dump($file);
        if (!is_file($file)) {
            $fp = fopen(($file), "w");
            fwrite($fp, $content);
            fclose($fp);
        }
    }

    public function getFields(): string
    {
        return 'about,activities,bdate,blacklisted,blacklisted_by_mebooks,can_post,can_see_all_posts,can_see_audio,can_send_friend_request,can_write_private_message,career,city,common_count,connections,contacts,country,crop_photo,domain,education,exports,followers_count,friend_status,games,has_mobile,has_photo,home_town,interests,is_favorite,is_friend,is_hidden_from_feed,last_seen,lists,maiden_name,military,movies,music,nickname,occupation,online,personal,photo_100,photo_200,photo_200_orig,photo_400_orig,photo_50,photo_id,photo_max,photo_max_orig,quotes,relation,relatives,schools,screen_name,sex,site,status,timezone,tv,universities,verified,wall_comments.';

    }

    public function getApiUsers(): array
    {
        $returnArr = array();
        for ($ageStep = 0; $ageStep < 200; $ageStep++) {
            $data = array(
                'online' => 1,
                'has_photo' => 0,
                //'city' => '1938562',
                'country' => 13,
                'age_from' => $ageStep,
                'age_to' => (1 + $ageStep),
                'count' => 1000,
                'fields' => $this->getFields(),
                'access_token' => $this->getToken(),
                'v' => '5.131'
            );
            $response = null;

            try {
                $response = $this->client->request(
                    'POST',
                    'https://api.vk.com/method/users.search',
                    ['body' => $data]
                );
            } catch (TransportExceptionInterface $e) {
                var_dump($e);
            }

            $data = $this->getDataFromResponse($response);
            if (!empty($data)) {
                $returnArr[] = $data;
            }

            usleep(500 * 1000);
        }
        return $returnArr;
    }

    public function getApiUserById($userId): array
    {
        $returnArr = array();
        $data = array(
            'user_ids' => $userId,
            'fields' => $this->getFields(),
            'access_token' => $this->getToken(),
            'v' => '5.131'
        );
        $response = null;

        try {
            $response = $this->client->request(
                'POST',
                'https://api.vk.com/method/users.get',
                ['body' => $data]
            );
        } catch (TransportExceptionInterface $e) {
            var_dump($e);
        }

        $data = $this->getDataFromResponse($response);
        if (!empty($data)) {
            $returnArr[] = $data;
        }

        return $returnArr;
    }

    public function getToken(): string
    {
        $entityManager = $this->doctrine->getManager();
        $tokens = $entityManager->getRepository(VkTokens::class)->findBy(array(), array('id' => 'DESC'), 1, 0);
        $tokens = [];
        $token = 'vk1.a.lbi6hJODCN_3Ph3f2dyflxPyJTGfTnSEBcPiI2ol4C8D5LtFBYw_YNxb67IyKxGDR7gfsktFQbQWp32BIwBpVjhsnAJ6PYdrRG4Ha5T8Fy2-GroB7VtjE1eXgcTzRwqLEnyxuajF4kU99tOT9i1rphP4l0Xfvi0wG4SSt9MPs-IiT5CGjPeeynGUv1YAtVZo';
        if (count($tokens) > 0) {
            $token = $tokens[0]->getToken();
        }
        return $token;
    }

    private function getDataFromResponse($response): array
    {
        $statusCode = 0;
        try {
            $statusCode = $response->getStatusCode();
        } catch (TransportExceptionInterface $e) {
            var_dump($e);
        }
        $parsedData = array();
        try {
            $parsedData = $response->toArray();
        } catch (ClientExceptionInterface|TransportExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            var_dump($e);
        }
        if ($statusCode == 200 && count($parsedData) > 0) {
            return $parsedData;
        }
        return [];
    }

    public function getUserDataQuery(): string
    {
        return 'vk_users.userId as UserId, vk_users.firstName as FirstName, vk_users.lastName, city.title as cityName, vk_users.nickname, vk_users.maidenName, vk_users.city, vk_users.country, vk_users.mobilePhone, vk_users.lastSeen, vk_users.screenName, vk_users.online, vk_users.skype, vk_users.military, vk_users.recordData';
    }

    public function getUserData($id, $em): array
    {
        $userDataArr = $em
            ->createQueryBuilder()
            ->select($this->getUserDataQuery())
            ->from(VkUsers::class, 'vk_users')
            ->leftJoin(Cities::class, 'city', 'with', 'vk_users.city = city.cityId')
            ->where('vk_users.id = '.$id)
            ->orderBy('vk_users.id', 'ASC')
            ->getQuery()
            ->getResult();
        $userData = array();
        if (count($userDataArr) == 1) {
            $userData = $userDataArr[0];
        }

        return $userData;
    }

    public function extractUserDataFromResponse(array $userResponse): array
    {
        if (
            isset($userResponse[0])
            && isset($userResponse[0]['response'])
            && isset($userResponse[0]['response'][0])
        ) {
            return $userResponse[0]['response'][0];
        }

        return array();
    }
}
