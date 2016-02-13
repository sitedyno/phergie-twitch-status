# sitedyno/phergie-twitch-status

[Phergie](http://github.com/phergie/phergie-irc-bot-react/) plugin for fetching a twitch channel's status/title.

[![Build Status](https://secure.travis-ci.org/sitedyno/phergie-twitch-status.png?branch=master)](http://travis-ci.org/sitedyno/phergie-twitch-status)

## Install

The recommended method of installation is [through composer](http://getcomposer.org).

`php composer.phar require sitedyno/phergie-twitch-status`

See Phergie documentation for more information on
[installing and enabling plugins](https://github.com/phergie/phergie-irc-bot-react/wiki/Usage#plugins).

## Configuration

```php
return [
    'plugins' => [
        // dependencies
        new \Phergie\Plugin\Dns\Plugin,
        new \Phergie\Plugin\Http\Plugin,
        new \Phergie\Irc\Plugin\React\Url\Plugin,

        // configuration
        new \Sitedyno\Phergie\Plugin\Twitch-status\Plugin([
            // optional: template used to define response to urls
            'responseFormat' => '%viewers% viewers watching %display_name% play %game% - %status%',
        ])
    ]
];
```

## Tests

To run the unit test suite:

```
curl -s https://getcomposer.org/installer | php
php composer.phar install
./vendor/bin/phpunit
```

## License

Released under the BSD License. See `LICENSE`.
