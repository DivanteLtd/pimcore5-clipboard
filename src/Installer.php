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
use Pimcore\Tool\Admin;

/**
 * Class Installer
 *
 * @package Divante\ClipboardBundle
 */
class Installer extends AbstractInstaller
{
    /**
     *
     * @throws InstallationException
     */
    public function install()
    {
        $sql = file_get_contents(__DIR__ . '/Resources/sql/install.sql');
        try {
            Db::getConnection()->query($sql);
        } catch (\Exception $ex) {
            new InstallationException('An error occurred while installing the bundle', 0, $ex);
        }
    }

    /**
     *
     * @throws InstallationException
     */
    public function uninstall()
    {
        $sql = file_get_contents(__DIR__ . '/Resources/sql/uninstall.sql');
        try {
            Db::getConnection()->query($sql);
        } catch (\Exception $ex) {
            new InstallationException('An error occurred while uninstalling the bundle', 0, $ex);
        }
    }

    /**
     * @return bool
     */
    public function isInstalled()
    {
        try {
            $stmt = Db::getConnection()->query("SHOW TABLES LIKE 'bundle_divante_clipboard'");
            $result = strcmp((string) $stmt->fetchColumn(), 'bundle_divante_clipboard') === 0;
        } catch (\Exception $ex) {
            $result = false;
        }
        return $result;
    }

    /**
     * @return bool
     */
    public function canBeInstalled()
    {
        return Admin::isExtJS6() && !$this->isInstalled();
    }

    /**
     * @return bool
     */
    public function canBeUninstalled()
    {
        return $this->isInstalled();
    }

    /**
     * @return bool
     */
    public function needsReloadAfterInstall()
    {
        return true;
    }
}