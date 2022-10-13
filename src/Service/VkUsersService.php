<?php

namespace App\Service;

use App\Entity\VkUsers;
use App\Entity\VkTokens;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
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

    public function __construct(HttpClientInterface $client, ManagerRegistry $doctrine)
    {
        $this->client = $client;
        $this->doctrine = $doctrine;
    }

    public function writeUsersData($parsedData): int
    {
        $entityManager = $this->doctrine->getManager();
        $recordsCounter = 0;
        foreach ($parsedData as $container) {
            if (isset($container['response']) && isset($container['response']['items']) && count($container['response']) > 0 ) {
                foreach ($container['response']['items'] as $parsed) {
                    
                    $existVkUsers = $entityManager->getRepository(VkUsers::class)->findBy(['userId' => $parsed['id']]);
                    if (!$existVkUsers) {
                        $modelVkUsers = new VkUsers();
                    } else {
                        $modelVkUsers = $existVkUsers[0];
                    }

                    $modelVkUsers->setUserId($parsed['id'] ?? '');
                    $modelVkUsers->setNickname($parsed['nickname'] ?? '');
                    $modelVkUsers->setMaidenName($parsed['maiden_name'] ?? '');

                    $dtObj = null;
                    if (isset($parsed['bdate']) && DateTime::createFromFormat('d.m.Y', $parsed['bdate'])) {
                        try {
                            $dtObj = new DateTime($parsed['bdate']);
                        } catch (Exception $e) {
                            var_dump($e);
                        }
                    }
                    $modelVkUsers->setBdate($dtObj);


                    if (isset($parsed['city'])) {
                        $city = $parsed['city']['id'];
                    } else {
                        $city = 0;
                    }
                    $modelVkUsers->setCity($city);


                    if (isset($parsed['country'])) {
                        $country = $parsed['country']['id'];
                    } else {
                        $country = 0;
                    }
                    $modelVkUsers->setCountry($country);
                    $modelVkUsers->setPhotoMaxOrig($parsed['photo_max_orig'] ?? '');

                    if ($parsed['has_photo']==1) {
                        $has_photo= True;
                    } else {
                        $has_photo= False;
                    }
                    $modelVkUsers->setHasPhoto($has_photo);


                    if ($parsed['has_mobile']==1) {
                        $has_mobile= True;
                    } else {
                        $has_mobile= False;
                    }
                    $modelVkUsers->setHasMobile($has_mobile);
                    $modelVkUsers->setMobilePhone($parsed['mobile_phone'] ?? '');
                    $modelVkUsers->setHomePhone($parsed['home_phone'] ?? '');

                    $last_seen = null;
                    if (isset($parsed['last_seen']['time'])) {
                        $date = new DateTime();
                        $date->setTimestamp($parsed['last_seen']['time']);
                        $last_seen = $date;
                    }
                    $modelVkUsers->setLastSeen($last_seen);

                    $modelVkUsers->setScreenName($parsed['screen_name'] ?? '');

                    if ($parsed['online']==1) {
                        $online = True;
                    } else {
                        $online = False;
                    }
                    $modelVkUsers->setOnline($online);
                    $modelVkUsers->setFirstName($parsed['first_name'] ?? '');
                    $modelVkUsers->setLastName($parsed['last_name'] ?? '');
                    $modelVkUsers->setSkype($parsed['skype'] ?? '');


                    if (isset($parsed['military'])) {
                        $military = True;
                    } else {
                        $military = False;
                    }
                    $modelVkUsers->setMilitary($military);
                    $modelVkUsers->setTwitter($parsed['twitter'] ?? '');
                    $modelVkUsers->setRecordData($parsed);
                    $modelVkUsers->setDatetime(new DateTime('now'));

                    if (!$existVkUsers) {
                        $this->getPhoto($modelVkUsers->getPhotoMaxOrig(), $modelVkUsers->getUserId().'.jpg');
                    }
                    
                    $entityManager->persist($modelVkUsers);
                    $entityManager->flush();
                    $recordsCounter++;
            
                }
            }
        }
        return $recordsCounter;
    }
    public function getPhoto($url, $filename): Void
    {
        $content = file_get_contents($url);
        //Store in the filesystem.
        $file = realpath(dirname(__FILE__) . '/../../public/photos/').'/'.$filename;
        var_dump($file);
        if(!is_file($file)){
            $fp = fopen(($file), "w");
            fwrite($fp, $content);
            fclose($fp);
        }
    }

    public function getApiUsers(): array
    {
        $entityManager = $this->doctrine->getManager();
        $tokens = $entityManager->getRepository(VkTokens::class)->findBy(array(),array('id'=>'DESC'),1,0);
        $token = 'vk1.a.uI6Y9yLfufmUyOXx65xY4OL3YmE-faJbkpx97FkSnEtvYHy1EkPTY5U5DIRhNUFP6CZ9O9b2lw-5GuPHjQKXESN2eiM0PX__875ni2_PXryPwLbdhhAZsV3_7GSU7TPgjqphNz__TPVICEqyM7YDWb8mLNpG6KDW9Nn2NsizNeGylaZqfEg1rqilh9v6p4N7';
        if(count($tokens) > 0) {
            $token = $tokens[0]->getToken();
        }
        $fields = 'about,activities,bdate,blacklisted,blacklisted_by_mebooks,can_post,can_see_all_posts,can_see_audio,can_send_friend_request,can_write_private_message,career,city,common_count,connections,contacts,country,crop_photo,domain,education,exports,followers_count,friend_status,games,has_mobile,has_photo,home_town,interests,is_favorite,is_friend,is_hidden_from_feed,last_seen,lists,maiden_name,military,movies,music,nickname,occupation,online,personal,photo_100,photo_200,photo_200_orig,photo_400_orig,photo_50,photo_id,photo_max,photo_max_orig,quotes,relation,relatives,schools,screen_name,sex,site,status,timezone,tv,universities,verified,wall_comments.';
        $returnArr = array();
        for($ageStep=0;$ageStep<200;$ageStep++) {
            $data = array(
                'online' => 1,
                'has_photo' => 0,
                //'city' => '1938562',
                'country' => 13,
                'age_from' => $ageStep,
                'age_to' => (1 + $ageStep),
                'count' => 1000,
                'fields' => $fields,
                'access_token' => $token,
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
                $returnArr[] = $parsedData;
            }
            usleep(500 * 1000);
        }
        return $returnArr;
    }
}
