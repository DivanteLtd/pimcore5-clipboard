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
    protected $name = "Pimcore 5 Clipboard";

    protected $description = "Shelve objects and perform actions only on these separated objects";

    protected $version = "0.1.0";

    /**
     * @return Installer
     */
    public function getInstaller()
    {
        return $this->container->get(Installer::class);
    }

    /**
     * @return array
     */
    public function getJsPaths()
    {
        return [
            '/bundles/divanteclipboard/js/pimcore/startup.js'
        ];
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function getVersion()
    {
        return  $this->version;
    }
}
