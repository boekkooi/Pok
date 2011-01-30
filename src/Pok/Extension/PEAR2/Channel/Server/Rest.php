<?php
namespace Pok\Extension\PEAR2\Channel\Server;

class Rest {
    protected $url;
    protected $type;

    public function __construct($url, $version = '1.0') {
        $this->setUrl($url);
        $this->setVersion($version);
    }

    public function getUrl() {
        return $this->url;
    }

    public function getVersion() {
        return $this->version;
    }

    protected function setUrl($url) {
        // TODO use parse_url
        $this->url = $url;
    }

    protected function setVersion($version) {
        $matches = array();
        if (!preg_match('/^(REST)?([0-9]+\.[0-9]+)$/', $version, $matches)) {
            throw new \InvalidArgumentException('Invalid version format given (`%s`)');
        }
        $this->version = $matches[2];
    }
}
