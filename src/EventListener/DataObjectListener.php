<?php
/**
 * @category    pimcore5-clipboard
 * @date        23/02/2018
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2018 DIVANTE (http://divante.pl)
 */

declare(strict_types=1);

namespace Divante\ClipboardBundle\EventListener;

use Divante\ClipboardBundle\Service\ClipboardService;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Event\DataObjectEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DataObjectListener
 * @package Divante\ClipboardBundle\EventListener
 */
class DataObjectListener implements EventSubscriberInterface
{
    /**
     * @var ClipboardService
     */
    protected $service;

    /**
     * DataObjectListener constructor.
     * @param ClipboardService $service
     */
    public function __construct(ClipboardService $service)
    {
        $this->service = $service;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            DataObjectEvents::POST_DELETE => 'onPostDelete',
        ];
    }

    /**
     * @param DataObjectEvent $event
     */
    public function onPostDelete(DataObjectEvent $event)
    {
        $object = $event->getObject();
        $this->service->deleteDirtyItems($object->getId());
    }
}
