<?php

namespace Gregoriohc\Kaleidoscope\Tests;

use Illuminate\Database\Capsule\Manager as DB;
use PHPUnit_Framework_TestCase;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->configureDatabase();
        $this->migrateUsersTable();
    }

    protected function configureDatabase()
    {
        $db = new DB();
        $db->addConnection([
            'driver'    => 'sqlite',
            'database'  => ':memory:',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);
        $db->bootEloquent();
        $db->setAsGlobal();
    }

    public function migrateUsersTable()
    {
        DB::schema()->create('users', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->timestamps();
        });
    }

    /**
     * Assert that an array has a given structure.
     *
     * @param array $structure
     * @param array $data
     *
     * @return $this
     */
    public function seeArrayStructure(array $structure, $data)
    {
        foreach ($structure as $key => $value) {
            if (is_array($value) && $key === '*') {
                $this->assertInternalType('array', $data);
                foreach ($data as $dataItem) {
                    $this->seeArrayStructure($structure['*'], $dataItem);
                }
            } elseif (is_array($value)) {
                $this->assertArrayHasKey($key, $data);
                $this->seeArrayStructure($structure[$key], $data[$key]);
            } else {
                $this->assertArrayHasKey($value, $data);
            }
        }

        return $this;
    }
}
