<?php

namespace Tonysm\LaravelParatest\Database;

interface Connector
{
    /**
     * @param string $sql
     *
     * @return mixed whatever the actual implementation returns, depending on the connector
     */
    public function exec(string $sql);
}

