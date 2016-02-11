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
     * Setup
     */
    public function setUp()
    {
        $this->plugin = new Plugin;
    }

    /**
     * Teardown
     */
    public function tearDown()
    {
        $this->plugin = null;
    }

    /**
     * Tests that getSubscribedEvents() returns an array.
     */
    public function testGetSubscribedEvents()
    {
        $plugin = new Plugin;
        $this->assertInternalType('array', $plugin->getSubscribedEvents());
    }

    /**
     * Supplies urls for getChannelName
     */
    public function channelNames()
    {
        yield [
            'http://www.twitch.tv/directory/following',
            ''
        ];

        yield [
            'http://www.twitch.tv/directory',
            ''
        ];

        yield [
            'http://www.twitch.tv/test_channel',
            'test_channel'
        ];
    }

    /**
     * Test extracting channel name from urls
     *
     * @dataProvider channelNames
     */
    public function testGetChannelName($url, $channelName)
    {
        $this->assertSame($channelName, $this->plugin->getChannelName($url));
    }

    /**
     * Supplies channel names for testGetApiUrl
     */
    public function apiUrls()
    {
        yield [
            'test_channel',
            'https://api.twitch.tv/kraken/streams/test_channel'
        ];
    }

    /**
     * Test getting API URLs
     *
     * @dataProvider apiUrls
     */
    public function testGetApiUrl($channelName, $apiUrl)
    {
        $this->assertSame($apiUrl, $this->plugin->getApiUrl($channelName));
    }
}
