<?php

namespace pmill\Doctrine\Hydrator\Test;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Mockery as m;
use pmill\Doctrine\Hydrator\ArrayHydrator;
use pmill\Doctrine\Hydrator\Test\Fixture\Company;
use pmill\Doctrine\Hydrator\Test\Fixture\Permission;

class ArrayHydratorTest extends TestCase
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

        $user = new Fixture\User;
        $user = $this->hydrator->hydrate($user, $data);

        $this->assertNull($user->getId());
        $this->assertEquals($data['name'], $user->getName());
        $this->assertEquals($data['email'], $user->getEmail());
    }

    /**
     * Tests the hydration of a table where database column names and entity field names differ
     *
     * @throws \Exception
     */
    public function testHydratePropertiesByColumn()
    {
        $data = [
            'address_id'=>103,
            'street_address'=>'Super Street 12',
            'town_or_similar'=>'Farmville at the Sea',
            'country'=>'Republic',
        ];

        $this->hydrator->setHydrateBy(ArrayHydrator::HYDRATE_BY_COLUMN);
        $address = new Fixture\Address;
        $address = $this->hydrator->hydrate($address, $data);

        $this->assertNull($address->getId());
        $this->assertEquals($data['street_address'], $address->getStreetAddress());
        $this->assertEquals($data['town_or_similar'], $address->getCity());
        $this->assertEquals($data['country'], $address->getCountry());
    }

    /**
     * Tests the hydration of a table where database column names and entity field names differ and we also want to
     * hydrate the primary key
     *
     * @throws \Exception
     */
    public function testHydratePropertiesByColumnWithId()
    {
        $data = [
            'address_id'=>103,
            'street_address'=>'Super Street 12',
            'town_or_similar'=>'Farmville at the Sea',
            'country'=>'Republic',
        ];

        $this->hydrator->setHydrateId(true);
        $this->hydrator->setHydrateBy(ArrayHydrator::HYDRATE_BY_COLUMN);
        $address = new Fixture\Address;
        $address = $this->hydrator->hydrate($address, $data);

        $this->assertEquals($data['address_id'], $address->getId());
        $this->assertEquals($data['street_address'], $address->getStreetAddress());
        $this->assertEquals($data['town_or_similar'], $address->getCity());
        $this->assertEquals($data['country'], $address->getCountry());
    }

    public function testHydrateManyToOneAssociation()
    {
        $data = [
            'company'=>1,
            'address'=>103,
        ];

        $user = new Fixture\User;
        /** @var Fixture\User $user */
        $user = $this->hydrator->hydrate($user, $data);

        $this->assertEquals(1, $user->getCompany()->getId());
        $this->assertEquals(103, $user->getAddress()->getId());
    }

    public function testHydrateManyToOneAssociationByColumn()
    {
        $data = [
            'company_id'=>1,
            'foreign_address_id'=>103,
        ];

        $this->hydrator->setHydrateBy(ArrayHydrator::HYDRATE_BY_COLUMN);
        $user = new Fixture\User;
        /** @var Fixture\User $user */
        $user = $this->hydrator->hydrate($user, $data);

        $this->assertEquals(1, $user->getCompany()->getId());
        $this->assertEquals(103, $user->getAddress()->getId());
    }

    public function testHydrateOneToManyAssociations()
    {
        $data = [
            'permissions'=>[1,2,3,4,5],
        ];

        $user = new Fixture\User;
        /** @var Fixture\User $user */
        $user = $this->hydrator->hydrate($user, $data);

        $permissions = $user->getPermissions();
        $this->assertEquals(1, $permissions[0]->getId());
        $this->assertEquals(2, $permissions[1]->getId());
        $this->assertEquals(3, $permissions[2]->getId());
        $this->assertEquals(4, $permissions[3]->getId());
        $this->assertEquals(5, $permissions[4]->getId());
    }

    public function testHydrateOneToManyObjects()
    {
        $data = [
            'name' => 'George',
            'permissions' => [
                ['name' => 'New Permission 1'],
                ['name' => 'New Permission 2'],
            ],
        ];

        $user = new Fixture\User;
        /** @var Fixture\User $user */
        $user = $this->hydrator->hydrate($user, $data);

        $this->assertEquals($data['name'], $user->getName());

        $permissions = $user->getPermissions();
        foreach ($permissions as $permission) {
            $this->assertInstanceOf(Permission::class, $permission);
        }

        $this->assertEquals($data['permissions'][0]['name'], $permissions[0]->getName());
        $this->assertEquals($data['permissions'][1]['name'], $permissions[1]->getName());
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

        $user = new Fixture\User;
        /** @var Fixture\User $user */
        $user = $this->hydrator->hydrate($user, $data);

        $this->assertNull($user->getId());
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

    public function testHydrateNullValues()
    {
        $data = [
            'name'  => null,
            'email' => null,
        ];
        
        $user = new Fixture\User;
        /** @var Fixture\User $user */
        $user = $this->hydrator->hydrate($user, $data);
        $user->setName('name');
        $user->setEmail('email');

        $this->assertEquals('name', $user->getName());
        $this->assertEquals('email', $user->getEmail());
        $user = $this->hydrator->hydrate($user, $data);
        $this->assertNull($user->getName());
        $this->assertNull($user->getEmail());
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

    public function testFetchAssociationEntity()
    {
        /** @var Fixture\User $user */
        $user = new Fixture\User;
        $company = new Company();
        $company->setId(1);
        $company->setName('testing');

        /** @var EntityManager $entityManagerMock */
        $entityManagerMock = m::mock($this->entityManager)
            ->shouldReceive('find')->with(Company::class, 1)
            ->andReturn($company)
            ->getMock();

        $this->hydrator = new ArrayHydrator($entityManagerMock);
        $this->hydrator->setHydrateAssociationReferences(false);
        $user = $this->hydrator->hydrate($user, ['company' => $company->getId()]);

        $this->assertEquals($company->getId(), $user->getCompany()->getId());
        $this->assertEquals($company->getName(), $user->getCompany()->getName());
    }

    public function testConvertType()
    {
        $data = [
            'id'        => '1',
            'duration'  => '50',
            'startTime' => '2017-10-23 17:57:00',
            'status'    => 'true',
        ];

        $call = new Fixture\Call();
        $call = $this->hydrator->hydrate($call, $data);

        $this->assertInternalType('integer', $call->getDuration());
        $this->assertInstanceOf(\DateTime::class, $call->getStartTime());
        $this->assertInternalType('bool', $call->isStatus());
    }

    public function testOneToManySavePreviousPropertyValue()
    {
        $user = new Fixture\User();

        $this->assertInstanceOf(ArrayCollection::class, $user->getTags());

        $user = $this->hydrator->hydrate($user, ['tags' => [
            ['name' => 'foo'],
            ['name' => 'bar'],
        ]]);

        $this->assertInstanceOf(ArrayCollection::class, $user->getTags());

        $this->assertCount(2, $user->getTags());

        $this->assertSame('foo', $user->getTags()[0]->getName());

        $this->assertSame('bar', $user->getTags()[1]->getName());
    }
}
