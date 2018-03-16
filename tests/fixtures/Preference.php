<?php
namespace pmill\Doctrine\Hydrator\Test\Fixture;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="preferences")
 */
class Preference
{
    /**
     * @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="preference")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @var User
     */
    protected $user;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $wallpaper;

    /**
     * @ORM\Column(type="string", name="ring_tone")
     * @var string
     */
    protected $ringTone;

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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getWallpaper()
    {
        return $this->wallpaper;
    }

    /**
     * @param string $wallpaper
     */
    public function setWallpaper($wallpaper)
    {
        $this->wallpaper = $wallpaper;
    }

    /**
     * @return string
     */
    public function getRingTone()
    {
        return $this->ringTone;
    }

    /**
     * @param string $ringTone
     */
    public function setRingTone($ringTone)
    {
        $this->ringTone = $ringTone;
    }
}