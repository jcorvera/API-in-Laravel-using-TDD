<?php

namespace App\Utopia\Repositories\Eloquent;

use App\Utopia\Repositories\Interfaces\AbstractRepositoryInterface;

class AbstractRepository implements AbstractRepositoryInterface{

    protected $model;

    public function __construct(String $model)
    {
        $this->model = "App\\$model";
    }

    public function findOrFail($id){
        return $this->model::findOrFail($id);
    }

    public function paginate(){
        return $this->model::paginate();
    }

}
