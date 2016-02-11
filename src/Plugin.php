<?php
/**
 * Phergie plugin for fetching a twitch channel's status/title (https://github.com/sitedyno/phergie-twitch-status)
 *
 * @link https://github.com/sitedyno/phergie-twitch-status for the canonical source repository
 * @copyright Copyright (c) 2016 Heath Nail (https://github.com/sitedyno)
 * @license https://opensource.org/licenses/MIT MIT License
 * @package Sitedyno\Phergie\Plugin\Twitch-status
 */

namespace Sitedyno\Phergie\Plugin\TwitchStatus;

use Phergie\Irc\Bot\React\AbstractPlugin;
use Phergie\Irc\Bot\React\EventQueueInterface as Queue;
use Phergie\Irc\Event\EventInterface as Event;

/**
 * Plugin class.
 *
 * @category Sitedyno
 * @package Sitedyno\Phergie\Plugin\Twitch-status
 */
class Plugin extends AbstractPlugin
{
    /**
     * Accepts plugin configuration.
     *
     * Supported keys:
     *
     *
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {

    }

    /**
     *
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'irc.' => 'handleEvent',
        ];
    }

    /**
     *
     *
     * @param \Phergie\Irc\Event\EventInterface $event
     * @param \Phergie\Irc\Bot\React\EventQueueInterface $queue
     */
    public function handleEvent(Event $event, Queue $queue)
    {
    }
}
