<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function create(string $model, array $attributes=[], bool $resource = true)
    {
        $resourceModel = factory("App\\$model")->create($attributes);

        if(!$resource)
            return $resourceModel;
        else
            $resourceClass = "App\\Http\\Resources\\Product";

        return new $resourceClass($resourceModel);
    }
}
