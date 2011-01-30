<?php
namespace Pok\Extension\PEAR2\Channel;

class Channel extends \Pok\Channel\Channel {
    protected $servers = array();

    public function getServers() {
        return $this->servers;
    }

    public function setServers(array $servers) {
        // TODO add interface to validate, and do a real add (not override)
        $this->servers = $servers;

        return $this;
    }

    /**
     * Get a list of package identifiers.
     * @example [ '<scheme>://<host>/package' ]
     * @abstract
     * @return array
     */
    public function getPackages() {
        // TODO get client from di? or as param?
        $client = new \Pok\Component\Remote\CurlClient();
        foreach ($this->servers as $server) {
            // TODO add support for soap, xmlprc
            if ($server instanceof Server\Rest) {
                // TODO move this to some nice REST api class?
                $client->request('GET', $server->getUrl() . '/p/packages.xml');
                $response = $client->getResponse();
                if ($response->getStatus() !== 200) {
                    continue;
                }

                // Get all channel packages
                $xml = new \SimpleXMLElement($response->getContent());
                $packages = array();
                foreach ($xml->p as $package) {
                    $packages[(string)$package] = $server->getUrl() . '/r/' . (string)$package . '/allreleases.xml';
                }

                // Get all package versions
                foreach ($packages as $name => $package) {
                    $client->request('GET', $package);
                    $response = $client->getResponse();
                    if ($response->getStatus() !== 200) {
                        // TODO throw a warning here?
                        continue;
                    }

                    $versions = array();
                    $xml = new \SimpleXMLElement($response->getContent());
                    foreach ($xml->r as $r) {
                        $version = (string)$r->v;
                        $stability = '';
                        switch ((string)$r->s) {
                            case 'alpha':
                                $stability = 'a';
                                break;
                            case 'beta':
                                $stability = 'b';
                                break;
                            case 'stable':
                                break;
                            default:
                                trigger_error(sprintf('Unable to resolve stability `$1%s` for `$2%s` assuming `dev`', (string)$r->s, $name), E_USER_WARNING);
                            case 'devel':
                                $stability = 'dev';
                                break;
                        }
                        if (strpos($version, $stability) === false) {
                            $version .= $stability;
                        }
                        $versions[] = $version;
                    }
                    $packages[$name] = $versions;
                }

                // Make the package references
                foreach ($packages as $name => $versions) {
                    $ref = new \Pok\Channel\PackageReference();
                    $ref->setName($name);
                    $ref->setChannel($this);
                    $ref->setVersions($versions);
                    $packages[$name] = $ref;
                }

                return $packages;
            }
        }

        throw new \RuntimeException('Unable to load channel packages');
    }
}
