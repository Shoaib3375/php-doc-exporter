<?php

namespace Shoaib3375\PhpDocExporter;

class Config
{
    private string $mainApiToken = '903352ea22c8ab26bf76ee18a452b3377d2a7d5c';
    private string $safeApiToken = '5de6f212bc0320ed82a4eeb914115b9f450625f6';

    public function getMainApiToken(): string
    {
        return $this->mainApiToken;
    }

    public function getSafeApiToken(): string
    {
        return $this->safeApiToken;
    }

    public function isMainApiToken(string $token): bool
    {
        return $this->mainApiToken === $token;
    }

    public function isSafeApiToken(string $token): bool
    {
        return $this->safeApiToken === $token;
    }

    public function canAccessFullApi(string $token): bool
    {
        return $this->isMainApiToken($token);
    }

    public function canAccessSafeApi(string $token): bool
    {
        return $this->isMainApiToken($token) || $this->isSafeApiToken($token);
    }
}
