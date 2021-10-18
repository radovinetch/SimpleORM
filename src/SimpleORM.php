<?php


namespace SimpleORM;

use SimpleORM\connector\connection\Connector;
use SimpleORM\connector\credentials\Credentials;
use SimpleORM\connector\credentials\MySQLCredentials;
use SimpleORM\connector\credentials\NullableCredentials;
use SimpleORM\model\Model;

class SimpleORM
{
    /**
     * @var Connector
     */
    private Connector $connector;

    public function __construct(string $link)
    {
        $credentials = $this->parseCredentials($link);
        if ($credentials instanceof NullableCredentials) {
            echo 'Используется неизвестная РСУБД!';
            die();
        }

        $this->connector = new Connector($credentials);
        $this->connector->connect();

        Model::setConnection($this->connector->getConnection());
    }

    /**
     * @return Connector
     */
    public function getConnector(): Connector
    {
        return $this->connector;
    }

    /**
     * @param string $link
     * @return Credentials
     */
    private function parseCredentials(string $link) : Credentials {
        $array = [];
        foreach (explode(";", $link) as $item) {
            $item = explode(":", $item);
            $array[$item[0]] = $item[1] ?? '';
        }

        $credentials = match ($array['db']) {
            'mysql' => MySQLCredentials::class,
            default => NullableCredentials::class,
        };

        return new $credentials($array['host'], $array['user'], $array['password'], $array['database']);
    }
}