<?php
/**
 * @category    pimcore5-clipboard
 * @date        20/02/2018
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2018 DIVANTE (http://divante.pl)
 */

declare(strict_types=1);

namespace Divante\ClipboardBundle\Service;

use Divante\ClipboardBundle\Model\Clipboard;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\Element\ValidationException;
use Pimcore\Model\User;
use Pimcore\Tool\Admin;

/**
 * Class ClipboardService
 * @package Divante\ClipboardBundle\Service
 */
class ClipboardService
{
    /**
     * @param int $objectId
     * @throws ValidationException
     */
    public function addObjectToClipboard(int $objectId)
    {
        $user   = $this->getCurrentUser();
        $object = $this->getObjectById($objectId);

        $model = Clipboard::getByUniqueKey($user->getId(), $object->getId());
        if ($model instanceof Clipboard) {
            $message = sprintf("Object with ID %d has already been added to the clipboard", $objectId);
            throw new ValidationException($message);
        }

        $model = new Clipboard();
        $model->setUserId($user->getId());
        $model->setObjectId($object->getId());
        $model->save();
    }

    /**
     * @param int $objectId
     * @throws ValidationException
     */
    public function deleteObjectFromClipboard(int $objectId)
    {
        $user   = $this->getCurrentUser();
        $object = $this->getObjectById($objectId);

        $model = Clipboard::getByUniqueKey($user->getId(), $object->getId());
        if (!$model instanceof Clipboard) {
            $message = sprintf('No object found with ID %d in the clipboard', $objectId);
            throw new ValidationException($message);
        }

        $model->delete();
    }

    /**
     * @param int $objectId
     */
    public function deleteDirtyItems(int $objectId)
    {
        $sql = 'DELETE FROM bundle_divante_clipboard WHERE objectId = ?';
        \Pimcore\Db::get()->query($sql, [$objectId]);
    }

    /**
     * @return array
     */
    public function getClasses(): array
    {
        $sql = 'SELECT o_classId '
             . 'FROM bundle_divante_clipboard '
             . 'INNER JOIN objects ON objectId = o_id '
             . 'WHERE userId = ? '
             . 'AND o_type = ? '
             . 'GROUP BY o_classId';

        $classIds = \Pimcore\Db::get()->fetchCol($sql, [$this->getCurrentUser()->getId(), 'object']);

        $classes = [];
        foreach ($classIds as $classId) {
            $classes[] = ClassDefinition::getById($classId);
        }

        return $classes;
    }

    /**
     * @return array
     */
    public function getObjectIds(): array
    {
        $sql = 'SELECT objectId FROM bundle_divante_clipboard WHERE userId = ?';
        return \Pimcore\Db::get()->fetchCol($sql, [$this->getCurrentUser()->getId()]);
    }

    /**
     * @return User
     */
    protected function getCurrentUser(): User
    {
        return Admin::getCurrentUser();
    }

    /**
     * @param int $id
     * @return AbstractObject
     * @throws ValidationException
     */
    protected function getObjectById(int $id): AbstractObject
    {
        $object = AbstractObject::getById($id);
        if (!$object instanceof AbstractObject) {
            $message = sprintf('No object found with ID %d', $id);
            throw new ValidationException($message);
        }

        return $object;
    }
}
