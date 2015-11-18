<?php
namespace pmill\Doctrine\Hydrator;

use Doctrine\ORM\EntityManager;
use Exception;

class ArrayHydrator
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string|object $entity
     * @param array $data
     * @throws Exception
     */
    public function hydrate($entity, array $data)
    {
        if (is_string($entity) && class_exists($entity)) {
            $entity = new $entity;
        }
        elseif (!is_object($entity)) {
            throw new Exception('Entity passed to ArrayHydrator::hydrate() must be a class name or entity object');
        }


        $metaData = $this->entityManager->getClassMetadata(get_class($entity));
        $associations = $metaData->associationMappings;

        //$metaData->columnNames
    }
}
