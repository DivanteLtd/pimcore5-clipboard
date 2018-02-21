<?php
/**
 * @category    pimcore5-clipboard
 * @date        20/02/2018
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2018 DIVANTE (http://divante.pl)
 */

declare(strict_types=1);

namespace Divante\ClipboardBundle\Model;

use Pimcore\Model\AbstractModel;

/**
 * Class Clipboard
 * @package Divante\ClipboardBundle
 */
class Clipboard extends AbstractModel
{
    /**
     * @var int|null
     */
    public $id;

    /**
     * @var int|null
     */
    public $userId;

    /**
     * @var int|null
     */
    public $objectId;

    /**
     * @param int $userId
     * @param int $objectId
     * @return Clipboard|null
     */
    public static function getByUniqueKey(int $userId, int $objectId)
    {
        try {
            $model = new self();
            $model->getDao()->getByUniqueKey($userId, $objectId);
        } catch (\Exception $ex) {
            $model = null;
        }

        return $model;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return int|null
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return int|null
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * @param int $objectId
     */
    public function setObjectId(int $objectId)
    {
        $this->objectId = $objectId;
    }

    /**
     * @return Clipboard\Dao
     */
    public function getDao(): Clipboard\Dao
    {
        return parent::getDao();
    }
}
