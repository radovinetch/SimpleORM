<?php

namespace SimpleORM\tests;

use SimpleORM\connector\connection\Connector;
use SimpleORM\connector\credentials\MySQLCredentials;
use SimpleORM\SimpleORM;
use PHPUnit\Framework\TestCase;

class SimpleORMTestCase extends TestCase
{
    private SimpleORM $ORM;

    protected function setUp(): void
    {
        $this->ORM = new SimpleORM('db:mysql;host:127.0.0.1;user:root;password:;database:sever1');
    }

    public function testConnection() {
        $this->assertInstanceOf(\PDO::class, $this->ORM->getConnector()->getConnection()->getPDO());
    }
}
