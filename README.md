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

Reports for Craft CMS and Craft Commerce makes it possible to write reports in Twig via a simple, fluent API.

It also makes it possible to setup report targets like email and (soon) Slack.

This way you can send reports directly to your target channels, either on demands or (soon) on a schedule.

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

You may use includes both for the Report Content and Settings fields.

The content Twig will be passed the following variables:

- `result` - a `ReportResult` model behind the scenes  
- `report` - a `Report` model behind the scenes

### Report Example

To generate a list of users that has logged in the last 30 days:

```twig
{% set loginPeriod = now|date_modify('-30 days') %}
{% set users = craft.users.lastLoginDate('> ' ~ loginPeriod|atom).all() %}

{% if result is defined %}
    {% do result.setHeader(['Username', 'Name', 'Email']) %}
    {% for user in users %}
        {% do result.append([user.username, user.getName(), user.email] ]) %}
    {% endfor %}
{% endif %}
```

## Reports Roadmap

Some things to do, and ideas for potential features:

- [x] Port Craft 2 version
- [x] Chainable content API
- [ ] Document content API
- [ ] Document report targets
- [ ] Fields support
- [ ] Charts support
- [ ] Widget support
- [ ] Scheduled reports
- [x] Email as report target
- [ ] Slack as report target
- [ ] Event for registering report target
- [ ] Template helpers
- [ ] Document helpers
- [ ] Report sources (think Slack slash command or CraftQL)
- [x] Permissions (Create, View, Export, Run, Delete)

Brought to you by [Superbig](https://superbig.co)
