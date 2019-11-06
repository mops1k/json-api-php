<?php
declare(strict_types=1);

namespace JsonApi;

abstract class AbstractSerializer implements SerializerInterface
{
    /**
     * The type.
     */
    public const TYPE = null;

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException
     */
    public function getType($model): string
    {
        if (null === static::TYPE || empty(static::TYPE)) {
            throw new \LogicException('Type must not be empty.');
        }

        return (string) static::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getId($model): string
    {
        return (string) $model->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($model, array $fields = null): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks($model): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta($model): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException
     */
    public function getRelationship($model, $name): ?Relationship
    {
        $method = $this->getRelationshipMethodName($name);

        if (\method_exists($this, $method)) {
            $relationship = $this->$method($model);

            if (null !== $relationship && !($relationship instanceof Relationship)) {
                throw new \LogicException(
                    \sprintf(
                        'Relationship method must return null or an instance of %s',
                        Relationship::class
                    ));
            }

            return $relationship;
        }
    }

    /**
     * Get the serializer method name for the given relationship.
     *
     * snake_case and kebab-case are converted into camelCase.
     *
     * @param string $name
     *
     * @return string
     */
    private function getRelationshipMethodName($name): string
    {
        if (\strpos($name, '-')) {
            $name = \lcfirst(\implode('', \array_map('ucfirst', \explode('-', $name))));
        }

        if (\strpos($name, '_')) {
            $name = \lcfirst(\implode('', \array_map('ucfirst', \explode('_', $name))));
        }

        return $name;
    }
}
