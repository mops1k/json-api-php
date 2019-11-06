<?php
declare(strict_types=1);

namespace JsonApi\Tests\Stubs\Serializables;

class Bike
{
    public $id;

    public $name = 'Bike';

    public function __construct($id)
    {
        $this->id = $id;
    }
}
