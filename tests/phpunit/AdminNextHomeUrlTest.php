<?php

require_once dirname(__DIR__) . '/AdminNextBootstrap.php';

/*
 * Admin entry switching tests (AdminUiIsNext / AdminHomeUrl).
 *
 * The `ui` query flag is read through GetVars from $_GET/$_REQUEST;
 * both are swapped per test and restored afterwards.
 */

if (!defined('ZBP_PATH')) {
    // Load the framework once, before PHPUnit snapshots the output buffer level.
    zbpn_boot();
}

/**
 * @internal
 *
 * @coversNothing
 */
class AdminNextHomeUrlTest extends PHPUnit\Framework\TestCase
{
    private $getBackup = [];
    private $requestBackup = [];
    private $uiBackup;

    protected function setUp(): void
    {
        zbpn_boot();

        $this->getBackup = $_GET;
        $this->requestBackup = $_REQUEST;
        $this->uiBackup = $GLOBALS['zbp']->option['ZC_ADMIN_UI'] ?? null;
    }

    protected function tearDown(): void
    {
        $zbp = $GLOBALS['zbp'];

        $_GET = $this->getBackup;
        $_REQUEST = $this->requestBackup;

        if (null === $this->uiBackup) {
            unset($zbp->option['ZC_ADMIN_UI']);
        } else {
            $zbp->option['ZC_ADMIN_UI'] = $this->uiBackup;
        }
    }

    /** @test */
    public function testDefaultIsNextUiAfterReleaseGates()
    {
        $this->setUi(null);
        $this->setQuery([]);

        $this->assertTrue(AdminUiIsNext());
        $this->assertSame('admin-next/index.php', AdminHomeUrl());
    }

    /** @test */
    public function testDefaultOptionIsNext()
    {
        $options = include dirname(__DIR__, 2) . '/zb_system/defend/option.php';

        $this->assertSame('next', $options['ZC_ADMIN_UI']);
    }

    /** @test */
    public function testOptionNextExplicit()
    {
        $this->setUi('next');
        $this->setQuery([]);

        $this->assertTrue(AdminUiIsNext());
        $this->assertSame('admin-next/index.php', AdminHomeUrl());
    }

    /** @test */
    public function testOptionLegacySwitchesHome()
    {
        $this->setUi('legacy');
        $this->setQuery([]);

        $this->assertFalse(AdminUiIsNext());
        $this->assertSame('admin/index.php?act=admin', AdminHomeUrl());
    }

    /** @test */
    public function testQueryLegacyOverridesOptionNext()
    {
        $this->setUi('next');
        $this->setQuery(['ui' => 'legacy']);

        $this->assertFalse(AdminUiIsNext());
        $this->assertSame('admin/index.php?act=admin', AdminHomeUrl());
    }

    /** @test */
    public function testQueryLegacyOverridesDefaultNext()
    {
        $this->setUi(null);
        $this->setQuery(['ui' => 'legacy']);

        $this->assertFalse(AdminUiIsNext());
        $this->assertSame('admin/index.php?act=admin', AdminHomeUrl());
    }

    /** @test */
    public function testQueryLegacyOverridesOptionLegacy()
    {
        $this->setUi('legacy');
        $this->setQuery(['ui' => 'legacy']);

        $this->assertFalse(AdminUiIsNext());
    }

    /** @test */
    public function testUnknownOptionValueFallsBackToLegacy()
    {
        $this->setUi('something-else');
        $this->setQuery([]);

        $this->assertFalse(AdminUiIsNext());
        $this->assertSame('admin/index.php?act=admin', AdminHomeUrl());
    }

    /** @test */
    public function testUnknownQueryValueDoesNotSwitch()
    {
        $this->setUi('next');
        $this->setQuery(['ui' => 'next']);

        $this->assertTrue(AdminUiIsNext());
    }

    /** @test */
    public function testSuffixIsAppended()
    {
        $this->setUi('next');
        $this->setQuery([]);

        $this->assertSame(
            'admin-next/index.php?act=ArticleMng',
            AdminHomeUrl('?act=ArticleMng'),
        );
    }

    /** @test */
    public function testSuffixAppendedToLegacyHome()
    {
        $this->setUi('legacy');
        $this->setQuery([]);

        $this->assertSame(
            'admin/index.php?act=admin&act=ArticleMng',
            AdminHomeUrl('&act=ArticleMng'),
        );
    }

    /** @test */
    public function testLegacyHomeKeepsActAdmin()
    {
        $this->setUi('legacy');
        $this->setQuery([]);

        $this->assertStringEndsWith('act=admin', AdminHomeUrl());
    }

    /** @test */
    public function testOptionsContainBothEntries()
    {
        $options = AdminUiOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('next', $options);
        $this->assertArrayHasKey('legacy', $options);
        $this->assertCount(2, $options);
    }

    private function setQuery(array $query): void
    {
        $_GET = $query;
        $_REQUEST = $query;
    }

    private function setUi(?string $value): void
    {
        $zbp = $GLOBALS['zbp'];

        if (null === $value) {
            unset($zbp->option['ZC_ADMIN_UI']);
        } else {
            $zbp->option['ZC_ADMIN_UI'] = $value;
        }
    }
}
