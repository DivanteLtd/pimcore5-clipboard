<?php

namespace Divante\ClipboardBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class DivanteClipboardBundle extends AbstractPimcoreBundle
{
    public function getInstaller()
    {
        return new Installer();
    }

    public function getJsPaths()
    {
        return [
            '/bundles/divanteclipboard/js/pimcore/startup.js'
        ];
    }
}
