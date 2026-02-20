<?php

namespace Shoaib3375\PhpDocExporter\Tests;

use PHPUnit\Framework\TestCase;
use Shoaib3375\PhpDocExporter\Config;

class ConfigTest extends TestCase
{
    private Config $config;

    protected function setUp(): void
    {
        $this->config = new Config();
    }

    public function testTokensAreCorrect()
    {
        $this->assertEquals('903352ea22c8ab26bf76ee18a452b3377d2a7d5c', $this->config->getMainApiToken());
        $this->assertEquals('5de6f212bc0320ed82a4eeb914115b9f450625f6', $this->config->getSafeApiToken());
    }

    public function testCanAccessFullApi()
    {
        $mainToken = '903352ea22c8ab26bf76ee18a452b3377d2a7d5c';
        $safeToken = '5de6f212bc0320ed82a4eeb914115b9f450625f6';

        $this->assertTrue($this->config->canAccessFullApi($mainToken));
        $this->assertFalse($this->config->canAccessFullApi($safeToken));
        $this->assertFalse($this->config->canAccessFullApi('wrong-token'));
    }

    public function testCanAccessSafeApi()
    {
        $mainToken = '903352ea22c8ab26bf76ee18a452b3377d2a7d5c';
        $safeToken = '5de6f212bc0320ed82a4eeb914115b9f450625f6';

        $this->assertTrue($this->config->canAccessSafeApi($mainToken));
        $this->assertTrue($this->config->canAccessSafeApi($safeToken));
        $this->assertFalse($this->config->canAccessSafeApi('wrong-token'));
    }
}
