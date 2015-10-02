<?php
namespace Embed\Providers;

use Embed\Url;
use Embed\Bag;
use Embed\Utils;

/**
 * Generic html provider.
 *
 * Load the html data of an url and store it
 */
class Ebay extends Html
{

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (!($html = $this->request->getHtmlContent())) {
            return false;
        }

        self::extractFromLink($html, $this->bag);
        self::extractFromMeta($html, $this->bag);

        $main = self::getMainElement($html);

        self::extractImages($main, $this->bag, $this->request->getDomain());

        //Title
        $title = $html->getElementsByTagName('title');

        if ($title->length) {
            $this->bag->set('title', $title->item(0)->nodeValue);
        }
    }

    /**
     * Extract <img> elements
     *
     * @param \DOMElement $html
     * @param Bag         $bag
     * @param null|string $domain
     */
    protected static function extractImages(\DOMElement $html, Bag $bag, $domain = null)
    {
        foreach ($html->getElementsByTagName('img') as $img) {
            self::addByAttribute($img, 'src', $html, $bag, $domain);
            self::addByAttribute($img, 'data-src', $html, $bag, $domain);
        }
    }

    private static function addByAttribute($img, $attribute, \DOMElement $html, Bag $bag, $domain = null) {
        $src = $img->hasAttribute($attribute);
        if ($src) {
            $src = new Url($img->getAttribute($attribute));

            //Is src relative?
            if (!$src->getDomain()) {
                $bag->add('images', ['url' => $src->getUrl(), 'alt' => self::_getAltTag($img), 'href' => self::extractA($img)]);
                return;
            }

            //Avoid external images or in external links
            if ($domain !== null) {
                if (!preg_match('~(ebayimg)~i', $src->getDomain())) {
                    return;
                }

                $bag->add('images', ['url' => $src->getUrl(), 'alt' => self::_getAltTag($img), 'href' => self::extractA($img)]);
            }
        }
    }

    private static function _getAltTag(\DOMElement $img) {
        if($img->hasAttribute('alt') && $img->getAttribute('alt'))
            return str_replace('-', ' ', $img->getAttribute('alt'));
        $next = $img->parentNode->getElementsByTagName('span');
        for($i=0; $i<$next->length; $i++) {
            $item = $next->item($i);
            if(in_array($item->getAttribute('class'), ['hide-text']))
                return trim($next->item($i)->nodeValue);
        }
        return '';

    }
}
