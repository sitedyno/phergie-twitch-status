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

use DomainException;
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
     * Mock event emitter
     *
     * @var \Evenement\EventEmitterInterface
     */
    protected $emitter;

    /**
     * Mock event object
     *
     * @var \Phergie\Irc\Event\EventInterface
     */
    protected $event;

    /**
     * Mock logger object
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Mock event queue object
     *
     * @var \Phergie\Irc\Bot\React\EventQueueInterface
     */
    protected $queue;

    /**
     * Setup
     */
    public function setUp()
    {
        $this->event = Phake::mock('\Phergie\Irc\Event\UserEventInterface');
        $this->queue = Phake::mock('\Phergie\Irc\Bot\React\EventQueueInterface');
        $this->emitter = Phake::mock('\Evenement\EventEmitterInterface');
        $this->logger = Phake::mock('\Psr\Log\LoggerInterface');
        $this->plugin = new Plugin;
        $this->plugin->setEventEmitter($this->emitter);
        $this->plugin->setLogger($this->logger);
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
     * Data provider for testInvalidConfiguration
     */
    public function invalidConfigurations()
    {
        yield [
            ['responseFormat' => 1],
            Plugin::INVALID_RESPONSEFORMAT,
        ];
    }

    /**
     * Tests that an exception is thrown for invalid configuration
     *
     * @param array $config
     * @param int $error
     * @dataProvider invalidConfigurations
     */
    public function testInvalidConfiguration(array $config, $error)
    {
        try {
            $plugin = new Plugin($config);
        } catch (DomainException $e) {
            $this->assertSame($error, $e->getCode());
        }
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

    /**
     * Supplies urls for testHandleUrl
     */
    public function channelUrls()
    {
        yield [
            'http://www.twitch.tv/test_channel',
        ];
    }

    /**
     * Tests handleUrl() with a valid URL
     *
     * @param string $url
     * @dataProvider channelUrls
     */
    public function testHandleUrl($url)
    {
        $this->plugin->handleUrl($url, $this->event, $this->queue);

        Phake::verify($this->emitter)->emit('http.request', Phake::capture($params));

        $this->assertInternalType('array', $params);
        $this->assertCount(1, $params);
        $request = reset($params);
        $this->assertInstanceOf('\Phergie\Plugin\Http\Request', $request);
        $this->assertSame('https://api.twitch.tv/kraken/streams/test_channel', $request->getUrl());

        $config = $request->getConfig();
        $this->assertInternalType('array', $config);
        $this->assertArrayHasKey('resolveCallback', $config);
        $this->assertInternalType('callable', $config['resolveCallback']);
        $this->assertArrayHasKey('rejectCallback', $config);
        $this->assertInternalType('callable', $config['rejectCallback']);

        foreach ($this->channelNames() as $url => $channelName) {
            if (empty($channelName)) {
                Phake::verifyNoFurtherInteraction($this->emitter);
                $this->plugin->handleUrl($url, $this->event, $this->queue);
            }
        }
    }
}
