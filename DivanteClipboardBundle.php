<?php

namespace Divante\ClipboardBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class DivanteClipboardBundle extends AbstractPimcoreBundle
{
    public function getJsPaths()
    {
        return [
            '/bundles/DivanteClipboard/js/pimcore/startup.js'
        ];
    }
}
