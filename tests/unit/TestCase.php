<?php
namespace pmill\Doctrine\Hydrator\Test;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var AnnotationReader
     */
    protected $annotationReader;

    /**
     * @param $object $object
     * @param string $propertyName
     * @return mixed
     */
    protected function getProtectedValue($object, $propertyName)
    {
        $reflectionObject = new \ReflectionObject($object);
        $property = $reflectionObject->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }

    /**
     * @param $object
     * @param $propertyName
     * @param $value
     */
    protected function setProtectedValue(&$object, $propertyName, $value)
    {
        $reflectionObject = new \ReflectionObject($object);
        $property = $reflectionObject->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     */
    protected function setupDoctrine()
    {
        $databaseConfig = [
            'driver'=>'pdo_sqlite',
            'dbname'=>':memory:',
        ];
        $doctrineConfig = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(['tests/fixtures/'], false, null, new ArrayCache(), false);

        $this->entityManager = EntityManager::create($databaseConfig, $doctrineConfig);
        $this->annotationReader = $this->entityManager->getConfiguration()->getMetadataDriverImpl(); //newDefaultAnnotationDriver('tests/fixtures/', false)->getReader();
    }

    /**
     * @param EntityManager $entityManager
     * @return array
     */
    protected function getEntityClassNames(EntityManager $entityManager)
    {
        $classes = [];

        /** @var ClassMetadata[] $metas */
        $metas = $entityManager->getMetadataFactory()->getAllMetadata();
        foreach ($metas as $meta) {
            $classes[] = $meta->getName();
        }

        return $classes;
    }

}