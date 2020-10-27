<?php

namespace Tonysm\LaravelParatest\Database;

use PDO;

class DryRunConnector implements Connector
{
    public function __construct($output)
    {
        $this->output = $output;
    }

    /**
     * @param string $sql
     *
     * @return mixed whatever the actual implementation returns, depending on the connector
     */
    public function exec(string $sql)
    {
        return $this->output->writeln(sprintf(
            '<info>[DRY RUN] %s</info>',
            $sql
        ));
    }
}

