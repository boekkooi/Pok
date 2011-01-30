<?php
namespace Pok\Component\Metadata\Loader\PEAR2;

use Pok\Component\Metadata\Metadata,
    Pok\Component\Metadata\Extension\File\File,
    Pok\Component\Metadata\Extension\File\Collection as FileCollection,
    Pok\Component\Metadata\Extension\Dependency\Dependency,
    Pok\Component\Metadata\Extension\Dependency\Collection as DependencyCollection;

class Loader {
	public function load($resource) {
		if (is_string($resource)) {
			$resource = new \SimpleXMLIterator($resource, null, true);
		} elseif (!($resource instanceof \SimpleXMLElement)) {
			throw new \InvalidArgumentException('Invalid resource supplied.');
		}

        // Create package metadata
        $meta = new Metadata(
            $this->getIdentifier($resource),
            $this->getVersion($resource)
        );

        // Load contents
        if ($resource->contents->count() === 0) {
            throw new \LogicException('No package contents found.');
        }
        $this->parseContents($resource->contents[0], $meta->getFiles());

        // Load php releases
        // TODO support install conditions
        $this->parsePhpReleases($resource->phprelease, $meta->getFiles());

        // Load dependencies (if there are any)
        if ($resource->dependencies->count() > 0) {
            $this->parseDependencies($resource->dependencies, $meta->getDependencies());
        }

        // Return metadata
        return $meta;
	}

    protected function getIdentifier(\SimpleXMLIterator $resource) {
        $identifier = (string)$resource->name;
        if ($resource->channel->count() === 0) {
            $identifier = (string)$resource->uri . '/' . $identifier;
        } else {
            $identifier = (string)$resource->channel . '/' . $identifier;
        }
        return $identifier;
    }

    protected function getVersion(\SimpleXMLIterator $resource) {
        // TODO Why would i support API version?
        return (string)$resource->version->release;
    }

    protected function parseContents(\SimpleXMLIterator $iterator, FileCollection $files) {
        // Load files
        $iterator = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::SELF_FIRST);
        $iterator->rewind();

        $lastDepth = -1;
        $path = array();
        while($iterator->valid()) {
            if ($lastDepth > $iterator->getDepth()) {
                array_pop($path);
                while($lastDepth != $iterator->getDepth()) {
                    array_pop($path);
                    $lastDepth--;
                }
            } elseif ($lastDepth === $iterator->getDepth()) {
                array_pop($path);
            }

            $elt = $iterator->current();
            if ($elt->getName() === 'file') {
                $path[] = (string)$elt['name'];

                $filePath = implode('/', $path);
                $file = new File($filePath, $filePath, $elt['role']);

                // Find file tasks
                $tasks = $elt->children('http://pear.php.net/dtd/tasks-1.0');
                foreach ($tasks as $task) {
                    $options = current((array)$task->attributes());
                    $file->addTask($task->getName(), $options);
                }

                $files[] = $file;
            } elseif ($elt->getName() === 'dir') {
                $path[] = ((string)$elt['name'] === '/' ? '' : $elt['name']) .
                          (isset($elt['baseinstalldir']) && (string)$elt['baseinstalldir'] !== '/' ? '/' . $elt['baseinstalldir'] : '');
            }

            $lastDepth = $iterator->getDepth();
            $iterator->next();
        }
    }

    protected function parsePhpReleases(\SimpleXMLIterator $iterator, FileCollection $files) {
        $iterator->rewind();
        while ($iterator->valid()) {
            $release = $iterator->current();

            // TODO parse install conditions
            if ($release->installconditions->count() > 0) {
                throw new \RuntimeException('installconditions are not yet implemented.');
            }

            // Parse file list
            foreach ($release->filelist as $fileList) {
                $this->parseFileList($fileList, $files);
            }
            $iterator->next();
        }
    }

    protected function parseFileList(\SimpleXMLIterator $iterator, FileCollection $files) {
        $iterator->rewind();
        while ($iterator->valid()) {
            $elt = $iterator->current();

            if ($elt->getName() === 'install') {
                $files[(string)$elt['name']]->setTarget((string)$elt['as']);
            } elseif ($elt->getName() === 'ignore') {
                unset($files[(string)$elt['name']]);
            } else {
                throw new \RuntimeException(sprintf('Unknown element `%s` in `filelist`.', $elt->getName()));
            }

            $iterator->next();
        }
    }

    protected function parseDependencies(\SimpleXMLIterator $iterator, DependencyCollection $dependencies) {
        // TODO parse dependency groups?
        if ($iterator->group->count() > 0) {
            throw new \RuntimeException('dependency groups are not yet implemented.');
        }

        // Parse requires
        $this->parseDependencyType($iterator->required[0], $dependencies);

        $this->parseDependencyType($iterator->optional[0], $dependencies, false);
    }

    protected function parseDependencyType(\SimpleXMLIterator $iterator, DependencyCollection $dependencies, $required = true) {
        $iterator->rewind();
        while ($iterator->valid()) {
            $elt = $iterator->current();
            $name = $elt->getName();
            $iterator->next();

            if ($name === 'php') {
                $dependencies->import($this->parsePhpCondition($elt));
            } elseif ($name === 'pearinstaller') {
                // Let's just ignore this for now!
            } else {
                // TODO do not ignore recommended, nodefault and uri, providesextension
                $identifier = 'pear2://' . (string)$elt->channel . '/' . (string)$elt->name;
                $dependencies[] = new Dependency(
                    $identifier,
                    isset($elt->min) ? (string)$elt->min : null,
                    isset($elt->max) ? (string)$elt->max : null,
                    isset($elt->conflicts) ? Dependency::CONFLICT : ($required ? Dependency::REQUIRED : Dependency::OPTIONAL)
                );
                foreach ($elt->exclude as $exclude) {
                    $dependencies[] = new Dependency(
                        $identifier,
                        (string)$exclude,
                        (string)$exclude,
                        Dependency::CONFLICT
                    );
                }
            }
        }
    }

    protected function parsePhpCondition(\SimpleXMLElement $element) {
        $dependencies = array();
        $dependencies[] = new Dependency(
            'system://php',
            isset($element->min) ? (string)$element->min : null,
            isset($element->max) ? (string)$element->max : null,
            Dependency::REQUIRED
        );
        foreach ($element->exclude as $exclude) {
            $dependencies[] = new Dependency(
                'system://php',
                (string)$exclude,
                (string)$exclude,
                Dependency::CONFLICT
            );
        }
        return $dependencies;
    }
}
