<?php
/**
 * @category    pimcore5-clipboard
 * @date        22/02/2018
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2018 DIVANTE (http://divante.pl)
 */

declare(strict_types=1);

namespace Divante\ClipboardBundle\EventListener;

use Divante\ClipboardBundle\Service\ClipboardService;
use Pimcore\Event\AdminEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class AdminListener
 * @package Divante\ClipboardBundle\EventListener
 */
class AdminListener implements EventSubscriberInterface
{
    /**
     * @var ClipboardService
     */
    protected $service;

    /**
     * AdminListener constructor.
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
            AdminEvents::OBJECT_LIST_BEFORE_LIST_LOAD => 'onBeforeListLoad',
        ];
    }

    /**
     * @param GenericEvent $event
     */
    public function onBeforeListLoad(GenericEvent $event)
    {
        $context = $event->getArgument('context');
        if (empty($context['clipboard'])) {
            return;
        }

        $objectIds = $this->service->getObjectIds();
        if (empty($objectIds)) {
            $objectIds[] = 0;
        }

        /** @var \Pimcore\Model\Listing\AbstractListing $list */
        $list = $event->getArgument('list');
        $cond = $list->getCondition() . ' AND o_id IN (' . implode(', ', $objectIds) . ')';
        $list->setCondition($cond);
    }
}
