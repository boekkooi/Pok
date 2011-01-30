<?php
namespace Pok\Channel;

abstract class Channel implements ChannelInterface {
    protected $name;
    protected $scheme;

    public function getName() {
        return $this->name;
    }

    /**
     * Returns the channel schema.
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    public function setScheme($scheme) {
        if (is_string($scheme) || strlen(trim($scheme)) === 0 || !is_string($scheme) || !preg_match('/^([a-z0-9_\-.]+):\/\/$/', $scheme)) {
            throw new \InvalidArgumentException('Scheme must be a string and may not be empty.');
        }

        $this->scheme = $scheme;

        return $this;
    }

    public function setName($name) {
        $this->name = $name;

        return $this;
    }
}
