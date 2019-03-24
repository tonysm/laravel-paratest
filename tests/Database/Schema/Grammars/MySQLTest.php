<?php

namespace Tonysm\DbCreateCommand\Database\Schema\Grammars;

use PHPUnit\Framework\TestCase;

class MySQLTest extends TestCase
{
    /** @test */
    public function implements_sql_interface()
    {
        $this->assertInstanceOf(SQL::class, new MySQL());
    }

    /** @test */
    public function compiles_create_database_sql()
    {
        $grammar = new MySQL();
        $options = [
            'database' => 'fakedb',
            'collation' => 'utf8-collaction',
            'charset' => 'utf8-charset',
        ];

        $this->assertEquals(
            'CREATE DATABASE IF NOT EXISTS fakedb CHARACTER SET utf8-charset COLLATE utf8-collaction;',
            $grammar->compileCreateDatabase($options)
        );
    }

    /** @test */
    public function compiles_drop_database()
    {
        $grammar = new MySQL();

        $this->assertEquals(
            'DROP DATABASE fakedb;',
            $grammar->compileDropDatabase('fakedb')
        );
    }
}
