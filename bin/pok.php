<?php
require_once __DIR__ . '/../src/Pok/Application/Bootstrap.php';
$bootstrap = new \Pok\Application\Bootstrap();
$bootstrap->initialize();
$bootstrap->run();