<?php

namespace App\Utopia\Repositories\Interfaces;

interface AbstractRepositoryInterface{

    public function findOrFail($id);

    public function paginate();

}
