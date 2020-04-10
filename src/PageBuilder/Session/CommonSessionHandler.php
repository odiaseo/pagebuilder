<?php
namespace PageBuilder\Session;

use Laminas\Session\SaveHandler\SaveHandlerInterface;

/**
 * Class MemcachedSession
 *
 * @package PageBuilder\Session
 */
class CommonSessionHandler extends \SessionHandler implements SaveHandlerInterface
{
    /**
     * @param string $sessionId
     *
     * @return string
     */
    public function read($sessionId)
    {
        return (string)parent::read($sessionId);
    }
}
