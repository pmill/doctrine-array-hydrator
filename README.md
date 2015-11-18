Doctrine Array to Entity Hydrator (WIP)
================

Introduction
------------

[![Build Status](https://secure.travis-ci.org/pmill/doctrine-rest.svg?branch=master)](http://travis-ci.org/pmill/doctrine-rest) [![Code Climate](https://codeclimate.com/github/pmill/doctrine-rest/badges/gpa.svg)](https://codeclimate.com/github/pmill/doctrine-rest) [![Test Coverage](https://codeclimate.com/github/pmill/doctrine-rest/badges/coverage.svg)](https://codeclimate.com/github/pmill/doctrine-rest/coverage) [![Test Coverage](https://scrutinizer-ci.com/g/pmill/doctrine-rest/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/pmill/doctrine-rest/)


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

    $hydrator = new \pmill\Doctrine\Hydrator\Array($entityManager);
    $entity = $hydrator->hydrate($entityClassOrObject, $data);

Copyright
---------

Doctrine Array to Entity  sHydrator
Copyright (c) 2015 pmill (dev.pmill@gmail.com) 
All rights reserved.