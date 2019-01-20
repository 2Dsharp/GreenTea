<?php
/**
 * Created by PhpStorm.
 * User: dedipyaman
 * Date: 1/20/19
 * Time: 1:59 PM
 */

namespace Greentea\Core;


abstract class Controller
{
    protected function getService(ServiceFactory $factory) : WriteService
    {
        return $factory->getWriteService();
    }
}