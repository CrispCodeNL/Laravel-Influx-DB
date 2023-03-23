<?php

namespace CrispCode\LaravelInfluxDB\Tests\Mocks;

use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    protected $primaryKey = 'custom-column';
}
