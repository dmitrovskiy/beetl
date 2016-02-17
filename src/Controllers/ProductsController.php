<?php

namespace Controllers;

use Lib\AbstractController;
use Models\Point\PointModel;
use Models\Product\ProductModel;
use Models\Product\ProductNormalizer;
use Silex\Application;

class ProductsController extends AbstractController
{
    /** @var ProductModel */
    protected $productModel;
    /** @var  ProductNormalizer */
    protected $productNormalizer;
    /** @var PointModel */
    protected $pointModel;

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $ctlInjection = $this->getInjectionName();

        $controllers->get('/points/{id}/products', "$ctlInjection:readAllByPointId");

        $controllers->get('/products/{id}', "$ctlInjection:readById");
        $controllers->put('/products/{id}', "$ctlInjection:updateById");
        $controllers->put('/products/{id}', "$ctlInjection:deleteById");

        $controllers->post('/products/', "$ctlInjection:create");

        return $controllers;
    }

    protected function initialize()
    {
        parent::initialize();

        $this->productModel = $this->app['Models\Product\ProductModel'];
        $this->productNormalizer = $this->app['Models\Product\ProductNormalizer'];
        $this->pointModel = $this->app['Models\Point\PointModel'];
    }

    public function readAllByPointId($id)
    {
        $id = new \MongoId($id);

        $products = $this->productModel->read(['point.$id' => $id]);
        $productsArrayData = $this->productNormalizer->normalize($products);

        return $this->response($productsArrayData);
    }

    public function readById($id)
    {
        $id = new \MongoId($id);

        $product = $this->productModel->readOne(['_id' => $id]);
        $productArrayData = $this->productNormalizer->normalize($product);

        return $this->response($productArrayData);
    }

    public function updateById($id)
    {
        $id = new \MongoId($id);
        $data = $this->app['request']->request->all();

        $product = $this->productModel->update(['_id' => $id], $data);
        $productArrayData = $this->productNormalizer->normalize($product);

        return $this->response($productArrayData, 202);
    }

    public function deleteById($id)
    {
        $id = new \MongoId($id);

        $product = $this->productModel->deleteOne(['_id' => $id]);
        $productArrayData = $this->productNormalizer->normalize($product);

        return $this->response($productArrayData, 200);
    }

    public function create()
    {
        $data = $this->app['request']->request->all();

        if (isset($data['point'])) {
            $data['point'] = $this->pointModel->readOne(
                ['_id' => new \MongoId($data['point'])]
            );
        }

        $product = $this->productModel->create($data);
        $productArrayData = $this->productNormalizer->normalize($product);

        return $this->response($productArrayData);
    }
}