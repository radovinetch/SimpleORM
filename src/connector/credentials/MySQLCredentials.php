<?php


namespace SimpleORM\connector\credentials;


class MySQLCredentials extends Credentials
{
    public function getConnectionUri(): string
    {
        return vsprintf('mysql:dbname=%s;host=%s', [$this->database, $this->host]);
    }
}