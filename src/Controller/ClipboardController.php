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
use Pimcore\Model\Element\ValidationException;
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

            if ($ex instanceof ValidationException) {
                return $this->adminJson([
                    'success' => false,
                    'type'    => 'ValidationException',
                    'message' => $ex->getMessage(),
                    'stack'   => $this->createDetailedInfo($ex),
                    'code'    => $ex->getCode()
                ]);
            }

            throw $ex;
        }

        return $this->adminJson(['success' => true]);
    }

    /**
     * @param Request $request
     * @param ClipboardService $service
     * @return JsonResponse
     * @Method({"POST"})
     * @Route("/delete-object")
     */
    public function deleteObjectAction(Request $request, ClipboardService $service): JsonResponse
    {
        $objectId = (int) $request->get('objectId');

        try {
            $service->deleteObjectFromClipboard($objectId);
        } catch (\Exception $ex) {
            $this->logger->error($ex->getMessage());

            if ($ex instanceof ValidationException) {
                return $this->adminJson([
                    'success' => false,
                    'type'    => 'ValidationException',
                    'message' => $ex->getMessage(),
                    'stack'   => $this->createDetailedInfo($ex),
                    'code'    => $ex->getCode()
                ]);
            }

            throw $ex;
        }

        return $this->adminJson(['success' => true]);
    }

    /**
     * @param ValidationException $ex
     * @return string
     */
    protected function createDetailedInfo(ValidationException $ex): string
    {
        $detailedInfo = '';

        $detailedInfo .= '<b>Message:</b><br>';
        $detailedInfo .= $ex->getMessage();

        $detailedInfo .= '<br><br><b>Trace:</b> ' . $ex->getTraceAsString();
        if ($ex->getPrevious()) {
            $detailedInfo .= '<br><br><b>Previous Message:</b><br>';
            $detailedInfo .= $ex->getPrevious()->getMessage();
            $detailedInfo .= '<br><br><b>Previous Trace:</b><br>' . $ex->getPrevious()->getTraceAsString();
        }

        return $detailedInfo;
    }
}
