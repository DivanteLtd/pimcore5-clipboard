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
     */
    public function addObjectToClipboard(int $objectId)
    {
        $user   = $this->getCurrentUser();
        $object = $this->getObjectById($objectId);

        $model = new Clipboard();
        $model->setUserId($user->getId());
        $model->setObjectId($object->getId());

        $model->save();
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
     * @throws \UnexpectedValueException
     */
    protected function getObjectById(int $id): AbstractObject
    {
        $object = AbstractObject::getById($id);
        if (!$object instanceof AbstractObject) {
            $message = sprintf('No object found with ID %d', $id);
            throw new \UnexpectedValueException($message);
        }

        return $object;
    }
}
