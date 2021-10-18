<?php


namespace SimpleORM\connector\connection;

use SimpleORM\connector\credentials\Credentials;

class Connector implements IConnector
{
    /**
     * @var Credentials
     */
    private Credentials $credentials;

    /**
     * @var IConnection|null
     */
    private ?IConnection $connection;

    /**
     * @return Connection|null
     */
    public function getConnection(): ?Connection
    {
        return $this->connection;
    }
    /**
     * Connector constructor.
     * @param Credentials $credentials
     */
    public function __construct(Credentials $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * @return void
     */
    public function connect() : void
    {
        try {
            $pdo = new \PDO($this->credentials->getConnectionUri(), $this->credentials->getUsername(), $this->credentials->getPassword());
            $this->connection = new MySQLConnection($pdo);
        } catch (\PDOException $exception) {
            echo 'Cannot connect to database! Error: ' . $exception->getMessage();
            die();
        }
    }
}