<?php namespace pmill\Doctrine\Hydrator\Test;

use pmill\Doctrine\Hydrator\JsonApiHydrator;
use pmill\Doctrine\Hydrator\Test\Fixture\Permission;
use pmill\Doctrine\Hydrator\Test\Fixture\User;

class JsonApiHydratorTest extends TestCase
{
    /**
     * @var JsonApiHydrator
     */
    protected $hydrator;

    public function setUp()
    {
        $this->setupDoctrine();
        $this->hydrator = new JsonApiHydrator($this->entityManager);
    }

    public function testHydrateProperties()
    {
        $jsonapiData = [
            'attributes' => [
                'id'=>1,
                'name'=>'Fred Jones',
                'email'=>'fred@example.org',
            ]
        ];

        $user = $this->hydrator->hydrate(User::class, $jsonapiData);
        $this->assertNull($user->getId());
        $this->assertEquals($jsonapiData['attributes']['name'], $user->getName());
        $this->assertEquals($jsonapiData['attributes']['email'], $user->getEmail());
    }

    public function testHydrateOneToManyObjects()
    {
        $data = [
            'attributes' => [
                'name' => 'George',
            ],
            'relationships' => [
                'permissions' => [
                    'data' => [
                        ['name' => 'New Permission 1'],
                        ['name' => 'New Permission 2'],
                    ],
                ],
            ]
        ];

        $user = new Fixture\User;
        /** @var Fixture\User $user */
        $user = $this->hydrator->hydrate($user, $data);

        $this->assertEquals($data['attributes']['name'], $user->getName());

        $permissions = $user->getPermissions();
        // var_export($permissions);
        foreach ($permissions as $permission) {
            $this->assertInstanceOf(Permission::class, $permission);
        }

        $this->assertEquals($data['relationships']['permissions']['data'][0]['name'], $permissions[0]->getName());
        $this->assertEquals($data['relationships']['permissions']['data'][1]['name'], $permissions[1]->getName());
    }

    public function testHydrateAll()
    {
        $data = [
            'attributes' => [
                'id' => 1,
                'name' => 'Fred Jones',
                'email' => 'fred@example.org',
            ],
            'relationships' => [
                'company' => [
                    'data' => [
                        'id' => 1, 'type' => 'company'
                    ],
                ],
                'permissions' => [
                    'data' => [
                        ['id' => 1, 'type' => 'permission'],
                        ['id' => 2, 'type' => 'permission'],
                        ['id' => 3, 'type' => 'permission'],
                        ['id' => 4, 'type' => 'permission'],
                        ['id' => 5, 'type' => 'permission'],
                    ]
                ],
            ]
        ];

        /** @var Fixture\User $user */
        $user = new User;
        $user = $this->hydrator->hydrate($user, $data);

        $this->assertNull($user->getId());
        $this->assertEquals($data['attributes']['name'], $user->getName());
        $this->assertEquals($data['attributes']['email'], $user->getEmail());

        $this->assertEquals(1, $user->getCompany()->getId());

        $permissions = $user->getPermissions();
        $this->assertEquals(1, $permissions[0]->getId());
        $this->assertEquals(2, $permissions[1]->getId());
        $this->assertEquals(3, $permissions[2]->getId());
        $this->assertEquals(4, $permissions[3]->getId());
        $this->assertEquals(5, $permissions[4]->getId());
    }

    public function testNotFoundRelationship()
    {
        $this->setExpectedException(
            \Exception::class,
            'Relation `test` association not found'
        );

        $user = new Fixture\User;
        $this->hydrator->hydrate($user, [
            'relationships' => [
                'test' => [
                    'data' => ['id' => 1, 'type' => 'company']
                ]
            ]
        ]);
    }
}
