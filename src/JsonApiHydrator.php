<?php
namespace pmill\Doctrine\Hydrator;

/**
 * Json API Request Doctrine Hydrator
 */
class JsonApiHydrator extends ArrayHydrator
{
    /**
     * @link http://jsonapi.org/format/#document-resource-objects
     * @param object|string $entity     Doctrine entity or class name
     * @param array         $data       Data to hydrate
     *
     * @return mixed|object
     */
    public function hydrate($entity, array $data)
    {
        if (is_string($entity) && class_exists($entity)) {
            $entity = new $entity;
        } elseif (!is_object($entity)) {
            throw new \InvalidArgumentException(
                'Entity passed to JsonApiHydrator::hydrate() must be a class name or entity object'
            );
        }

        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $entity = $this->hydrateProperties($entity, $data['attributes']);
        }

        if (isset($data['relationships']) && is_array($data['relationships'])) {
            $entity = $this->hydrateRelationships($entity, $data['relationships']);
        }

        return $entity;
    }

    /**
     * Map JSON API resource relations to doctrine entity.
     *
     * @param       $entity
     * @param array $relationships
     *
     * @return array
     * @throws \Exception
     */
    protected function hydrateRelationships($entity, array $relationships)
    {
        $metadata = $this->entityManager->getClassMetadata(get_class($entity));
        foreach ($relationships as $name => $data) {
            if (!isset($metadata->associationMappings[$name])) {
                throw new \Exception(sprintf('Relation `%s` association not found', $name));
            }

            if (is_array($data['data'])) {
                if (isset($data['data']['id']) && isset($data['data']['type'])) {
                    $this->hydrateToOneAssociation($entity, $name,
                        $metadata->associationMappings[$name],
                        $data['data']['id']
                    );
                } else {
                    $this->hydrateToManyAssociation($entity, $name,
                        $metadata->associationMappings[$name],
                        array_map(
                            function($relation) {
                                if (isset($relation['id']) && isset($relation['type'])) {
                                    return $relation['id'];
                                }

                                return ['attributes' => $relation];
                            },
                            $data['data']
                        )
                    );
                }
            }
        }

        return $entity;
    }
}
