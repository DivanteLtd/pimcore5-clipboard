<?php
/**
 * @category    pimcore5-clipboard
 * @date        20/02/2018
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2018 DIVANTE (http://divante.pl)
 */

declare(strict_types=1);

namespace Divante\ClipboardBundle\Model\Clipboard;

use Divante\ClipboardBundle\Model\Clipboard;
use Pimcore\Model\Dao\AbstractDao;

/**
 * Class Dao
 * @package Divante\ClipboardBundle\Model\Clipboard
 */
class Dao extends AbstractDao
{
    const TABLE_EXPRESSION = '`bundle_divante_clipboard`';

    /**
     *
     */
    public function save()
    {
        $data = [
            'userId'   => $this->getModel()->getUserId(),
            'objectId' => $this->getModel()->getObjectId(),
        ];

        $types = [
            'id'       => \PDO::PARAM_INT,
            'userId'   => \PDO::PARAM_INT,
            'objectId' => \PDO::PARAM_INT,
        ];

        if (!$this->getModel()->getId()) {
            $this->db->insert(static::TABLE_EXPRESSION, $data, $types);
            $this->getModel()->setId((int) $this->db->lastInsertId());
        } else {
            $identifier = ['id' => $this->getModel()->getId()];
            $this->db->update(static::TABLE_EXPRESSION, $data, $identifier, $types);
        }
    }

    /**
     *
     */
    public function delete()
    {
        if ($this->getModel()->getId()) {
            $identifier = ['id' => $this->getModel()->getId()];
            $types      = ['id' => \PDO::PARAM_INT];
            $this->db->delete(static::TABLE_EXPRESSION, $identifier, $types);
        }
    }

    /**
     * @return Clipboard
     */
    protected function getModel(): Clipboard
    {
        return $this->model;
    }
}
