<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractJsonController extends AbstractController
{
    protected function initJsonParameters(Request $request): void
    {
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
        $request->query = new InputBag($parametersAsArray);
    }
}