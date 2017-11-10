<?php
namespace pmill\Doctrine\Hydrator;

/**
 * Json API Request Doctrine Hydrator
 * @link http://jsonapi.org/format/#document-resource-objects
 */
class JsonApiHydrator extends ArrayHydrator
{
    /**
     * @param $entity
     * @param $data
     *
     * @return object
     */
    protected function hydrateProperties($entity, $data)
    {
        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $entity = parent::hydrateProperties($entity, $data['attributes']);
        }

        return $entity;
    }

    /**
     * Map JSON API resource relations to doctrine entity.
     *
     * @param object $entity
     * @param array  $data
     *
     * @return mixed
     * @throws \Exception
     */
    protected function hydrateAssociations($entity, $data)
    {
        if (isset($data['relationships']) && is_array($data['relationships'])) {
            $metadata = $this->entityManager->getClassMetadata(get_class($entity));

            foreach ($data['relationships'] as $name => $data) {
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
                                function ($relation) {
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
        }

        return $entity;
    }
}
