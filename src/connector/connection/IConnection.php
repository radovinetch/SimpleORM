<?php


namespace SimpleORM\connector\connection;


interface IConnection
{
    public function fetch(string $query, array $params = [], bool $fetchAll = false): ?array;

    public function exec(string $query, array $params = []): bool;
}