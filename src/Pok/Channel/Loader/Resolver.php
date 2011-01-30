<?php
namespace Pok\Channel\Loader;

class Resolver implements ResolverInterface {
    protected $resolver = null;
    protected $loaders = array();

    public function load($resource) {
        // Resolve the loader
        $loader = $this->resolve($resource);
        if ($loader === false) {
            throw new \InvalidArgumentException('Unsupported resource.');
        }

        // Load the resource
        return $loader->load($resource);
    }

    public function supports($resource) {
        return $this->resolve($resource) !== false;
    }

    public function resolve($resource) {
        // Loop through all loaders until resource is supported.
        foreach ($this->loaders as $loader) {
            if ($loader->supports($resource)) {
                return $loader;
            }
        }

        return false;
    }

    public function register(LoaderInterface $loader) {
        $this->loaders[] = $loader;
    }
}
