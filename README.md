# Reports for Craft CMS 5

Write data reports in Twig and deliver them via email, CSV export, or queue jobs.

## Requirements

- Craft CMS 5.5+
- PHP 8.2+

## Installation

```bash
composer require superbig/craft-reports
```

Then install the plugin from the Craft Control Panel or run:

```bash
./craft plugin/install reports
```

## Quick Start

1. Go to **Reports** in the CP sidebar
2. Click **New** and give your report a name and handle
3. Write your report in Twig using the `result` variable:

```twig
{% set users = craft.users.lastLoginDate('> ' ~ now|date_modify('-30 days')|atom).all() %}

{% do result.setHeader(['Username', 'Name', 'Email']) %}
{% for user in users %}
    {% do result.append([user.username, user.getName(), user.email]) %}
{% endfor %}
```

4. Click **Run** to see the output, or **Export** to download as CSV

## Configuration

Create a `config/reports.php` file to override defaults:

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `enableScheduler` | `bool` | `true` | Enable the report scheduler |
| `helpers` | `array` | `[]` | Custom helper functions available in report templates |
| `pluginName` | `string` | `'Reports'` | Override the plugin name in the CP |

```php
<?php

return [
    'enableScheduler' => true,
    'pluginName' => 'Reports',
    'helpers' => [],
];
```

## Writing Reports

Report templates receive two variables:

- **`result`** — a `ReportResult` model for building tabular output
- **`report`** — the `Report` model for the current report

### ReportResult API

| Method | Description |
|--------|-------------|
| `result.setHeader(array)` | Set column headers |
| `result.append(array)` | Add a row (or array of rows) |
| `result.setFilename(string)` | Set the CSV export filename |
| `result.setContent(array)` | Replace all rows |
| `result.getHeader()` | Get current headers |
| `result.getContent()` | Get all rows |

### Example: Commerce Orders

```twig
{% set orders = craft.orders.dateOrdered('> ' ~ now|date_modify('-7 days')|atom).all() %}

{% do result.setHeader(['Order #', 'Email', 'Total', 'Date']) %}
{% do result.setFilename('weekly-orders') %}
{% for order in orders %}
    {% do result.append([order.number, order.email, order.totalPrice|currency, order.dateOrdered|date]) %}
{% endfor %}
```

## Report Targets

Report targets deliver report results to external channels. Currently supported:

- **Email** — Send report results as CSV attachments

### Setting Up an Email Target

1. Go to **Reports → Report Targets**
2. Click **New**, select **Email** as the target type
3. Configure recipients and email body template
4. Connect one or more reports to the target

The email body template has access to `reports` (connected reports) and `target` (the target model):

```twig
Report generated for {{ target.name }}

{% for report in reports %}
- {{ report.name }}
{% endfor %}
```

## Console Commands

Run report targets from the CLI — useful for cron jobs or long-running reports:

```bash
# Run by handle
./craft reports/default/run-target weekly-summary

# Run by ID
./craft reports/default/run-target 5

# List all targets
./craft reports/default/list-targets
```

## Permissions

| Permission | Description |
|-----------|-------------|
| Run Reports | View and run reports |
| Manage Reports | Create, edit, and delete reports |
| Manage Export Targets | Create, edit, and manage report targets |

## Breaking Changes (v3.0.0)

- Requires Craft 5.5+ and PHP 8.2+
- `mobileGrade` method removed (if referenced in custom code)
- `FILTER_SANITIZE_STRING` replaced (PHP 8.2 removal)
- Widget `iconPath()` removed (Craft 5 change)

---

Brought to you by [Superbig](https://superbig.co)
