<?php
namespace Pok\Application\Console\Input;

class ArgvInput extends \Symfony\Component\Console\Input\ArgvInput {
    protected $registryPath = false;

    /**
     * Constructor.
     *
     * @param array $argv An array of parameters from the CLI (in the argv format)
     */
    public function __construct(array $argv = null)
    {
        parent::__construct($argv, null);

        // Strip the first argument if it is a directory because it will then be the pyrus directory
        if (isset($this->tokens[0]) && @is_dir($this->tokens[0])) {
            $this->registryPath = $this->tokens[0];
            array_shift($this->tokens);
        }
    }

    /**
     * Returns the pyrus path argument from the raw parameters (not parsed).
     *
     * @return string The value of the pyrus path argument or false otherwise
     */
    public function getRegistryPathArgument()
    {
        return $this->registryPath;
    }
}