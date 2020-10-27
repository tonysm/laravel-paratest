<?php

namespace Tonysm\LaravelParatest\Database\Schema;

use Tonysm\LaravelParatest\Database\Connector;

class Builder
{
    public function __construct(Connector $connector, GrammarFactory $grammars)
    {
        $this->connector = $connector;
        $this->grammars = $grammars;
    }

    public function createDatabase(array $options)
    {
        $grammar = $this->grammars->make($options['driver']);

        return $this->connector->exec(
            $grammar->compileCreateDatabase($options)
        );
    }

    public function dropDatabase(array $options)
    {
        $grammar = $this->grammars->make($options['driver']);

        return $this->connector->exec(
            $grammar->compileDropDatabase($options['database'])
        );
    }

    public function recreateDatabase(array $options): void
    {
        $this->dropDatabase($options);
        $this->createDatabase($options);
    }
}
