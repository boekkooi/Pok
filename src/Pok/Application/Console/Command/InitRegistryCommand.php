<?php
namespace Pok\Application\Console\Command;

use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputInterface;

class InitRegistryCommand extends Command {
    protected $name = 'pok:hidden:init';

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->registryPath = $this->application->getRegistryPath($input);
        $this->userCancel = !$this->getHelper('dialog')->askConfirmation(
            $output,
            sprintf('<question>Initialize registry in `%s`? (y/n)</question>', $this->registryPath),
            false
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->userCancel) {
            $output->write('<info>Unable to create registry, user cancelled.</info>');
            return -1;
        }

        // TODO use umask?
        if (!mkdir($this->registryPath . '/.pok', 0777, true)) {
            $output->write('<info>Unable to create registry.</info>');
            return -1;
        }

        return 0;
    }

    protected function mergeApplicationDefinition()
    {
        return;
    }
}
