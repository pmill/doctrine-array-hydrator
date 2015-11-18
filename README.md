Doctrine Array to Entity Hydrator
================

Introduction
------------

[![Build Status](https://secure.travis-ci.org/pmill/doctrine-array-hydrator.svg?branch=master)](http://travis-ci.org/pmill/doctrine-array-hydrator) [![Code Climate](https://codeclimate.com/github/pmill/doctrine-array-hydrator/badges/gpa.svg)](https://codeclimate.com/github/pmill/doctrine-array-hydrator) [![Test Coverage](https://codeclimate.com/github/pmill/doctrine-array-hydrator/badges/coverage.svg)](https://codeclimate.com/github/pmill/doctrine-array-hydrator/coverage) [![Test Coverage](https://scrutinizer-ci.com/g/pmill/doctrine-array-hydrator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/pmill/doctrine-array-hydrator/)


A hydrator for doctrine 2 that converts an array to the entity of your choice.


Example
-------

Given this doctrine entity:

    <?php
    namespace App\Entity;
    
    use Doctrine\ORM\Mapping as ORM;
    
    /**
     * @ORM\Entity
     * @ORM\Table(name="users")
     */
    class User
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
        protected $name;
    
        /**
         * @ORM\Column(type="string")
         * @var string
         */
        protected $email;
    
        /**
         * @ManyToOne(targetEntity="Company")
         * @var Company
         */
        protected $company;
        
        /**
         * @OneToMany(targetEntity="Permission", mappedBy="product")
         * @var Permission[]
         */
        protected $permissions;
        ...

We can populate this object with an array, for example:

    $data = [
        'name'=>'Fred Jones',
        'email'=>'fread@example.com',
        'company'=>2,
        'permissions'=>[1, 2, 3, 4];
    ];

    $hydrator = new \pmill\Doctrine\Hydrator\ArrayHydrator($entityManager);
    $entity = $hydrator->hydrate('App\Entity\User', $data);

Copyright
---------

Doctrine Array to Entity  sHydrator
Copyright (c) 2015 pmill (dev.pmill@gmail.com) 
All rights reserved.