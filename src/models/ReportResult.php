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

use superbig\reports\Reports;

use Craft;
use craft\base\Model;

/**
 * @author    Superbig
 * @package   Reports
 * @since     1.0.0
 */
class ReportResult extends Model
{
    // Public Properties
    // =========================================================================

    /**  @var array */
    public $header = [];

    /**  @var array */
    public $content = [];

    /**  @var array */
    public $rows = [];

    /**  @var string */
    public $filename = 'output';

    /**  @var array */
    public $fields = [];


    // Public Methods
    // =========================================================================

    public function init()
    {
        if (!empty($this->rows)) {
            $this->content = $this->rows;
        }
    }

    /**
     * @param array $content
     *
     * @return $this
     */
    public function append(array $content = []): self
    {
        if (isset($content[0]) && !\is_array($content[0])) {
            $content = [$content];
        }
        $this->content = array_merge($this->content, $content);

        return $this;
    }

    public function setHeader(array $headers = [])
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

    /**
     * @param $content
     *
     * @return $this
     */
    public function setContent($content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getConfig()
    {
        return [
            'header' => $this->header,
            'rows'   => $this->content,
        ];
    }

    public function getFilename($ext = null): string
    {
        $filename = pathinfo(filter_var($this->filename, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW), PATHINFO_FILENAME);

        return "$filename.$ext";
    }

    /**
     * @param null $filename
     *
     * @return $this
     */
    public function setFilename($filename = null): self
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }
}
