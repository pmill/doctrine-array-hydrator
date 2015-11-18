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

    public function testHydrateProperties()
    {
        $data = [
            'id'=>1,
            'name'=>'Fred Jones',
            'email'=>'fred@example.org',
        ];

        $user = new \pmill\Doctrine\Hydrator\Test\Fixture\User;
        $user = $this->hydrator->hydrate($user, $data);

        $this->assertEquals($data['id'], $user->getId());
        $this->assertEquals($data['name'], $user->getName());
        $this->assertEquals($data['email'], $user->getEmail());
    }

    public function testHydrateManyToOneAssociation()
    {
        $data = [
            'company'=>1,
        ];

        $user = new \pmill\Doctrine\Hydrator\Test\Fixture\User;
        /** @var \pmill\Doctrine\Hydrator\Test\Fixture\User $user */
        $user = $this->hydrator->hydrate($user, $data);

        $this->assertEquals(1, $user->getCompany()->getId());
    }

    public function testHydrateOneToManyAssociations()
    {
        $data = [
            'permissions'=>[1,2,3,4,5],
        ];

        $user = new \pmill\Doctrine\Hydrator\Test\Fixture\User;
        /** @var \pmill\Doctrine\Hydrator\Test\Fixture\User $user */
        $user = $this->hydrator->hydrate($user, $data);

        $permissions = $user->getPermissions();
        $this->assertEquals(1, $permissions[0]->getId());
        $this->assertEquals(2, $permissions[1]->getId());
        $this->assertEquals(3, $permissions[2]->getId());
        $this->assertEquals(4, $permissions[3]->getId());
        $this->assertEquals(5, $permissions[4]->getId());
    }

    public function testHydrateAll()
    {
        $data = [
            'id'=>1,
            'name'=>'Fred Jones',
            'email'=>'fred@example.org',
            'company'=>1,
            'permissions'=>[1,2,3,4,5],
        ];

        $user = new \pmill\Doctrine\Hydrator\Test\Fixture\User;
        /** @var \pmill\Doctrine\Hydrator\Test\Fixture\User $user */
        $user = $this->hydrator->hydrate($user, $data);

        $this->assertEquals($data['id'], $user->getId());
        $this->assertEquals($data['name'], $user->getName());
        $this->assertEquals($data['email'], $user->getEmail());

        $this->assertEquals(1, $user->getCompany()->getId());

        $permissions = $user->getPermissions();
        $this->assertEquals(1, $permissions[0]->getId());
        $this->assertEquals(2, $permissions[1]->getId());
        $this->assertEquals(3, $permissions[2]->getId());
        $this->assertEquals(4, $permissions[3]->getId());
        $this->assertEquals(5, $permissions[4]->getId());
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidClass()
    {
        $this->hydrator->hydrate(1, []);
    }

    /**
     * @expectedException \Exception
     */
    public function testUnkownClass()
    {
        $this->hydrator->hydrate('An\Unknown\Class', []);
    }
}
