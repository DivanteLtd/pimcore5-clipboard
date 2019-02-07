<?php
/**
 * @category    pimcore5-clipboard
 * @date        22/02/2018
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2018 DIVANTE (http://divante.pl)
 */

namespace Divante\ClipboardBundle\Controller;

use Divante\ClipboardBundle\Service\ClipboardService;
use Pimcore\Bundle\AdminBundle\Controller\Admin\DataObject\DataObjectHelperController as AdminDataObjectHelperController;
use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DataObjectHelperController
 * @package Divante\ClipboardBundle\Controller
 * @Route("/admin/object-helper")
 */
class DataObjectHelperController extends AdminDataObjectHelperController
{
    /**
     * @param Request $request
     * @param ClipboardService $clipboardService
     * @return JsonResponse
     * @Route("/get-clipboard-batch-jobs")
     */
    public function getClipboardBatchJobsAction(Request $request, ClipboardService $clipboardService)
    {
        if ($request->get('language')) {
            $request->setLocale($request->get('language'));
        }

        $folder = DataObject::getById($request->get('folderId'));
        $class = DataObject\ClassDefinition::getById($request->get('classId'));

        $conditionFilters = "(o_path = ? OR o_path LIKE '"
                          . str_replace('//', '/', $folder->getRealFullPath() . '/')
                          . "%')";
        $conditionFilters = [$conditionFilters];

        if ($request->get('filter')) {
            $conditionFilters[] = DataObject\Service::getFilterCondition($request->get('filter'), $class);
        }
        if ($request->get('condition')) {
            $conditionFilters[] = ' (' . $request->get('condition') . ')';
        }

        $objectIds = $clipboardService->getObjectIds();
        if (empty($objectIds)) {
            $objectIds[] = 0;
        }

        $conditionFilters[] = 'o_id IN (' . implode(', ', $objectIds) . ')';

        $className = $class->getName();
        $listClass = '\\Pimcore\\Model\\DataObject\\' . ucfirst($className) . '\\Listing';
        $list = new $listClass();
        $list->setCondition(implode(' AND ', $conditionFilters), [$folder->getRealFullPath()]);
        $list->setOrder('ASC');
        $list->setOrderKey('o_id');

        if ($request->get('objecttype')) {
            $list->setObjectTypes([$request->get('objecttype')]);
        }

        $jobs = $list->loadIdList();

        return $this->adminJson(['success' => true, 'jobs' => $jobs]);
    }
}
