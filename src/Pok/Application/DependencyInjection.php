<?php
namespace Pok\Application;

use Symfony\Component\DependencyInjection\Container;

class DependencyInjection extends Container {
    public function getConsoleService() {
        if (isset($this->services['console'])) {
            return $this->services['console'];
        }

        $loader = new \Pok\Application\Console\Application($this);

        return $this->services['console'] = $loader;
    }

    public function getChannelLoaderService() {
        if (isset($this->services['channelloader'])) {
            return $this->services['channelloader'];
        }

        $loader = new \Pok\Channel\Loader\Resolver();

        return $this->services['channelloader'] = $loader;
    }
}
