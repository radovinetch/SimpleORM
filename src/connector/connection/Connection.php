<?php


namespace SimpleORM\connector\connection;


abstract class Connection implements IConnection
{
    /**
     * @var \PDO
     */
    protected \PDO $PDO;

    /**
     * Connection constructor.
     * @param \PDO $PDO
     */
    public function __construct(\PDO $PDO)
    {
        $this->PDO = $PDO;
    }

    /**
     * @return \PDO
     */
    public function getPDO(): \PDO
    {
        return $this->PDO;
    }

    abstract public function fetch(string $query, array $params = [], bool $fetchAll = false): ?array;

    abstract public function exec(string $query, array $params = []): bool;
}