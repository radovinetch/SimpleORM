<?php


namespace SimpleORM\connector\credentials;


abstract class Credentials
{
    /**
     * @var string
     */
    protected string $host;

    /**
     * @var string
     */
    protected string $username;

    /**
     * @var string
     */
    protected string $password;

    /**
     * @var string
     */
    protected string $database;

    /**
     * Connector constructor.
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $database
     */
    public function __construct(string $host, string $username, string $password, string $database)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    abstract public function getConnectionUri() : string;
}