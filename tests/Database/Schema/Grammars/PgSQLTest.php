<?php

namespace Tonysm\LaravelParatest\Database\Schema\Grammars;

use PHPUnit\Framework\TestCase;

class PgSQLTest extends TestCase
{
    /** @test */
    public function implements_sql_interface()
    {
        $this->assertInstanceOf(SQL::class, new PgSQL());
    }

    /** @test */
    public function compiles_create_database_sql()
    {
        $grammar = new PgSQL();
        $options = [
            'database' => 'fakedb',
            'collation' => 'utf8-collaction',
            'charset' => 'utf8-charset',
        ];

        $this->assertEquals(
            'CREATE DATABASE fakedb ENCODING \'utf8-charset\' LC_COLLATE \'utf8-collaction\';',
            $grammar->compileCreateDatabase($options)
        );
    }

    /** @test */
    public function compiles_drop_database()
    {
        $grammar = new PgSQL();

        $this->assertEquals(
            'DROP DATABASE IF EXISTS fakedb;',
            $grammar->compileDropDatabase('fakedb')
        );
    }
}
