<?php
namespace Pok\Component\Metadata\Extension\Dependency;

class Dependency {
    const REQUIRED = 1;
    const OPTIONAL = 2;
    const CONFLICT = 3;

    protected $type;
    protected $identifier;
    protected $min;
    protected $max;

    public function __construct($identifier, $min = null, $max = null, $type = null) {
        $this->identifier = $identifier;
        $this->min = $min;
        $this->max = $max;
        $this->type = $type;
    }
}
