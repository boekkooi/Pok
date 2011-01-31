<?php
namespace Pok\Application\Console\Command\Package;

use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputArgument;

class ListCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('package:list')
            ->setAliases(array('pl'))
            ->setDescription('A list of packages in a channel.')
            ->setDefinition(array(
                 new InputArgument(
                     'channel', InputArgument::REQUIRED, 'Channel name or uri.'
                 )
            ))
            ->setHelp(<<<EOT
List all packages in a channel.
EOT
        );
    }

    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        // TODO use local cache first

        // Resolve the channel loader
        $loader = $this->application->getContainer()->get('ChannelLoader');
        $loader = $loader->resolve($input->getArgument('channel'));
        if ($loader === false) {
            $output->write('<error>Unable to resolve channel.</error>', true);
            return -1;
        }

        // Load the channel
        $channel = $loader->load($input->getArgument('channel'));
        if (!($channel instanceof \Pok\Channel\ChannelInterface)) {
            throw new \RuntimeException('Expected instance of `\Pok\Channel\ChannelInterface`.');
        }

        // Get packages
        $output->write(sprintf('<info>Packages for channel %s:</info>', $channel->getName()), true);
        $packages = $channel->getPackages();
        foreach ($packages as $package) {
            $output->write(sprintf(' <info>[%s]:</info>', $package->getName()), true);
            foreach ($package->getVersions() as $version) {
                $output->write(sprintf('  - %s', $version), true);
            }
        }
    }
}
