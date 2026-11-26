<?php

require_once dirname(__DIR__) . '/AdminNextBootstrap.php';

if (!defined('ZBP_PATH')) {
    // Load the framework once, before PHPUnit snapshots the output buffer level.
    zbpn_boot();
}

class AdminNextAppManagementZbp extends ZBlogPHP
{
    public $pluginFixtures = [];
    public $themeFixtures = [];

    public function LoadPlugins()
    {
        return $this->pluginFixtures;
    }

    public function LoadThemes()
    {
        return $this->themeFixtures;
    }
}

class AdminNextAppManagementApp extends App
{
    public $manageUrlFixture = '';

    public function GetManageUrl()
    {
        return $this->manageUrlFixture;
    }
}

class AdminNextAppManagementTest extends PHPUnit\Framework\TestCase
{
    private $zbp;
    private $zbpBackup;
    private $actionsBackup;
    private $fixtureDir = '';

    protected function setUp(): void
    {
        zbpn_boot();

        $this->zbpBackup = $GLOBALS['zbp'];
        $this->actionsBackup = $GLOBALS['actions'] ?? null;
        $base = $this->zbpBackup;

        $GLOBALS['actions'] = include ZBP_PATH . 'zb_system/defend/actions.php';
        $zbp = new AdminNextAppManagementZbp();
        $GLOBALS['zbp'] = $zbp;
        $this->zbp = $zbp;

        $zbp->table = $base->table;
        $zbp->datainfo = $base->datainfo;
        $zbp->option = (array) $base->option;
        $zbp->lang = $base->lang;
        $zbp->actions = $GLOBALS['actions'];
        $zbp->user = zbpn_test_member(['ID' => 1, 'Level' => 1]);
        $zbp->islogin = true;
        $zbp->host = 'https://site.example/blog/';
        $zbp->cmdurl = 'https://site.example/blog/zb_system/cmd.php';
        $zbp->usersdir = $base->usersdir;
        $zbp->theme = '';
        $zbp->style = '';
        $zbp->activedapps = [];
        $zbp->option['ZC_USING_PLUGIN_LIST'] = '';
        $zbp->lang['msg']['app_center'] = '应用中心';
        $zbp->lang['msg']['legacy_plugin_manage'] = '旧版插件管理';

        $this->fixtureDir = sys_get_temp_dir() . '/zbpn-app-management-' . uniqid('', true);
        mkdir($this->fixtureDir, 0777, true);

        require_once ZBP_PATH . 'zb_system/api/app.php';
        require_once ZBP_PATH . 'zb_system/admin-next/bootstrap/shell.php';
    }

    protected function tearDown(): void
    {
        if ($this->fixtureDir !== '' && is_dir($this->fixtureDir)) {
            foreach (glob($this->fixtureDir . '/plugin/*') ?: [] as $path) {
                @unlink($path . '/plugin.xml');
                @rmdir($path);
            }
            @rmdir($this->fixtureDir . '/plugin');
            @rmdir($this->fixtureDir);
        }

        $GLOBALS['zbp'] = $this->zbpBackup;
        if ($this->actionsBackup === null) {
            unset($GLOBALS['actions']);
        } else {
            $GLOBALS['actions'] = $this->actionsBackup;
        }
        $this->zbp = null;
    }

    /** @test */
    public function testPluginListExposesManageCapabilityAndServerUrl()
    {
        $this->assertListManageMetadata('api_app_get_plugins', 'plugin', 'ManagePlugin');
    }

    /** @test */
    public function testThemeListExposesManageCapabilityAndServerUrl()
    {
        $this->assertListManageMetadata('api_app_get_themes', 'theme', 'ManageTheme');
    }

    /** @test */
    public function testDisabledAppCentreFallsBackToForcedLegacyPluginManagement()
    {
        $management = ZbpAdminNext_AppManagement();

        $this->assertFalse($management['appCentre']['enabled']);
        $this->assertNull($management['appCentre']['url']);
        $this->assertSame(
            'https://site.example/blog/zb_system/admin/index.php?act=PluginMng&force=1',
            $management['legacyPluginManageUrl']
        );

        $items = ZbpAdminNext_OfficialNav();
        $legacy = $this->findNavItem($items, 'legacy_plugin_manage');

        $this->assertNotNull($legacy);
        $this->assertSame('旧版插件管理', $legacy['title']);
        $this->assertSame($management['legacyPluginManageUrl'], $legacy['url']);
        $this->assertTrue($legacy['external']);
        $this->assertTrue($legacy['legacy']);
        $this->assertNull($this->findNavItem($items, 'app_centre'));
    }

    /** @test */
    public function testEnabledAppCentreProvidesOfficialNavAndPageEntry()
    {
        $this->installAppCentre('main.php');
        $this->zbp->option['ZC_USING_PLUGIN_LIST'] = 'AppCentre';
        $this->zbp->activedapps = ['AppCentre'];

        $management = ZbpAdminNext_AppManagement();
        $expectedUrl = 'https://site.example/blog/zb_users/plugin/AppCentre/main.php';

        $this->assertTrue($management['appCentre']['enabled']);
        $this->assertSame($expectedUrl, $management['appCentre']['url']);

        $items = ZbpAdminNext_OfficialNav();
        $appCentre = $this->findNavItem($items, 'app_centre');

        $this->assertNotNull($appCentre);
        $this->assertSame('应用中心', $appCentre['title']);
        $this->assertSame($expectedUrl, $appCentre['url']);
        $this->assertSame('PluginMng', $appCentre['require']);
        $this->assertTrue($appCentre['external']);
        $this->assertTrue($appCentre['legacy']);
        $this->assertNull($this->findNavItem($items, 'legacy_plugin_manage'));
    }

    /** @test */
    public function testInstalledAppCentreWithoutManagePageStillFallsBack()
    {
        $this->installAppCentre('');
        $this->zbp->option['ZC_USING_PLUGIN_LIST'] = 'AppCentre';
        $this->zbp->activedapps = ['AppCentre'];

        $management = ZbpAdminNext_AppManagement();

        $this->assertFalse($management['appCentre']['enabled']);
        $this->assertNull($management['appCentre']['url']);
        $this->assertNotNull($this->findNavItem(
            ZbpAdminNext_OfficialNav(),
            'legacy_plugin_manage'
        ));
    }

    /** @test */
    public function testAppManagementNavRespectsPluginManagementRight()
    {
        $this->installAppCentre('main.php');
        $this->zbp->option['ZC_USING_PLUGIN_LIST'] = 'AppCentre';
        $this->zbp->activedapps = ['AppCentre'];
        $this->zbp->user = zbpn_test_member(['ID' => 1, 'Level' => 6]);

        $ids = array_column(ZbpAdminNext_OfficialNav(), 'id');

        $this->assertNotContains('app_centre', $ids);
        $this->assertNotContains('legacy_plugin_manage', $ids);
    }

    /** @test */
    public function testBootDataPublishesServerResolvedAppManagementEntry()
    {
        $this->installAppCentre('main.php');
        $this->zbp->option['ZC_USING_PLUGIN_LIST'] = 'AppCentre';
        $this->zbp->option['ZC_MANAGE_COUNT'] = 20;
        $this->zbp->option['ZC_PAGEBAR_COUNT'] = 10;
        $this->zbp->activedapps = ['AppCentre'];
        $this->zbp->name = 'Test site';
        $this->zbp->subname = '';

        $boot = ZbpAdminNext_BootData();

        $this->assertTrue($boot['appCentre']['enabled']);
        $this->assertSame(
            'https://site.example/blog/zb_users/plugin/AppCentre/main.php',
            $boot['appCentre']['url']
        );
        $this->assertSame(
            'https://site.example/blog/zb_system/admin/index.php?act=PluginMng&force=1',
            $boot['legacyPluginManageUrl']
        );
        $this->assertNotNull($this->findNavItem($boot['nav'], 'app_centre'));
    }

    private function assertListManageMetadata(string $function, string $type, string $id): void
    {
        $manageable = $this->makeApp(
            $type,
            $id,
            'main.php',
            'https://manage.example/custom.php?tab=advanced&mode=edit'
        );
        $unmanageable = $this->makeApp(
            $type,
            $id . 'NoPage',
            '',
            'https://manage.example/no-page/'
        );

        if ($type === 'plugin') {
            $this->zbp->pluginFixtures = [$manageable, $unmanageable];
        } else {
            $this->zbp->themeFixtures = [$manageable, $unmanageable];
        }
        $this->zbp->activedapps = [$id];

        $result = $function();
        $this->assertCount(2, $result['data']['list']);

        $row = $result['data']['list'][0];
        $this->assertTrue($row['can_manage']);
        $this->assertSame($manageable->manageUrlFixture, $row['manage_url']);
        $this->assertTrue($row['is_actived']);

        $row = $result['data']['list'][1];
        $this->assertFalse($row['can_manage']);
        $this->assertSame($unmanageable->manageUrlFixture, $row['manage_url']);
        $this->assertFalse($row['is_actived']);
    }

    private function makeApp(string $type, string $id, string $path, string $manageUrl): AdminNextAppManagementApp
    {
        $app = new AdminNextAppManagementApp();
        $app->type = $type;
        $app->id = $id;
        $app->name = $id;
        $app->version = '1.0';
        $app->path = $path;
        $app->manageUrlFixture = $manageUrl;

        return $app;
    }

    private function installAppCentre(string $path): void
    {
        $dir = $this->fixtureDir . '/plugin/AppCentre';
        mkdir($dir, 0777, true);
        file_put_contents($dir . '/plugin.xml', '<?xml version="1.0" encoding="utf-8"?>'
            . '<plugin version="php">'
            . '<id>AppCentre</id><name>应用中心</name><path>' . $path . '</path>'
            . '<include>include.php</include><level>1</level>'
            . '<author><name>Test</name><email>test@example.invalid</email><url></url></author>'
            . '<source><name></name><email></email><url></url></source>'
            . '<version>1.0</version><phpver>8.0</phpver>'
            . '</plugin>');
        $this->zbp->usersdir = $this->fixtureDir . '/';
    }

    private function findNavItem(array $items, string $id): ?array
    {
        foreach ($items as $item) {
            if (($item['id'] ?? '') === $id) {
                return $item;
            }
        }

        return null;
    }
}
