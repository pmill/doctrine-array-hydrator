<?php
namespace pmill\Doctrine\Hydrator;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Exception;

class ArrayHydrator
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var bool
     */
    protected $hydrateAssociationReferences = true;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $entity
     * @param array $data
     * @return mixed|object
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

        $entity = $this->hydrateProperties($entity, $data);
        $entity = $this->hydrateAssociations($entity, $data);
        return $entity;
    }

    /**
     * @param boolean $hydrateAssociationReferences
     */
    public function setHydrateAssociationReferences($hydrateAssociationReferences)
    {
        $this->hydrateAssociationReferences = $hydrateAssociationReferences;
    }

    /**
     * @param $entity
     * @param $data
     * @return object
     */
    protected function hydrateProperties($entity, $data)
    {
        $reflectionObject = new \ReflectionObject($entity);

        $metaData = $this->entityManager->getClassMetadata(get_class($entity));
        
        $platform = $this->entityManager->getConnection()
                                        ->getDatabasePlatform();
        
        foreach ($metaData->columnNames as $propertyName) {
            if (isset($data[$propertyName]) && !in_array($propertyName, $metaData->identifier)) {
                $value = $data[$propertyName];
                
                if (array_key_exists('type', $metaData->fieldMappings[$propertyName])) {
                    $fieldType = $metaData->fieldMappings[$propertyName]['type'];
                    
                    $type = Type::getType($fieldType);
                    
                    $value = $type->convertToPHPValue($value, $platform);
                }

                $entity = $this->setProperty($entity, $propertyName, $value, $reflectionObject);
            }
        }

        return $entity;
    }

    /**
     * @param $entity
     * @param $data
     * @return mixed
     */
    protected function hydrateAssociations($entity, $data)
    {
        $metaData = $this->entityManager->getClassMetadata(get_class($entity));
        foreach ($metaData->associationMappings as $propertyName => $mapping) {
            if (isset($data[$propertyName])) {
                if (in_array($mapping['type'], [ClassMetadataInfo::ONE_TO_ONE, ClassMetadataInfo::MANY_TO_ONE])) {
                    $entity = $this->hydrateToOneAssociation($entity, $propertyName, $mapping, $data[$propertyName]);
                }

                if (in_array($mapping['type'], [ClassMetadataInfo::ONE_TO_MANY, ClassMetadataInfo::MANY_TO_MANY])) {
                    $entity = $this->hydrateToManyAssociation($entity, $propertyName, $mapping, $data[$propertyName]);
                }
            }
        }

        return $entity;
    }

    /**
     * @param $entity
     * @param $propertyName
     * @param $mapping
     * @param $value
     * @return mixed
     */
    protected function hydrateToOneAssociation($entity, $propertyName, $mapping, $value)
    {
        $reflectionObject = new \ReflectionObject($entity);

        $toOneAssociationObject = $this->fetchAssociationEntity($mapping['targetEntity'], $value);
        if (!is_null($toOneAssociationObject)) {
            $entity = $this->setProperty($entity, $propertyName, $toOneAssociationObject, $reflectionObject);
        }

        return $entity;
    }

    /**
     * @param $entity
     * @param $propertyName
     * @param $mapping
     * @param $value
     * @return mixed
     */
    protected function hydrateToManyAssociation($entity, $propertyName, $mapping, $value)
    {
        $reflectionObject = new \ReflectionObject($entity);
        $values = is_array($value) ? $value : [$value];

        $assocationObjects = [];
        foreach ($values as $value) {
            if (is_array($value)) {
                $assocationObjects[] = $this->hydrate($mapping['targetEntity'], $value);
            }
            elseif ($associationObject = $this->fetchAssociationEntity($mapping['targetEntity'], $value)) {
                $assocationObjects[] = $associationObject;
            }
        }

        $entity = $this->setProperty($entity, $propertyName, $assocationObjects, $reflectionObject);

        return $entity;
    }

    /**
     * @param $entity
     * @param $propertyName
     * @param $value
     * @param null $reflectionObject
     * @return mixed
     */
    protected function setProperty($entity, $propertyName, $value, $reflectionObject = null)
    {
        $reflectionObject = is_null($reflectionObject) ? new \ReflectionObject($entity) : $reflectionObject;
        $property = $reflectionObject->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($entity, $value);
        return $entity;
    }

    /**
     * @param $className
     * @param $id
     * @return bool|\Doctrine\Common\Proxy\Proxy|null|object
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    protected function fetchAssociationEntity($className, $id)
    {
        if ($this->hydrateAssociationReferences) {
            return $this->entityManager->getReference($className, $id);
        }

        return $this->entityManager->find($className, $id);
    }
}
