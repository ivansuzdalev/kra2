<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * VkUsers
 *
 * @ORM\Table(name="vk_users", uniqueConstraints={@ORM\UniqueConstraint(name="user_id", columns={"user_id"})})
 * @ORM\Entity
 */
class VkUsers
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nickname", type="string", length=2048, nullable=true)
     */
    private $nickname;

    /**
     * @var string|null
     *
     * @ORM\Column(name="maiden_name", type="string", length=2048, nullable=true)
     */
    private $maidenName;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="bdate", type="date", nullable=true)
     */
    private $bdate;

    /**
     * @var int|null
     *
     * @ORM\Column(name="city", type="integer", nullable=true)
     */
    private $city;

    /**
     * @var string|null
     *
     */
    private $cityName;


    /**
     * @var int|null
     *
     * @ORM\Column(name="country", type="integer", nullable=true)
     */
    private $country;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo_max_orig", type="string", length=2048, nullable=true)
     */
    private $photoMaxOrig;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="has_photo", type="boolean", nullable=true)
     */
    private $hasPhoto = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="has_mobile", type="boolean", nullable=true)
     */
    private $hasMobile = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="mobile_phone", type="string", length=255, nullable=true)
     */
    private $mobilePhone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="home_phone", type="string", length=255, nullable=true)
     */
    private $homePhone;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="last_seen", type="datetime", nullable=true)
     */
    private $lastSeen;

    /**
     * @var string|null
     *
     * @ORM\Column(name="screen_name", type="string", length=2048, nullable=true)
     */
    private $screenName;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="online", type="boolean", nullable=true)
     */
    private $online = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="skype", type="string", length=255, nullable=true)
     */
    private $skype;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="military", type="boolean", nullable=true)
     */
    private $military = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="twitter", type="string", length=255, nullable=true)
     */
    private $twitter;

    /**
     * @var array
     *
     * @ORM\Column(name="record_data", type="json", nullable=false)
     */
    private $recordData;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="datetime", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $datetime = 'CURRENT_TIMESTAMP';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getMaidenName(): ?string
    {
        return $this->maidenName;
    }

    public function setMaidenName(?string $maidenName): self
    {
        $this->maidenName = $maidenName;

        return $this;
    }

    public function getBdate(): ?\DateTimeInterface
    {
        return $this->bdate;
    }

    public function setBdate(?\DateTimeInterface $bdate): self
    {
        $this->bdate = $bdate;

        return $this;
    }

    public function getCity(): ?int
    {
        return $this->city;
    }

    public function getCityName(): ?string
    {
        return $this->cityName;
    }

    public function setCityName(?string $cityName): self
    {
        $this->cityName = $cityName;

        return $this;
    }

    public function setCity(?int $cityName): self
    {
        $this->cityName = $cityName;

        return $this;
    }

    public function getCountry(): ?int
    {
        return $this->country;
    }

    public function setCountry(?int $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getPhotoMaxOrig(): ?string
    {
        return $this->photoMaxOrig;
    }

    public function setPhotoMaxOrig(?string $photoMaxOrig): self
    {
        $this->photoMaxOrig = $photoMaxOrig;

        return $this;
    }

    public function isHasPhoto(): ?bool
    {
        return $this->hasPhoto;
    }

    public function setHasPhoto(?bool $hasPhoto): self
    {
        $this->hasPhoto = $hasPhoto;

        return $this;
    }

    public function isHasMobile(): ?bool
    {
        return $this->hasMobile;
    }

    public function setHasMobile(?bool $hasMobile): self
    {
        $this->hasMobile = $hasMobile;

        return $this;
    }

    public function getMobilePhone(): ?string
    {
        return $this->mobilePhone;
    }

    public function setMobilePhone(?string $mobilePhone): self
    {
        $this->mobilePhone = $mobilePhone;

        return $this;
    }

    public function getHomePhone(): ?string
    {
        return $this->homePhone;
    }

    public function setHomePhone(?string $homePhone): self
    {
        $this->homePhone = $homePhone;

        return $this;
    }

    public function getLastSeen(): ?\DateTimeInterface
    {
        return $this->lastSeen;
    }

    public function setLastSeen(?\DateTimeInterface $lastSeen): self
    {
        $this->lastSeen = $lastSeen;

        return $this;
    }

    public function getScreenName(): ?string
    {
        return $this->screenName;
    }

    public function setScreenName(?string $screenName): self
    {
        $this->screenName = $screenName;

        return $this;
    }

    public function isOnline(): ?bool
    {
        return $this->online;
    }

    public function setOnline(?bool $online): self
    {
        $this->online = $online;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getSkype(): ?string
    {
        return $this->skype;
    }

    public function setSkype(?string $skype): self
    {
        $this->skype = $skype;

        return $this;
    }

    public function isMilitary(): ?bool
    {
        return $this->military;
    }

    public function setMilitary(?bool $military): self
    {
        $this->military = $military;

        return $this;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(?string $twitter): self
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getRecordData(): array
    {
        return $this->recordData;
    }

    public function setRecordData(array $recordData): self
    {
        $this->recordData = $recordData;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(?\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }


}
