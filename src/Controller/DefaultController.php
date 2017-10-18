<?php

namespace Divante\ClipboardBundle\Controller;

use Pimcore\Controller\FrontendController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends FrontendController
{
    /**
     * @Route("/divante_clipboard")
     */
    public function indexAction(Request $request)
    {
        return new Response('Hello world from divante_clipboard');
    }
}
