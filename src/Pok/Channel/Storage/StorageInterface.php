<?php
namespace Pok\Channel\Storage;

use Pok\Channel\ChannelInterface;

interface StorageInterface {
    function load($uri);

    function loadAll();

    function save(ChannelInterface $channel);
}
