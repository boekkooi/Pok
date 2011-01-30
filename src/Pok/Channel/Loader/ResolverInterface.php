<?php
namespace Pok\Channel\Loader;

interface ResolverInterface {
    /**
     * Returns a loader able to load the resource.
     *
     * @param mixed $resource A resource
     * @return LoaderInterface A LoaderInterface instance
     */
    function resolve($resource);
}
