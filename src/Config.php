<?php

namespace Shoaib3375\PhpDocExporter;

class Config
{
    private string $mainApiToken;
    private string $safeApiToken;

    public function __construct(?string $mainToken = null, ?string $safeToken = null)
    {
        $this->mainApiToken = $mainToken ?? $this->getEnv('PHP_DOC_EXPORTER_MAIN_TOKEN', '');
        $this->safeApiToken = $safeToken ?? $this->getEnv('PHP_DOC_EXPORTER_SAFE_TOKEN', '');
    }

    private function getEnv(string $key, string $default = ''): string
    {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }

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

    public function hasTokens(): bool
    {
        return !empty($this->mainApiToken) || !empty($this->safeApiToken);
    }
}
