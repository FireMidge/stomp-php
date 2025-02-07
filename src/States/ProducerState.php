<?php
/*
 * This file is part of the Stomp package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Stomp\States;

/**
 * ProducerState client is working as a message producer.
 *
 * @package Stomp\States
 * @author Jens Radtke <swefl.oss@fin-sn.de>
 */
class ProducerState extends StateTemplate
{
    protected function init(array $options = []) : int|string|null
    {
        // nothing to do here
        return null;
    }

    /**
     * @inheritdoc
     */
    public function begin() : void
    {
        $this->setState(new ProducerTransactionState($this->getClient(), $this->getBase()));
    }

    /**
     * @inheritdoc
     */
    public function subscribe(string $destination, ?string $selector, string $ack, array $header = []) : string|int|null
    {
        return $this->setState(
            new ConsumerState($this->getClient(), $this->getBase()),
            ['destination' => $destination, 'selector' => $selector, 'ack' => $ack, 'header' => $header]
        );
    }

    /**
     * @inheritdoc
     */
    protected function getOptions() : array
    {
        return [];
    }
}
