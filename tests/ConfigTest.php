<?php

namespace Shoaib3375\PhpDocExporter\Tests;

use PHPUnit\Framework\TestCase;
use Shoaib3375\PhpDocExporter\Config;

class ConfigTest extends TestCase
{
    private Config $config;
    private string $mainToken = 'test-main-token';
    private string $safeToken = 'test-safe-token';

    protected function setUp(): void
    {
        $this->config = new Config($this->mainToken, $this->safeToken);
    }

    public function testTokensAreCorrect()
    {
        $this->assertEquals($this->mainToken, $this->config->getMainApiToken());
        $this->assertEquals($this->safeToken, $this->config->getSafeApiToken());
    }

    public function testCanAccessFullApi()
    {
        $this->assertTrue($this->config->canAccessFullApi($this->mainToken));
        $this->assertFalse($this->config->canAccessFullApi($this->safeToken));
        $this->assertFalse($this->config->canAccessFullApi('wrong-token'));
    }

    public function testCanAccessSafeApi()
    {
        $this->assertTrue($this->config->canAccessSafeApi($this->mainToken));
        $this->assertTrue($this->config->canAccessSafeApi($this->safeToken));
        $this->assertFalse($this->config->canAccessSafeApi('wrong-token'));
    }

    public function testHasTokens()
    {
        $this->assertTrue($this->config->hasTokens());
        
        $emptyConfig = new Config('', '');
        $this->assertFalse($emptyConfig->hasTokens());
    }
}
