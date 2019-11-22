<?php
/**
 * @category    pimcore5-clipboard
 * @date        25/10/2017 13:41
 * @author      Monika Litwin <mlitwin@divante.pl>
 * @copyright   Copyright (c) 2017 Divante Ltd. (https://divante.co)
 */

namespace Divante\ClipboardBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

/**
 * Class DivanteClipboardBundle
 *
 * @package Divante\ClipboardBundle
 */
class DivanteClipboardBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    /**
     * {@inheritdoc}
     */
    protected function getComposerPackageName()
    {
        return 'divante-ltd/pimcore5-clipboard';
    }

    /**
     * {@inheritdoc}
     */
    public function getInstaller()
    {
        return $this->container->get(Installer::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getJsPaths()
    {
        return [
            '/bundles/divanteclipboard/js/pimcore/startup.js',
            '/bundles/divanteclipboard/js/pimcore/clipboard.js',
            '/bundles/divanteclipboard/js/pimcore/search.js',
            '/bundles/divanteclipboard/js/pimcore/helpers.js',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getNiceName()
    {
        return "Pimcore 5 Clipboard";
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return "Shelve objects and perform actions only on these separated objects";
    }
}
