# Reports plugin for Craft CMS 3.x

Write reports with Twig.

![Icon](resources/icon.png)

## Requirements

This plugin requires Craft CMS 3.0.0 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require superbigco/craft-reports

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Reports.

## Reports Overview

-Insert text here-

## Configuring Reports

You can override the options with a config file called reports.php:

```php
<?php
return [
    'enableScheduler' => true,
    'helpers'          => [
        'formatUsers' => function($users) {
            /** @var \craft\elements\User[] $user */
        }
    ],
];
```

## Using Reports

-Insert text here-

## Reports Roadmap

Some things to do, and ideas for potential features:

- [x] Port Craft 2 version
- [x] Chainable content API
- [ ] Document content API
- [ ] Fields support
- [ ] Charts support
- [ ] Widget support
- [ ] Scheduled reports
- [x] Email as report target
- [ ] Slack as report target
- [ ] Event for registering report target
- [ ] Template helpers
- [ ] Report sources (think Slack slash command or CraftQL)
- [x] Permissions (Create, View, Export, Run, Delete)

Brought to you by [Superbig](https://superbig.co)
