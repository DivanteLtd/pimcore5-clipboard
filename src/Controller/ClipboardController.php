<?php
/**
 * @category    pimcore5-clipboard
 * @date        20/02/2018
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2018 DIVANTE (http://divante.pl)
 */

declare(strict_types=1);

namespace Divante\ClipboardBundle\Controller;

use Divante\ClipboardBundle\Service\ClipboardService;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ClipboardController
 * @package Divante\ClipboardBundle\Controller
 */
class ClipboardController extends AdminController
{
    public function addAction(Request $request, ClipboardService $service): JsonResponse
    {
        $objectId = (int) $request->get('objectId');

        try {
            $service->addObjectToClipboard($objectId);
        } catch (\Exception $ex) {

        }
    }
}
