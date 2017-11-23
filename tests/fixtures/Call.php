<?php

namespace pmill\Doctrine\Hydrator\Test\Fixture;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="calls")
 */
class Call
{
    /**
     * @var integer
     *
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $startTime;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Call
     */
    public function setId(int $id): Call
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     *
     * @return Call
     */
    public function setDuration(int $duration): Call
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime(): \DateTime
    {
        return $this->startTime;
    }

    /**
     * @param \DateTime $startTime
     *
     * @return Call
     */
    public function setStartTime(\DateTime $startTime): Call
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * @return bool
     */
    public function isStatus(): bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     *
     * @return Call
     */
    public function setStatus(bool $status): Call
    {
        $this->status = $status;

        return $this;
    }
}