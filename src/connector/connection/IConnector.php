<?php


namespace SimpleORM\connector\connection;


interface IConnector
{
    public function connect() : void;

    public function getConnection() : ?Connection;
}