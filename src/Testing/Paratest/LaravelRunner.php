<?php

namespace Tonysm\LaravelParatest\Testing\Paratest;

use ParaTest\Runners\PHPUnit\Options;
use ParaTest\Runners\PHPUnit\Runner;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use ParaTest\Runners\PHPUnit\BaseRunner;
use Illuminate\Contracts\Console\Kernel;
use Symfony\Component\Console\Output\OutputInterface;

class LaravelRunner extends BaseRunner
{
    /**
     * @var \ParaTest\Runners\PHPUnit\Runner
     */
    protected $innerRunner;

    public function __construct(Options $options, OutputInterface $output)
    {
        parent::__construct($options, $output);

        $this->innerRunner = new Runner($options, $output);
    }

    /**
     * @throws \Exception
     */
    public function doRun(): void
    {
        $this->tearDownTestDatabases();
    }

    public function tearDownTestDatabases()
    {
        $app = $this->createApp();

        $driver = $app['config']->get('database.default');
        $dbName = $app['config']->get("database.connections.{$driver}.database");

        for ($i = 1; $i <= $this->options->processes(); ++$i) {
            $this->swapTestingDatabase($app, $driver, sprintf('%s_test_%s', $dbName, $i));
            Artisan::call('db:drop');
        }
    }

    protected function swapTestingDatabase($app, $driver, $dbName): void
    {
        // Paratest gives each process a unique TEST_TOKEN env variable.
        // When that's not set, we can default to 1 because it's
        // probably running on PHPUnit instead.
        $app['config']->set([
            "database.connections.{$driver}.database" => $dbName,
        ]);
    }

    private function createApp(): Application
    {
        $app = require getcwd().'/bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    protected function beforeLoadChecks(): void
    {
        // TODO: Implement beforeLoadChecks() method.
    }
}
