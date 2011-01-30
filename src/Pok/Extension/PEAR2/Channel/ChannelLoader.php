<?php
namespace Pok\Extension\PEAR2\Channel;

use Pok\Channel\Loader\Loader,
    Pok\Channel\Loader\ResolverInterface;

/**
 * PEAR2 REST Channel loader.
 *
 * @see http://pear.php.net/manual/en/core.rest.php
 */
class ChannelLoader extends Loader {
    /**
     * Loads a resource.
     *
     * A resource can be anything that can be converted to a
     * Channel instance.
     *
     * @param mixed $resource The resource
     */
    public function load($resource)
    {
        // Load url
        $resource = substr($resource, strlen($this->schema));

        // TODO get client from di? or as param?
        $client = new \Pok\Component\Remote\CurlClient();
        $client->request('GET', 'http://' . $resource . '/channel.xml');
        $response = $client->getResponse();
        if ($response->getStatus() !== 200) {
            $client->request('GET', 'http://' . $resource . '/channel.xml');
            if ($response->getStatus() !== 200) {
                throw new \RuntimeException('Unable to load.');
            }
        }

        $xml = new \SimpleXMLElement($response->getContent());
        return $this->parseChannel($xml);
    }

    protected function parseChannel(\SimpleXMLElement $resource)
    {
        // TODO add alias, validatepackage support, summary, server mirrors
        $channel = new Channel();
        $channel->setName((string)$resource->name);
        $channel->setServers(
            $this->parseServer($resource->servers->primary[0])
        );

        return $channel;
    }

    protected function parseServer(\SimpleXMLElement $server)
    {
        $servers = array();

        // TODO add support for soap, xmlrpc
        if ($server->rest->count() > 0) {
            foreach ($server->rest[0]->baseurl as $baseUrl) {
                $servers[] = new Server\Rest((string)$baseUrl, $baseUrl['type']);
            }
        } else {
            throw new \RuntimeException('Only rest servers are supported at the moment');
        }

        return $servers;
    }
}
