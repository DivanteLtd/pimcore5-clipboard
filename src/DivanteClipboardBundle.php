<?php
/**
 * @category    pimcore5-clipboard
 * @date        25/10/2017 13:41
 * @author      Monika Litwin <mlitwin@divante.pl>
 * @copyright   Copyright (c) 2017 Divante Ltd. (https://divante.co)
 */

namespace Divante\ClipboardBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

/**
 * Class DivanteClipboardBundle
 *
 * @package Divante\ClipboardBundle
 */
class DivanteClipboardBundle extends AbstractPimcoreBundle
{
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
            '/bundles/divanteclipboard/js/pimcore/startup.js'
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

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return  "0.1.0";
    }
}