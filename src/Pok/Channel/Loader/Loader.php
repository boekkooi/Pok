<?php
namespace Pok\Channel\Loader;

abstract class Loader implements LoaderInterface
{
    protected $schema;

    protected $resolver;

    public function __construct($schema) {
        $this->setSchema($schema);
    }

    public function setSchema($schema)
    {
        if (empty($schema) || !is_string($schema) || !preg_match('/^([a-z0-9_\-.]+):\/\/$/', $schema)) {
            throw new \InvalidArgumentException("Schema argument may no be empty and must end with `://`.");
        }

        $this->schema = $schema;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Gets the loader resolver.
     *
     * @return LoaderResolver A LoaderResolver instance
     */
    public function getResolver()
    {
        return $this->resolver;
    }

    /**
     * Sets the loader resolver.
     *
     * @param LoaderResolver $resolver A LoaderResolver instance
     */
    public function setResolver(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Returns true if this class supports the given resource.
     *
     * @param  mixed $resource A resource
     * @return Boolean true if this class supports the given resource, false otherwise
     */
    public function supports($resource)
    {
        // Check if this is a simple string lookup
        if (is_string($resource) && strpos($this->schema, $resource) === 0) {
            return true;
        }

        return false;
    }
}
