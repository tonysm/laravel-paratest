<?php

namespace Tonysm\LaravelParatest\Database\Schema\Grammars;

interface SQL
{
    public function compileCreateDatabase(array $options): string;
    public function compileDropDatabase(string $database): string;
}
