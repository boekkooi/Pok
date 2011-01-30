<?php
namespace Pok\Package\Metadata;

class Metadata {
    protected $files;
    protected $dependencies;

    public function __construct($identifier, $version) {
        $this->files = new Extension\File\Collection();
        $this->dependencies = new Extension\Dependency\Collection();
    }

    public function getFiles() {
        return $this->files;
    }

    public function getDependencies() {
        return $this->dependencies;
    }
}