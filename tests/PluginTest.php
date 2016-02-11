<?php
/**
 * Phergie plugin for fetching a twitch channel's status/title (https://github.com/sitedyno/phergie-twitch-status)
 *
 * @link https://github.com/sitedyno/phergie-twitch-status for the canonical source repository
 * @copyright Copyright (c) 2016 Heath Nail (https://github.com/sitedyno)
 * @license https://opensource.org/licenses/MIT MIT License
 * @package Sitedyno\Phergie\Plugin\Twitch-status
 */

namespace Phergie\Irc\Tests\Plugin\React\TwitchStatus;

use Phake;
use Phergie\Irc\Bot\React\EventQueueInterface as Queue;
use Phergie\Irc\Event\EventInterface as Event;
use Sitedyno\Phergie\Plugin\TwitchStatus\Plugin;

/**
 * Tests for the Plugin class.
 *
 * @category Sitedyno
 * @package Sitedyno\Phergie\Plugin\Twitch-status
 */
class PluginTest extends \PHPUnit_Framework_TestCase
{


    /**
     * Tests that getSubscribedEvents() returns an array.
     */
    public function testGetSubscribedEvents()
    {
        $plugin = new Plugin;
        $this->assertInternalType('array', $plugin->getSubscribedEvents());
    }
}
