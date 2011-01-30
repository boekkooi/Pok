<?php
namespace Pok\Component\Metadata\Extension\File;

// TODO add validation on the setters
class File {
    protected $role;

    protected $source;

    protected $target;

    protected $tasks = array();

    public function __construct($source, $target, $role) {
        $this->setSource($source)
            ->setTarget($target)
            ->setRole($role);
    }

    /**
     * Get the file role.
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Get the file source path (aka the package path)
     *
     * @return string A relative source path.
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Get the file target path.
     *
     * @return string A relative target path.
     */
    public function getTarget()
    {
        return $this->target;
    }

    public function getTasks() {
        return $this->tasks;
    }

    public function addTask($identifier, array $options) {
        $this->tasks[$identifier] = $options;

        return $this;
    }

    public function setTarget($target)
    {
        $this->target = str_replace('\\', '/', $target);

        return $this;
    }

    protected function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    protected function setSource($source)
    {
        $this->source = str_replace('\\', '/', $source);

        return $this;
    }
}
