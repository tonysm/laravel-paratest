<?php

namespace Tonysm\LaravelParatest\Testing\Paratest;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Console\Kernel;
use ParaTest\Runners\PHPUnit\Options;
use ParaTest\Runners\PHPUnit\RunnerInterface;
use ParaTest\Runners\PHPUnit\WrapperRunner;
use Symfony\Component\Console\Output\OutputInterface;

class LaravelRunner implements RunnerInterface
{
    /**
     * @var WrapperRunner
     */
    private $innerRunner;
    /**
     * @var Options
     */
    private $options;

    public function __construct(Options $options, OutputInterface $output)
    {
        $this->options = $options;
        $this->innerRunner = new WrapperRunner($options, $output);
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

    private function swapTestingDatabase($app, $driver, $dbName): void
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

    public function run(): void
    {
        $this->innerRunner->run();
        $this->tearDownTestDatabases();
    }

    public function getExitCode(): int
    {
        return $this->innerRunner->getExitCode();
    }
}
