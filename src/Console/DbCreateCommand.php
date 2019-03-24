<?php

namespace Tonysm\Console;

use Tonysm\DbCreateCommand\Database\{
    Connector,
    PDOConnector,
    DryRunConnector,
    Schema\GrammarFactory
};
use Illuminate\Console\Command;

class DbCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create 
        {--dry-run}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates the database.';

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
    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run') ?? false;

        if ($dryRun) {
            $this->info('[DRY] Running in dry-run.');
        }

        $connection = config('database.default');
        $configs = config(sprintf('database.connections.%s', $connection));

        $connector = $this->createConnection($configs);
        dd($connector);
        $sql = $this->compileCreateStatement($configs);

        if ($connector->exec($sql) === false) {
            $this->error('Could not create the database.');
            return 1;
        }

        $this->info(sprintf('Database "%s" created successfully.', $configs['database']));
    }

    private function createConnection(array $configs, bool $dryRun = false): Connector
    {
        if ($dryRun === true) {
            return new OutputConnector($this->output);
        }

        if (!in_array($configs['driver'], ['mysql', 'pgsql'])) {
            abort('Database not supported.');
        }

        return PDOConnector::make($configs);
    }

    private function compileCreateStatement(array $configs): string
    {
        return GrammarFactory::make($configs['driver'])->compileCreateDatabase($configs);
    }
}
