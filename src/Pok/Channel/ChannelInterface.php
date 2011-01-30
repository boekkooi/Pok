<?php
namespace Pok\Channel;

interface ChannelInterface {
    /**
     * Returns the channel schema.
     *
     * @abstract
     * @return string
     */
    function getScheme();

    /**
     * Returns the channel name.
     *
     * @abstract
     * @return string
     */
    function getName();

    /**
     * Get a list of package identifiers.
     * @example [ '<scheme>://<host>/package' ]
     * @abstract
     * @return array An array of PackageReference instances
     */
    function getPackages();
}
