<?php
namespace pmill\Doctrine\Hydrator\Test;

use Mockery as m;
use pmill\Doctrine\Hydrator\ArrayHydrator;

class RouteTest extends TestCase
{
    /**
     * @var ArrayHydrator
     */
    protected $hydrator;

    public function setUp()
    {
        $this->setupDoctrine();
        $this->hydrator = new ArrayHydrator($this->entityManager);
    }

    public function testHydrate()
    {
        $user = new \pmill\Doctrine\Hydrator\Test\Fixture\User;
        $this->hydrator->hydrate($user, []);
        $this->assertTrue(true);
    }
}
