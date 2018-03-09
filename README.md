Doctrine Array to Entity Hydrator
================

Introduction
------------

[![Build Status](https://secure.travis-ci.org/pmill/doctrine-array-hydrator.svg?branch=master)](http://travis-ci.org/pmill/doctrine-array-hydrator) [![Code Climate](https://codeclimate.com/github/pmill/doctrine-array-hydrator/badges/gpa.svg)](https://codeclimate.com/github/pmill/doctrine-array-hydrator) [![Test Coverage](https://codeclimate.com/github/pmill/doctrine-array-hydrator/badges/coverage.svg)](https://codeclimate.com/github/pmill/doctrine-array-hydrator/coverage) [![Test Coverage](https://scrutinizer-ci.com/g/pmill/doctrine-array-hydrator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/pmill/doctrine-array-hydrator/)


A hydrator for doctrine 2 that converts an array to the entity of your choice.

Installing via Composer
-----------------------

The recommended way to install is through
[Composer](http://getcomposer.org).

    composer require pmill/doctrine-array-hydrator

Example
-------

Given this doctrine entity:

```PHP
<?php

namespace App\Entity;
    
use Doctrine\ORM\Mapping as ORM;
use pmill\Doctrine\Hydrator\Test\Fixture\Company;
use pmill\Doctrine\Hydrator\Test\Fixture\Permission;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User
{
   /**
    * @ORM\Id 
    * @ORM\Column(type="integer") 
    * @ORM\GeneratedValue
    * 
    * @var int
    */
    protected $id;
    
    /**
     * @ORM\Column(type="string")
     * 
     * @var string
     */
    protected $name;
    
    /**
     * @ORM\Column(type="string")
     * 
     * @var string
     */
    protected $email;
    
    /**
     * @ManyToOne(targetEntity="Company")
     * 
     * @var Company
     */
    protected $company;
        
    /**
     * @OneToMany(targetEntity="Permission", mappedBy="product")
     * 
     * @var Permission[]
     */
    protected $permissions;
}
```

We can populate this object with an array, for example:

```PHP
$data = [
    'name'        => 'Fred Jones',
    'email'       => 'fred@example.com',
    'company'     => 2,
    'permissions' => [1, 2, 3, 4]
];

$hydrator = new \pmill\Doctrine\Hydrator\ArrayHydrator($entityManager);
$entity   = $hydrator->hydrate('App\Entity\User', $data);
```

We can populate user with JSON API resource data
[Documentation](http://jsonapi.org/format/#document-resource-objects)
```PHP
$data = [
    'attributes'    => [
        'name'  => 'Fred Jones',
        'email' => 'fred@example.com',
    ],
    'relationships' => [
        'company'     => [
            'data' => ['id' => 1, 'type' => 'company'],
        ],
        'permissions' => [
            'data' => [
                ['id' => 1, 'type' => 'permission'],
                ['id' => 2, 'type' => 'permission'],
                ['id' => 3, 'type' => 'permission'],
                ['id' => 4, 'type' => 'permission'],
                ['name' => 'New permission']
            ]
        ]
    ]
];
    
$hydrator = new \pmill\Doctrine\Hydrator\JsonApiHydrator($entityManager);
$entity   = $hydrator->hydrate('App\Entity\User', $data);
```

Copyright
---------

Doctrine Array to Entity Hydrator
Copyright (c) 2015 pmill (dev.pmill@gmail.com) 
All rights reserved.
