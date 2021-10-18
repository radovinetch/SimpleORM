<?php


namespace SimpleORM\connector\connection;


class MySQLConnection extends Connection
{
    /**
     * @param string $query
     * @param array $params
     * @param bool $fetchAll
     * @return array|null
     */
    public function fetch(string $query, array $params = [], bool $fetchAll = false): ?array
    {
        $prepare = $this->PDO->prepare($query);
        $prepare->execute($params);
        return $prepare->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $query
     * @param array $params
     * @return bool
     */
    public function exec(string $query, array $params = []): bool
    {
        $prepare = $this->PDO->prepare($query);
        return $prepare->execute($params);
    }
}