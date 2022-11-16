<?php
/**
 * Reports plugin for Craft CMS 3.x
 *
 * Write reports with Twig.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2019 Superbig
 */

namespace superbig\reports\models;

use craft\base\Model;

use superbig\reports\Reports;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class ReportResult extends Model
{
    public array $header = [];
    public array $content = [];
    public array $rows = [];
    public string $filename = 'output';
    public array $fields = [];

    public function init(): void
    {
        if (!empty($this->rows)) {
            $this->content = $this->rows;
        }
    }

    public function append(array $content = []): static
    {
        if (isset($content[0]) && !\is_array($content[0])) {
            $content = [$content];
        }
        $this->content = array_merge($this->content, $content);

        return $this;
    }

    public function setHeader(array $headers = []): static
    {
        $this->header = $headers;

        return $this;
    }

    public function getHeader(): array
    {
        return $this->header;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function setContent(array $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getConfig()
    {
        return [
            'header' => $this->header,
            'rows' => $this->content,
        ];
    }

    public function getFilename($ext = null): string
    {
        $filename = pathinfo(filter_var($this->filename, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW), PATHINFO_FILENAME);

        return "{$filename}{$ext}";
    }

    public function setFilename(string $filename = null): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function rules(): array
    {
        return [];
    }
}
