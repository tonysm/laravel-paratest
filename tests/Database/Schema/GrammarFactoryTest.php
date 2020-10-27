<?php

namespace Tonysm\LaravelParatest\Database\Schema;

use PHPUnit\Framework\TestCase;

class GrammarFactoryTest extends TestCase
{
    /**
     * @test
     * @dataProvider validDriversAndInstances
     */
    public function makes_drivers(string $driver, string $expectedInstance)
    {
        $grammar = new GrammarFactory($driver);

        $this->assertInstanceOf($expectedInstance, $grammar->make($driver));
    }

    public function validDriversAndInstances()
    {
        return [
            'mysql driver' => ['mysql', Grammars\MySQL::class],
            'postgresql driver' => ['pgsql', Grammars\PgSQL::class],
        ];
    }

    /** @test */
    public function throws_exception_when_driver_not_implemented()
    {
        $this->expectException(\RuntimeException::class);
       (new GrammarFactory())->make('sqlite');
    }
}
