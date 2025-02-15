<?php
/*
 * This file is part of the Stomp package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Stomp;

use Stomp\Exception\StompException;
use Stomp\Protocol\Protocol;
use Stomp\Transport\Frame;
use Stomp\Transport\Message;

/**
 * Simple Stomp Client
 *
 * This is a legacy implementation of the old Stomp Client (Version 2-3).
 * It's an almost stateless client, only wrapping some protocol calls for you.
 *
 * @package Stomp
 * @author Jens Radtke <swefl.oss@fin-sn.de>
 */
class SimpleStomp
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Read response frame from server
     */
    public function read() : ?Frame
    {
        return $this->client->readFrame();
    }

    /**
     * Register to listen to a given destination
     *
     * @param string $destination Destination queue
     */
    public function subscribe(
        string $destination,
        ?string $subscriptionId = null,
        string $ack = 'auto',
        ?string $selector = null,
        array $header = []
    ) : bool {
        return $this->client->sendFrame(
            $this->getProtocol()->getSubscribeFrame($destination, $subscriptionId, $ack, $selector)->addHeaders($header)
        );
    }

    protected function getProtocol() : ?Protocol
    {
        return $this->client->getProtocol();
    }

    /**
     * Send a message
     *
     * @throws StompException
     */
    public function send(string $destination, Message $message) : bool
    {
        return $this->client->send($destination, $message);
    }

    /**
     * Remove an existing subscription
     *
     * @throws StompException
     */
    public function unsubscribe(string $destination, ?string $subscriptionId = null, array $header = []) : bool
    {
        return $this->client->sendFrame(
            $this->getProtocol()->getUnsubscribeFrame($destination, $subscriptionId)->addHeaders($header)
        );
    }

    /**
     * Start a transaction
     *
     * @throws StompException
     */
    public function begin(?string $transactionId = null) : bool
    {
        return $this->client->sendFrame($this->getProtocol()->getBeginFrame($transactionId));
    }

    /**
     * Commit a transaction in progress
     *
     * @throws StompException
     */
    public function commit(?string $transactionId = null) : bool
    {
        return $this->client->sendFrame($this->getProtocol()->getCommitFrame($transactionId));
    }

    /**
     * Roll back a transaction in progress
     */
    public function abort(?string $transactionId = null) : bool
    {
        return $this->client->sendFrame($this->getProtocol()->getAbortFrame($transactionId));
    }

    /**
     * Acknowledge consumption of a message from a subscription
     *
     * @param Frame $frame
     * @return void
     */
    public function ack(Frame $frame) : void
    {
        $this->client->sendFrame($this->getProtocol()->getAckFrame($frame), false);
    }

    /**
     * Not acknowledge consumption of a message from a subscription
     */
    public function nack(Frame $frame) : void
    {
        $this->client->sendFrame($this->getProtocol()->getNackFrame($frame), false);
    }
}
