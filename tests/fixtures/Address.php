<?php
namespace pmill\Doctrine\Hydrator\Test\Fixture;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="addresses")
 */
class Address
{
    /**
     * @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $street_address;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $city;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $country;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getStreetAddress()
    {
        return $this->street_address;
    }

    /**
     * @param string $street_address
     */
    public function setStreetAddress($street_address)
    {
        $this->street_address = $street_address;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }
}
