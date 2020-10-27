<?php

namespace Tonysm\LaravelParatest\Console;

use Tonysm\LaravelParatest\Database\{
    Connector,
    PDOConnector,
    DryRunConnector,
    Schema\Builder,
    Schema\GrammarFactory
};
use Illuminate\Console\Command;

class DbDropCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:drop
        {--dry-run}
        {--database= : The database name}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drops the database.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(GrammarFactory $grammars)
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->info('[DRY] Running in dry-run.');
        }

        $connection = config('database.default');
        $configs = config(sprintf('database.connections.%s', $connection));
        $configs['database'] = $this->option('database') ?: $configs['database'];

        $builder = new Builder(
            $this->makeConnector($configs, $dryRun),
            $grammars
        );

        if ($builder->dropDatabase($configs) === false) {
            $this->error('Could not drop the database.');
            return 1;
        }

        $this->info(sprintf('Database "%s" drop successfully.', $configs['database']));
    }

    private function makeConnector(array $configs, bool $dryRun = false): Connector
    {
        if ($dryRun === true) {
            return new DryRunConnector($this->output);
        }

        return PDOConnector::make($configs);
    }
}
