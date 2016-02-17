<?php

namespace Middleware\Common;

use Exception\HttpException\BadRequestHttpException;
use Lib\AbstractMiddleware;
use Symfony\Component\HttpFoundation\Request;

class JsonBodyParser extends AbstractMiddleware
{
    public function parse(Request $request)
    {
        if (false !== strpos(
                $request->headers->get('Content-Type'),
                'json'
            )
            && $request->getContent() != ""
            && $request->getContent() != "null"
        ) {
            $data = json_decode($request->getContent(), true);

            if ($data === null) {
                throw new BadRequestHttpException(
                    "Invalid JSON format"
                );
            }
            $request->request->replace(is_array($data) ? $data : []);
        }
    }
}