<?php
namespace Pok\Channel\Loader;

interface LoaderInterface {
    /**
     * Loads a resource.
     *
     * A resource can be anything that can be converted to a
     * Channel instance.
     *
     * @param mixed $resource The resource
     */
    function load($resource);

    /**
     * Returns true if this class supports the given resource.
     *
     * @param  mixed $resource A resource
     * @return Boolean true if this class supports the given resource, false otherwise
     */
    function supports($resource);

    /**
     * Gets the loader resolver.
     *
     * @return LoaderResolver A LoaderResolver instance
     */
    function getResolver();

    /**
     * Sets the loader resolver.
     *
     * @param LoaderResolver $resolver A LoaderResolver instance
     */
    function setResolver(ResolverInterface $resolver);

    function getSchema();

    function setSchema($schema);
}
