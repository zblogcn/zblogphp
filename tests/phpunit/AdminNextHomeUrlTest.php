<?php

require_once dirname(__DIR__) . '/AdminNextBootstrap.php';

zbpn_boot();


/**
 * 后台界面切换（AdminHomeUrl / AdminUiIsNext）测试.
 *
 * `ui` 查询参数由 GetVars 从 $_GET/$_REQUEST 读取，这里直接替换超全局数组，
 * 测试结束后恢复，避免污染其它用例。
 */
class AdminNextHomeUrlTest extends PHPUnit\Framework\TestCase
{
    private $getBackup = [];
    private $requestBackup = [];
    private $uiBackup = null;

    protected function setUp(): void
    {
        global $zbp;


        $this->getBackup = $_GET;
        $this->requestBackup = $_REQUEST;
        $this->uiBackup = $zbp->option['ZC_ADMIN_UI'] ?? null;
    }

    protected function tearDown(): void
    {
        global $zbp;

        $_GET = $this->getBackup;
        $_REQUEST = $this->requestBackup;

        if ($this->uiBackup === null) {
            unset($zbp->option['ZC_ADMIN_UI']);
        } else {
            $zbp->option['ZC_ADMIN_UI'] = $this->uiBackup;
        }
    }

    private function setQuery(array $query): void
    {
        $_GET = $query;
        $_REQUEST = $query;
    }

    private function setUi(?string $value): void
    {
        global $zbp;

        if ($value === null) {
            unset($zbp->option['ZC_ADMIN_UI']);
        } else {
            $zbp->option['ZC_ADMIN_UI'] = $value;
        }
    }

    /** @test */
    public function testDefaultIsNextUi()
    {
        $this->setUi(null);
        $this->setQuery([]);

        $this->assertTrue(AdminUiIsNext());
        $this->assertSame('admin-next/index.php', AdminHomeUrl());
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
    public function testQueryLegacyOverridesOptionLegacy()
    {
        $this->setUi('legacy');
        $this->setQuery(['ui' => 'legacy']);

        $this->assertFalse(AdminUiIsNext());
    }

    /** @test */
    public function testUnknownOptionValueFallsBackToNext()
    {
        $this->setUi('something-else');
        $this->setQuery([]);

        $this->assertTrue(AdminUiIsNext());
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
            AdminHomeUrl('?act=ArticleMng')
        );
    }

    /** @test */
    public function testSuffixAppendedToLegacyHome()
    {
        $this->setUi('legacy');
        $this->setQuery([]);

        $this->assertSame(
            'admin/index.php?act=admin&act=ArticleMng',
            AdminHomeUrl('&act=ArticleMng')
        );
    }

    /** @test */
    public function testLegacyHomeKeepsActAdmin()
    {
        $this->setUi('legacy');
        $this->setQuery([]);

        // 旧后台入口必须以 act=admin 结尾，否则会落进 switch 的 default 分支
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
}
