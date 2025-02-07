<?php

/*
 * This file is part of the Stomp package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Stomp\Exception;

/**
 * Exception that occurs, when a frame receipt was not received.
 *
 *
 * @package Stomp
 * @author Jens Radtke <swefl.oss@fin-sn.de>
 */
class MissingReceiptException extends StompException
{
    private string $receiptId;

    public function __construct(string $receiptId)
    {
        $this->receiptId = $receiptId;
        parent::__construct(
            sprintf(
                'Missing receipt Frame for id "%s". Maybe the queue server is under heavy load. ' .
                'Try to increase timeouts.',
                $receiptId
            )
        );
    }

    /**
     * Expected receipt id.
     */
    public function getReceiptId() : string
    {
        return $this->receiptId;
    }
}
