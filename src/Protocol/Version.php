<?php
/*
 * This file is part of the Stomp package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Stomp\Protocol;

use Stomp\Broker\ActiveMq\ActiveMq;
use Stomp\Broker\Apollo\Apollo;
use Stomp\Broker\RabbitMq\RabbitMq;
use Stomp\Broker\OpenMq\OpenMq;
use Stomp\Exception\StompException;
use Stomp\Exception\UnexpectedResponseException;
use Stomp\Transport\Frame;

/**
 * Version determine stomp version and server dialect.
 *
 * @package Stomp\Protocol
 * @author Jens Radtke <swefl.oss@fin-sn.de>
 */
class Version
{
    /**
     * Stomp Version 1.0
     */
    const VERSION_1_0 = '1.0';
    /**
     * Stomp Version 1.1
     */
    const VERSION_1_1 = '1.1';
    /**
     * Stomp Version 1.2
     */
    const VERSION_1_2 = '1.2';

    private Frame $frame;


    /**
     * @throws StompException
     */
    public function __construct(Frame $frame)
    {
        if ($frame->getCommand() != 'CONNECTED') {
            throw new UnexpectedResponseException(
                $frame,
                sprintf('Expected a "CONNECTED" Frame to determine Version. Got a "%s" Frame!', $frame->getCommand())
            );
        }
        $this->frame = $frame;
    }

    /**
     * Returns the protocol to use.
     *
     * @param string $default server to use of no server detected
     * @return ActiveMq|Apollo|Protocol|RabbitMq
     */
    public function getProtocol(string $clientId, string $default = 'ActiveMQ/5.11.1') : Protocol
    {
        $server = trim((string) $this->frame['server']) ?: $default;
        $version = $this->getVersion();

        if (stristr($server, 'rabbitmq') !== false) {
            return new RabbitMq($clientId, $version, $server);
        }
        if (stristr($server, 'apache-apollo') !== false) {
            return new Apollo($clientId, $version, $server);
        }
        if (stristr($server, 'activemq') !== false) {
            return new ActiveMq($clientId, $version, $server);
        }
        if (stristr($server, 'open message queue') !== false) {
            return new OpenMq($clientId, $version, $server);
        }

        return new Protocol($clientId, $version, $server);
    }

    /**
     * Detected version
     */
    public function getVersion() : string
    {
        return $this->frame['version'] ?: self::VERSION_1_0;
    }

    /**
     * Check if version is same or newer than given one.
     *
     * @param string $version to check against
     */
    public function hasVersion(string $version) : bool
    {
        return version_compare($this->getVersion(), $version, '>=');
    }
}
