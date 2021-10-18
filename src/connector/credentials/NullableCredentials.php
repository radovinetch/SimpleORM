<?php


namespace SimpleORM\connector\credentials;


class NullableCredentials extends Credentials
{
    public function getConnectionUri(): string
    {
        return '';
    }
}