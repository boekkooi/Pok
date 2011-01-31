<?php
namespace Pok\Application;

use Symfony\Component\HttpFoundation\UniversalClassLoader;

class Bootstrap {
    protected $di;

    public function __construct() {
        // Validate PHP version
        if (version_compare(phpversion(), '5.3.1', '<') && substr(phpversion(), 0, 5) != '5.3.1') {
            // this small hack is because of running RCs of 5.3.1
            throw new \RuntimeException("Pyrus requires PHP 5.3.1 or newer.");
        }

        // Check PHP extensions
        foreach (array('phar', 'spl', 'pcre', 'simplexml', 'libxml', 'xmlreader') as $ext) {
            if (!extension_loaded($ext)) {
                throw new \RuntimeException("The $ext extension is required.");
            }
        }
        if (version_compare(LIBXML_DOTTED_VERSION, '2.6.20', '<')) {
            throw new \RuntimeException("The libxml extension is must be version 2.6.20 or higher.");
        }
    }

    public function initialize() {
        $this->registerAutoloader();

        // Setup di
        $this->di = new DependencyInjection();

        // Register Channel loaders
        $this->di->get('ChannelLoader')
                ->register(new \Pok\Extension\PEAR2\Channel\ChannelLoader('pear2://'));

        /* Register Package loaders
        $di->get('PackageLoader')
                ->register('pear2://', new \Pok\Component\PEAR2\PackageLoader())
                ->register('github://', new \Pok\Component\Github\PackageLoader());*/

        // Add commands
        $console = $this->di->get('console');
        $console->add(new Console\Command\Package\ListCommand());
    }

    public function run() {
        $this->di->get('console')->run();
    }
    
    protected function registerAutoloader() {
        $srcDir = realpath(__DIR__ . '/../../').'/';
        
        require_once $srcDir."Symfony/Component/HttpFoundation/UniversalClassLoader.php";
        $loader = new UniversalClassLoader();
        $loader->registerNamespaces(array(
            'Symfony'   => $srcDir,
            'Pok'       => $srcDir,
        ));
        $loader->register();
    }
}
