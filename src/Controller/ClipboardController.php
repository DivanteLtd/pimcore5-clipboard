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
use Pimcore\Log\ApplicationLogger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ClipboardController
 * @package Divante\ClipboardBundle\Controller
 * @Route("/admin/clipboard")
 */
class ClipboardController extends AdminController
{
    /**
     * @var ApplicationLogger
     */
    protected $logger;

    /**
     * ClipboardController constructor.
     * @param ApplicationLogger $logger
     */
    public function __construct(ApplicationLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     * @param ClipboardService $service
     * @return JsonResponse
     * @Method({"POST"})
     * @Route("/add-object")
     */
    public function addObjectAction(Request $request, ClipboardService $service): JsonResponse
    {
        $objectId = (int) $request->get('objectId');

        try {
            $service->addObjectToClipboard($objectId);
        } catch (\Exception $ex) {
            $this->logger->error($ex->getMessage());
            throw $ex;
        }

        return $this->adminJson(['success' => true]);
    }
}
