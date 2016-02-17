<?php

namespace Lib;

use Lib;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractMiddleware extends AbstractService
{
    public abstract function parse(Request $request);
}