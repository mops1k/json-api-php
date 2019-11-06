<?php
declare(strict_types=1);

namespace JsonApi\Tests\Stubs\Serializables;

class Car
{
    public $id;

    public $name = 'Car';

    public function __construct($id)
    {
        $this->id = $id;
    }
}
