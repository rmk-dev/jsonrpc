<?php

namespace Rmk\JsonRpc;

/**
 * Notification Response
 *
 * Empty response when a notification call is made (without call id)
 */
class NotificationResponse extends Response
{
    /**
     * Skip passing parameters to the constructor
     */
    public function __construct()
    {
        parent::__construct(null);
    }

    /**
     * Response with empty result
     *
     * @return array
     */
    public function respond(): array
    {
        return [];
    }

    /**
     * Return empty result for serializing
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->respond();
    }
}
