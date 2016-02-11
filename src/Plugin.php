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
use Phergie\Plugin\Http\Request as HttpRequest;

/**
 * Plugin class.
 *
 * @category Sitedyno
 * @package Sitedyno\Phergie\Plugin\Twitch-status
 */
class Plugin extends AbstractPlugin
{
    /**
     * Twitch API Url
     */
    protected $apiUrl = 'https://api.twitch.tv/kraken/streams/';

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
            'url.host.twitch.tv' => 'handleUrl',
        ];
    }

    /**
     *
     *
     * @param \Phergie\Irc\Event\EventInterface $event
     * @param \Phergie\Irc\Bot\React\EventQueueInterface $queue
     */
    public function handleUrl($url, Event $event, Queue $queue)
    {
        $logger = $this->getLogger();
        $logger->info('handleUrl', ['url' => $url]);
        $channelName = $this->getChannelName($url);
        if (empty($channelName)) {
            return;
        }
        $apiUrl = $this->getApiUrl($channelName);
        $request = $this->getApiRequest($apiUrl, $event, $queue);
        $this->getEventEmitter()->emit('http.request', [$request]);
    }

    /**
     * Get the channel's name from a url.
     *
     * @param string $url
     * @return string The channel's name
     */
    public function getChannelName($url)
    {
        $schemes = ['http://', 'https://'];
        $reservedWords = ['directory'];
        foreach ($schemes as $scheme) {
            $url = str_replace($scheme, '', $url);
        }
        $parts = explode('/', $url);
        if (count($parts) > 2) {
            return '';
        }
        foreach ($reservedWords as $reservedWord) {
            if ($parts[1] === $reservedWord) {
                return '';
            }
        }
        return $parts[1];
    }

    /**
     * Returns the API URL for the request.
     *
     * @param string $channelName
     * @return string The API URL
     */
    public function getApiUrl($channelName)
    {
        return $this->apiUrl . $channelName;
    }

    /**
     * Returns an API request to get data for a channel.
     *
     * @param string API request URL
     * @param \Phergie\Irc\Bot\React\EventInterface $event
     * @param \Phergie\Irc\Bot\React\EventQueueInterface $queue
     * @return use Phergie\Plugin\Http\Request
     */
    public function getApiRequest($url, Event $event, Queue $queue)
    {
        $request = new HttpRequest([
            'url' => $url,
            'headers' => ['Accept: application/vnd.twitchtv.v3+json'],
            'resolveCallback' => function($data) use ($url, $event, $queue) {
                $this->resolve($url, $data, $event, $queue);
            },
            'rejectCallback' => function($error) use ($url) {
                $this->reject($url, $error);
            }
        ]);
        return $request;
    }

    /**
     * Handles a successful request for video data.
     *
     * @param string $url URL of the request
     * @param \GuzzleHttp\Message\Response $data Response body
     * @param \Phergie\Irc\EventInterface $event
     * @param \Phergie\Irc\Bot\React\EventQueueInterface $queue
     */
    public function resolve($url, \GuzzleHttp\Message\Response $data, Event $event, Queue $queue)
    {
        $logger = $this->getLogger();
        $json = json_decode($data->getBody());
        $logger->info('resolve', ['url' => $url, 'json', $json]);
        if (isset($json->error)) {
            return $logger->warning('Twitch response error',
                ['url' => $url, 'error' => $json->error]);
        }
        if (null === $json->stream) {
            return $queue->ircPrivmsg($event->getSource(), "Stream is offline");
        }
        $replacements = $this->getReplacements($json);
        $message = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $this->responseFormat
        );
        $queue->ircPrivmsg($event->getSource(), $message);
    }

    /**
     * Handles a failed request for channel data.
     *
     * @param string $url URL of the failed request
     * @param string $error Error describing the failure
     */
    public function reject($url, $error)
    {
        $this->getLogger()->warning(
            'Request for video data failed',
            array(
                'url' => $url,
                'error' => $error,
            )
        );
    }

    /**
     * Returns replacements for response format for the given channel data
     *
     * @param object $data JSON data
     * @return array
     */
    protected function getReplacements($data)
    {
        $game = $data->stream->game;
        $viewers = $data->stream->viewers;
        $mature = $data->channel->mature ? 'yes' : 'no';
        $status = $data->channel->status;
        $display_name = $data->channel->display_name;
        $name = $data->channel->name;
        $partner = $data->channel->partner ? 'yes' : 'no';
        $url = $data->channel->url;
        $views = $data->channel->views;
        $followers = $data->channel->followers;
        return [
            'game' => $game,
            'viewers' => $viewers,
            'mature' => $mature,
            'status' => $status,
            'display_name' => $display_name,
            'name' => $name,
            'partner' => $partner,
            'url' => $url,
            'views' => $views,
            'followers' => $followers
        ];
    }
}
