<?php
/*
 * This file is part of the Stomp package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Stomp\States;

use Stomp\Client;
use Stomp\Protocol\Protocol;
use Stomp\StatefulStomp;
use Stomp\States\Exception\InvalidStateException;
use Stomp\States\Meta\SubscriptionList;
use Stomp\Transport\Frame;
use Stomp\Transport\Message;

/**
 * StateTemplate for StompStates.
 *
 * @package Stomp\States
 * @author Jens Radtke <swefl.oss@fin-sn.de>
 */
abstract class StateTemplate extends StateSetter implements IStateful
{
    /**
     * @var Client
     */
    private $client;

    /**
     * StateMachine
     *
     * @var StatefulStomp
     */
    private $base;

    /**
     * StateTemplate constructor.
     * @param Client $client
     * @param StatefulStomp $base
     */
    public function __construct(Client $client, StatefulStomp $base)
    {
        $this->client = $client;
        $this->base = $base;
    }

    /**
     * Returns the base StateMachine.
     *
     * @return StatefulStomp
     */
    protected function getBase()
    {
        return $this->base;
    }

    /**
     * Activates the current state, after it has been applied on base.
     * Returns the subscription ID.
     */
    abstract protected function init(array $options = []) : string|int|null;

    /**
     * Returns the options needed in current state.
     */
    abstract protected function getOptions() : array;

    protected function getClient() : Client
    {
        return $this->client;
    }

    protected function getProtocol() : Protocol
    {
        return $this->client->getProtocol();
    }

    /**
     * @inheritdoc
     */
    protected function setState(IStateful $state, array $options = []) : string|int|null
    {
        $init = null;
        if ($state instanceof StateTemplate) {
            $init = $state->init($options);
        }

        $this->base->setState($state);
        return $init;
    }

    /**
     * @inheritdoc
     */
    public function ack(Frame $frame) : void
    {
        throw new InvalidStateException($this, __FUNCTION__);
    }

    /**
     * @inheritdoc
     */
    public function nack(Frame $frame, ?bool $requeue = null) : void
    {
        throw new InvalidStateException($this, __FUNCTION__);
    }

    /**
     * @inheritdoc
     */
    public function send(string $destination, Message $message) : bool
    {
        return $this->getClient()->send($destination, $message);
    }

    /**
     * @inheritdoc
     */
    public function begin() : void
    {
        throw new InvalidStateException($this, __FUNCTION__);
    }

    /**
     * @inheritdoc
     */
    public function commit() : void
    {
        throw new InvalidStateException($this, __FUNCTION__);
    }

    /**
     * @inheritdoc
     */
    public function abort() : void
    {
        throw new InvalidStateException($this, __FUNCTION__);
    }

    /**
     * @inheritdoc
     */
    public function subscribe(string $destination, ?string $selector, string $ack, array $header = []) : string|int|null
    {
        throw new InvalidStateException($this, __FUNCTION__);
    }

    /**
     * @inheritdoc
     */
    public function unsubscribe(string|int|null $subscriptionId = null) : void
    {
        throw new InvalidStateException($this, __FUNCTION__);
    }

    /**
     * @inheritdoc
     */
    public function read() : ?Frame
    {
        throw new InvalidStateException($this, __FUNCTION__);
    }

    /**
     * @inheritdoc
     */
    public function getSubscriptions() : SubscriptionList
    {
        return new SubscriptionList();
    }
}
