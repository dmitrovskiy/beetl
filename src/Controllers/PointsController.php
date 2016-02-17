<?php

namespace Controllers;

use Lib\AbstractController;
use Models\Point\PointModel;
use Models\Point\PointNormalizer;
use Silex\Application;

class PointsController extends AbstractController
{
    /** @var PointModel */
    protected $pointModel;
    /** @var PointNormalizer */
    protected $pointNormalizer;

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $ctlInjection = $this->getInjectionName();

        $controllers->get('/', "$ctlInjection:readAll");
        $controllers->put('/{id}', "$ctlInjection:updateById");
        $controllers->post('/{id}/validation', "$ctlInjection:validateById");

        return $controllers;
    }

    protected function initialize()
    {
        parent::initialize();

        $this->pointModel = $this->app['Models\Point\PointModel'];
        $this->pointNormalizer = $this->app['Models\Point\PointNormalizer'];
    }

    public function readAll()
    {
        $points = $this->pointModel->read();
        $pointsArrayData = $this->pointNormalizer->normalize($points);

        return $this->response($pointsArrayData);
    }

    public function updateById($id)
    {
        $data = $this->app['request']->request->all();

        $point = $this->pointModel->updateOne(['_id' => new \MongoId($id)], $data);
        $pointArrayData = $this->pointNormalizer->normalize($point);

        return $this->response($pointArrayData, 202);
    }

    public function validateById($id)
    {
        $password = $this->app['request']->request->get('password');

        $permission = $this->pointModel->validateAccess(
            new \MongoId($id),
            $password
        );

        return $this->response(['permission' => $permission]);
    }
}