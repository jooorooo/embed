<?php
/**
 * Adapter to provide information from Ebay
 */
namespace Embed\Adapters;

use Embed\Request;
use Embed\Providers;

class Ebay extends Webpage implements AdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public static function check(Request $request)
    {
        return $request->isValid() && $request->match([
            'https?://*.ebay.*',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->addProvider('ebay', new Providers\Ebay());
    }

    /**
     * {@inheritdoc}
     */
    public function getProviderName()
    {
        return 'Ebay';
    }
}
