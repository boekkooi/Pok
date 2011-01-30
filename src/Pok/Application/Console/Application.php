<?php
namespace Pok\Application\Console;

use Symfony\Component\DependencyInjection\Container,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputInterface;

class Application extends \Symfony\Component\Console\Application {
    protected $container;

    public function __construct(Container $container) {
		parent::__construct(
			'Pok',
			 '@PACKAGE_VERSION@' == '@'.'PACKAGE_VERSION@' ? 'dev' : '@PACKAGE_VERSION@'
		);
        $this->setContainer($container);
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        if (null === $input) {
            $input = new Input\ArgvInput();
        }
        return parent::run($input, $output);
    }

	public function doRun(InputInterface $input, OutputInterface $output) {
        // Detect registry
        $registryPath = $this->getRegistryPath($input);
        if (realpath($registryPath . '/.pok') === false) {
            // Run init registry command
            $command = new Command\InitRegistryCommand();
            $command->setApplication($this);

            $this->runningCommand = $command;
            $statusCode = $command->run($input, $output);
            $this->runningCommand = null;

            if (is_numeric($statusCode) && $statusCode !== 0) {
                return $statusCode;
            }
        }

        // Set registry into DI
        $this->getContainer()->setParameter('pok.registryPath', $registryPath);
        $output->write("<info>Using registry at $registryPath</info>", true);

		return parent::doRun($input, $output);
	}

    /**
     * Gets the help message.
     *
     * @return string A help message.
     */
    public function getHelp()
    {
        $messages = array(
            $this->getLongVersion(),
            '',
            '<comment>Usage:</comment>',
            sprintf("  [/path/to/registry] [options] command [arguments]\n"),
            '<comment>Options:</comment>',
        );

        foreach ($this->definition->getOptions() as $option) {
            $messages[] = sprintf('  %-29s %s %s',
                '<info>--'.$option->getName().'</info>',
                $option->getShortcut() ? '<info>-'.$option->getShortcut().'</info>' : '  ',
                $option->getDescription()
            );
        }

        return implode("\n", $messages);
    }

    public function getRegistryPath(InputInterface $input)
    {
        $registryPath = getcwd();
        if ($input instanceof Input\ArgvInput && $input->getRegistryPathArgument() !== false) {
            $registryPath = $input->getRegistryPathArgument();
        }
        return $registryPath;
    }

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        // TODO move this to a helper?
        return $this->container;
    }
}