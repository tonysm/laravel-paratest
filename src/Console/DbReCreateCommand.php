<?php

namespace Tonysm\DbCreateCommand\Console;

use Tonysm\DbCreateCommand\Database\{
    Connector,
    PDOConnector,
    DryRunConnector,
    Schema\Builder,
    Schema\GrammarFactory
};
use Illuminate\Console\Command;

class DbReCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:recreate 
        {--dry-run}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-creates the database.';

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

        $builder = new Builder(
            $this->makeConnector($configs, $dryRun),
            $grammars
        );

        $builder->recreateDatabase($configs);

        $this->info(sprintf('Database "%s" re-created successfully.', $configs['database']));
    }

    private function makeConnector(array $configs, bool $dryRun = false): Connector
    {
        if ($dryRun === true) {
            return new DryRunConnector($this->output);
        }

        return PDOConnector::make($configs);
    }
}
