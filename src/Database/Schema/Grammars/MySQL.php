<?php

namespace Tonysm\LaravelParatest\Database\Schema\Grammars;

class MySQL implements SQL
{
    public function compileCreateDatabase(array $options): string
    {
        return sprintf(
            "CREATE DATABASE IF NOT EXISTS %s CHARACTER SET %s COLLATE %s;",
            $options['database'],
            $options['charset'],
            $options['collation']
        );
    }

    public function compileDropDatabase(string $database): string
    {
        return sprintf(
            'DROP DATABASE IF EXISTS %s;',
            $database
        );
    }
}
