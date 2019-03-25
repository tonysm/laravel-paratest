<?php

namespace Tonysm\DbCreateCommand\Database\Schema\Grammars;

class PgSQL implements SQL
{
    public function compileCreateDatabase(array $options): string
    {
        return sprintf(
            "CREATE DATABASE %s"
            ." ENCODING '%s'"
            ." LC_COLLATE '%s';",
            $options['database'],
            $options['charset'],
            $options['collation'] ?? 'en_US.utf8'
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
