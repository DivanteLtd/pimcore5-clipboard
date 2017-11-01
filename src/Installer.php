<?php
/**
 * @category    pimcore5-clipboard
 * @date        25/10/2017 13:41
 * @author      Monika Litwin <mlitwin@divante.pl>
 * @copyright   Copyright (c) 2017 Divante Ltd. (https://divante.co)
 */

namespace Divante\ClipboardBundle;

use Pimcore\Db;
use Pimcore\Extension\Bundle\Installer\AbstractInstaller;
use Pimcore\Extension\Bundle\Installer\Exception\InstallationException;
use Pimcore\Extension\Bundle\Installer\OutputWriterInterface;
use Pimcore\Tool\Admin;

/**
 * Class Installer
 *
 * @package Divante\ClipboardBundle
 */
class Installer extends AbstractInstaller
{

    /**
     * @var Db\Connection
     */
    private $db;

    /**
     * @var string
     */
    private $sqlTableCreate = 'CREATE TABLE `bundle_divante_clipboard` (
                              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                              `userId` int(11) unsigned NOT NULL,
                              `objectId` int(11) unsigned NOT NULL,
                              PRIMARY KEY (`id`)
                              ) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;';
    /**
     * Installer constructor.
     *
     * @param OutputWriterInterface|null $outputWriter
     */
    public function __construct(OutputWriterInterface $outputWriter = null)
    {
        parent::__construct($outputWriter);

        $this->db = Db::get();
    }

    /**
     * @inheritDoc
     */
    public function install()
    {
        try {
            $this->db->query($this->sqlTableCreate);
        } catch (\Exception $ex) {
            throw new InstallationException('An error occurred while installing the bundle', 0, $ex);
        }
    }

    /**
     * @inheritDoc
     */
    public function uninstall()
    {
        try {
            $this->db->query('DROP TABLE IF EXISTS `bundle_divante_clipboard`');
        } catch (\Exception $ex) {
            throw new InstallationException('An error occurred while uninstalling the bundle', 0, $ex);
        }
    }

    /**
     * @inheritDoc
     */
    public function isInstalled()
    {
        try {
            $stmt = $this->db->query("SHOW TABLES LIKE 'bundle_divante_clipboard'");
            $result = strcmp((string) $stmt->fetchColumn(), 'bundle_divante_clipboard') === 0;
        } catch (\Exception $ex) {
            $result = false;
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function canBeInstalled()
    {
        return !$this->isInstalled();
    }

    /**
     * @inheritDoc
     */
    public function canBeUninstalled()
    {
        return $this->isInstalled();
    }

    /**
     * @inheritDoc
     */
    public function needsReloadAfterInstall()
    {
        return true;
    }
}