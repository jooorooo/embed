<?php
namespace Embed\Providers;

use Embed\Request;
use Embed\Bag;

/**
 * Abstract class used by all providers
 */
abstract class Provider
{
    public $bag;

    protected $request;
    protected $config = [];

    /**
     * {@inheritdoc}
     */
    public function init(Request $request, array $config = null)
    {
        $this->bag = new Bag();
        $this->request = $request;
        $this->bag->set('request', $this->request);
        $this->bag->set('request_url', $request->getResolver()->getUrl());

        if ($config) {
            $this->config = array_replace($this->config, $config);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorName()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorUrl()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderIconsUrls()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderName()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderUrl()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getImagesUrls()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getWidth()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getHeight()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishedTime()
    {
    }

    /**
     * resolveFullPath
     *
     * @see    http://ca3.php.net/manual/en/function.realpath.php#86384
     * @access protected
     * @param  string $addr
     * @param  string $base
     * @return string
     */
    protected static function _resolveFullPath($addr, $base)
    {
        // empty address provided
        if (empty($addr)) {
            return $base;
        }

        // parse address; if scheme found, doesn't need to be resolved
        $parsed = parse_url($addr);
        if(array_key_exists('scheme', $parsed)) {
            return $addr;
        }

        // parse base passed in (will always be a full url)
        $parsed = parse_url($base);

        // protocol specific
        if (mb_substr($addr, 0, 2) === '//') {
            return ($parsed['scheme']) . '://' . mb_substr($addr, 2);
        }
        // otherwise if the address should go to the top of the tree
        elseif ($addr{0} === '/') {
            return ($parsed['scheme']) . '://' . ($parsed['host']) .
            ($addr);
        }

        // if the address doesn't contain any sub-directory calls
        if (!strstr($addr, '../')) {
            return ($base) . ($addr);
        }

        // set-up sub-directory pieces for traversing/replacing
        $pieces['addr'] = explode('../', $addr);
        $pieces['base'] = explode('/', $parsed['path']);
        array_pop($pieces['base']);
        $count = count($pieces['addr']) - 1;

        // array of respective sub-directory replacements (from base)
        $replacements = array_slice($pieces['base'], 0, 0 - $count);
        $replacements = array_filter($replacements);

        // add last non-empty sub-directory as tail
        $tail = array_pop($pieces['addr']);
        if (!empty($tail)) {
            $replacements[] = $tail;
        }

        // return sub-directory traversed address
        return ($parsed['scheme']) . '://' . ($parsed['host']) .
        '/' . implode('/', $replacements);
    }
}
