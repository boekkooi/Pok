<?php
namespace Pok\Channel;

class PackageReference {
    protected $channel;

    protected $name;

    protected $versions;

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getVersions()
    {
        return $this->versions;
    }

    public function setVersions(array $versions)
    {
        $this->versions = $versions;
    }

    public function getChannel()
    {
        return $this->channel;
    }

    public function setChannel($channel)
    {
        $this->channel = $channel;
    }
}
