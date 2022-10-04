<?php
/**
 * Reports plugin for Craft CMS 3.x
 *
 * Write reports with Twig.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\reports\targets;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use superbig\reports\models\Report;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 *
 * @property string $webhookUrl The Slack webhook url to post to
 */
class SlackTarget extends ReportTarget
{
    public $webhookUrl;
    public $pretext;

    public static function displayName(): string
    {
        return \Craft::t('reports', 'Slack');
    }

    public function send(Report $report): bool
    {
        $client = new Client();

        try {
            $payload = [
                'text' => $report->name,
            ];
            $client->post($this->webhookUrl, [
                'json' => $payload,
            ]);
        } catch (BadResponseException $e) {
            return false;
        }

        return true;
    }

    public function formatMessage(Report $report)
    {
    }
}
