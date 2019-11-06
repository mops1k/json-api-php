<?php
declare(strict_types=1);

namespace JsonApi\Tests\Stubs\Serializables;

class Garage
{
    public $id;

    public $vehicles = [];

    public function __construct($id, array $vehicles = [])
    {
        $this->id = $id;
        $this->vehicles = $vehicles;
    }
}
