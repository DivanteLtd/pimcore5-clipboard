<?php
/**
 * @category    pimcore5-clipboard
 * @date        22/02/2018
 * @author      Korneliusz Kirsz <kkirsz@divante.pl>
 * @copyright   Copyright (c) 2018 DIVANTE (http://divante.pl)
 */

namespace Divante\ClipboardBundle\Controller;

use Divante\ClipboardBundle\Service\ClipboardService;
use Pimcore\Bundle\AdminBundle\Controller\Admin\DataObjectController as AdminDataObjectController;
use Pimcore\Logger;
use Pimcore\Model\DataObject;
use Pimcore\Model\Element;
use Pimcore\Tool;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DataObjectController
 * @package Divante\ClipboardBundle\Controller
 * @Route("/admin/object")
 */
class DataObjectController extends AdminDataObjectController
{
    /**
     * @param Request $request
     * @param ClipboardService $clipboardService
     * @return JsonResponse
     * @Route("/get-clipboard")
     */
    public function getClipboardAction(Request $request, ClipboardService $clipboardService)
    {
        $object = DataObject::getById(intval($request->get('id')));
        if ($object->isAllowed('view')) {
            $objectData = [];

            $objectData['general'] = [];
            $objectData['idPath'] = Element\Service::getIdPath($object);
            $allowedKeys = ['o_published', 'o_key', 'o_id', 'o_type', 'o_path', 'o_modificationDate',
                'o_creationDate', 'o_userOwner', 'o_userModification'];
            foreach (get_object_vars($object) as $key => $value) {
                if (strstr($key, 'o_') && in_array($key, $allowedKeys)) {
                    $objectData['general'][$key] = $value;
                }
            }
            $objectData['general']['fullpath'] = $object->getRealFullPath();

            $objectData['general']['o_locked'] = $object->isLocked();

            $objectData['properties'] = Element\Service::minimizePropertiesForEditmode($object->getProperties());
            $objectData['userPermissions'] = $object->getUserPermissions();
            $objectData['classes'] = $this->prepareChildClasses($clipboardService->getClasses());

            // grid-config
            $configFile = PIMCORE_CONFIGURATION_DIRECTORY . '/object/grid/'
                        . $object->getId() . '-user_' . $this->getAdminUser()->getId() . '.psf';
            if (is_file($configFile)) {
                $gridConfig = Tool\Serialize::unserialize(file_get_contents($configFile));
                if ($gridConfig) {
                    $selectedClassId = $gridConfig['classId'];

                    foreach ($objectData['classes'] as $class) {
                        if ($class['id'] == $selectedClassId) {
                            $objectData['selectedClass'] = $selectedClassId;
                            break;
                        }
                    }
                }
            }

            return $this->adminJson($objectData);
        } else {
            Logger::debug('prevented getting folder id [ ' . $object->getId() . ' ] because of missing permissions');

            return $this->adminJson(['success' => false, 'message' => 'missing_permission']);
        }
    }
}
