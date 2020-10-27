<?php

namespace Tonysm\LaravelParatest\Database;

use PDO;
use Mockery;
use PHPUnit\Framework\TestCase;

class PDOConnectorTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /** @test */
    public function executes_sql_statement()
    {
        $sql = "FAKE SQL";
        $pdo = Mockery::mock(PDO::class);

        $pdo->shouldReceive()
            ->exec($sql)
            ->once()
            ->andReturn(true);

        $connector = new PDOConnector($pdo);

        $this->assertTrue($connector->exec($sql));
    }
}
