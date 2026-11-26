<?php

require_once dirname(__DIR__) . '/AdminNextBootstrap.php';

/**
 * admin-next navigation, rights and <admin> manifest tests.
 *
 * Pure functions only: no database, no session.
 */

if (!defined('ZBP_PATH')) {
    // Load the framework once, before PHPUnit snapshots the output buffer level.
    zbpn_boot();
}

class AdminNextNavTest extends PHPUnit\Framework\TestCase
{
    private $fixtureDir = '';

    protected function setUp(): void
    {
        zbpn_boot();

        $this->fixtureDir = sys_get_temp_dir() . '/zbpn-xml-' . uniqid('', true);
        mkdir($this->fixtureDir, 0777, true);
    }

    protected function tearDown(): void
    {
        if ($this->fixtureDir !== '' && is_dir($this->fixtureDir)) {
            foreach (glob($this->fixtureDir . '/*') ?: [] as $file) {
                @unlink($file);
            }
            @rmdir($this->fixtureDir);
        }
    }

    /** @test */
    public function testOfficialNavContainsRequiredFields()
    {
        $this->swapUser(zbpn_test_member());

        $levelBefore = ob_get_level();
        $items = ZbpAdminNext_OfficialNav();

        // Framework boot opens an output buffer; calling the collector must not change it.
        $this->assertSame($levelBefore, ob_get_level());

        $this->assertIsArray($items);
        $this->assertGreaterThan(0, count($items));

        foreach ($items as $item) {
            foreach (['id', 'title', 'url', 'icon', 'group', 'order', 'external'] as $key) {
                $this->assertArrayHasKey($key, $item);
            }

            $this->assertFalse($item['external']);
            $this->assertStringStartsWith('#/', $item['url']);
            $this->assertNotEmpty($item['title']);
        }
    }

    /** @test */
    public function testOfficialNavRespectsRights()
    {
        $this->swapUser(zbpn_test_member(['Level' => 1]));
        $ids = array_column(ZbpAdminNext_OfficialNav(), 'id');

        $this->assertContains('posts', $ids);
        $this->assertContains('plugins', $ids);

        $this->swapUser(zbpn_test_member(['Level' => 6]));
        $ids = array_column(ZbpAdminNext_OfficialNav(), 'id');

        $this->assertNotContains('plugins', $ids);
        $this->assertNotContains('themes', $ids);
    }

    /** @test */
    public function testParseLegacyMenuSkipsOfficialItems()
    {
        $html = '<li id="nav_article"><a id="aArticleMng" href="http://localhost/zb_system/cmd.php?act=ArticleMng" title="post">'
            . '<span><i class="icon-stickies"></i>Article</span></a></li>'
            . '<li class="split"><hr/></li>'
            . '<li id="nav_custom"><a id="aCustom" href="http://localhost/zb_users/plugin/DemoPanel/main.php" title="Demo">'
            . '<span><i class="icon-puzzle-fill"></i>Demo panel</span></a></li>';

        $items = ZbpAdminNext_ParseLegacyMenu($html);

        $this->assertCount(1, $items);
        $this->assertSame('Demo', $items[0]['title']);
        $this->assertTrue($items[0]['external']);
        $this->assertSame('puzzle', $items[0]['icon']);
        $this->assertSame('extend', $items[0]['group']);
    }

    /** @test */
    public function testParseLegacyMenuIgnoresJavascriptLinks()
    {
        $html = '<li id="nav_toggle"><a href="javascript:void(0)"><span>toggle</span></a></li>';

        $this->assertSame([], ZbpAdminNext_ParseLegacyMenu($html));
    }

    /** @test */
    public function testParseLegacyMenuHandlesEmptyInput()
    {
        $this->assertSame([], ZbpAdminNext_ParseLegacyMenu(''));
        $this->assertSame([], ZbpAdminNext_ParseLegacyMenu('   '));
    }

    /** @test */
    public function testParseLegacyMenuDeduplicatesByUrl()
    {
        $link = '<li><a href="http://localhost/zb_users/plugin/X/main.php"><span>X</span></a></li>';

        $this->assertCount(1, ZbpAdminNext_ParseLegacyMenu($link . $link));
    }

    /** @test */
    public function testParseLegacyTopMenuSkipsOfficialItemsAndKeepsPluginItem()
    {
        $html = '<li id="topmenu1"><a href="http://localhost/zb_system/cmd.php?act=admin" title="Dashboard"><span>Dashboard</span></a></li>'
            . '<li id="topmenu2"><a href="http://localhost/zb_system/cmd.php?act=SettingMng" title="Settings"><span>Settings</span></a></li>'
            . '<li id="topmenu3"><a href="https://www.zblogcn.com/" title="Official"><span>Official</span></a></li>'
            . '<li id="duplicate-posts"><a href="http://localhost/zb_system/cmd.php?act=ArticleMng" title="Posts again"><span>Posts again</span></a></li>'
            . '<li id="zbpdk"><a href="http://localhost/zb_users/plugin/ZBPDK/main.php" title="Dev tools"><i class="icon-tools"></i><span>Dev tools</span></a></li>';

        $items = ZbpAdminNext_ParseLegacyTopMenu($html);

        $this->assertCount(1, $items);
        $this->assertSame('Dev tools', $items[0]['title']);
        $this->assertSame('http://localhost/zb_users/plugin/ZBPDK/main.php', $items[0]['url']);
        $this->assertTrue($items[0]['legacy']);
        $this->assertTrue($items[0]['external']);
    }

    /** @test */
    public function testParseLegacyTopMenuMapsSingleManifestRoute()
    {
        $manifest = [
            'appId'   => 'DemoPanel',
            'baseUrl' => 'https://example.com/zb_users/plugin/DemoPanel/',
            'menu'    => [['route' => '/plugin/DemoPanel']],
        ];
        $html = '<li><a href="https://example.com/zb_users/plugin/DemoPanel/main.php" title="Demo"><span>Demo</span></a></li>';

        $items = ZbpAdminNext_ParseLegacyTopMenu($html, [$manifest]);

        $this->assertCount(1, $items);
        $this->assertSame('#/plugin/DemoPanel', $items[0]['url']);
        $this->assertFalse($items[0]['legacy']);
        $this->assertFalse($items[0]['external']);
    }

    /** @test */
    public function testParseLegacyTopMenuKeepsAmbiguousManifestAsLegacy()
    {
        $manifest = [
            'appId'   => 'DemoPanel',
            'baseUrl' => 'https://example.com/zb_users/plugin/DemoPanel/',
            'menu'    => [
                ['route' => '/plugin/DemoPanel'],
                ['route' => '/plugin/DemoPanel/settings'],
            ],
        ];
        $html = '<li><a href="https://example.com/zb_users/plugin/DemoPanel/main.php"><span>Demo</span></a></li>';

        $items = ZbpAdminNext_ParseLegacyTopMenu($html, [$manifest]);

        $this->assertTrue($items[0]['legacy']);
        $this->assertTrue($items[0]['external']);
    }

    /** @test */
    public function testParseLegacyTopMenuRejectsUnsafeSchemesAndDeduplicatesUrls()
    {
        $safe = '<li><a href="https://example.com/plugin/main.php"><span>Safe</span></a></li>';
        $html = '<li><a href="java&#10;script:alert(1)"><span>Bad</span></a></li>'
            . '<li><a href="data:text/html,x"><span>Bad data</span></a></li>'
            . $safe . $safe;

        $items = ZbpAdminNext_ParseLegacyTopMenu($html);

        $this->assertCount(1, $items);
        $this->assertSame('Safe', $items[0]['title']);
    }

    /** @test */
    public function testDedupeNavKeepsOfficialItemBeforeLegacyDuplicate()
    {
        $items = [
            ['id' => 'posts', 'url' => '#/posts'],
            ['id' => 'legacy_posts', 'url' => '#/posts'],
            ['id' => 'posts', 'url' => '#/duplicate-id'],
        ];

        $this->assertSame([['id' => 'posts', 'url' => '#/posts']], ZbpAdminNext_DedupeNav($items));
    }

    /** @test */
    public function testNavRunsTopMenuHookWithoutDuplicatingOfficialMenus()
    {
        global $zbp;

        $this->swapUser(zbpn_test_member());
        $hadHook = isset($GLOBALS['hooks']['Filter_Plugin_Admin_TopMenu']);
        $savedHook = $GLOBALS['hooks']['Filter_Plugin_Admin_TopMenu'] ?? [];
        $savedTopMenus = $GLOBALS['topmenus'] ?? [];
        $savedLeftMenus = $GLOBALS['leftmenus'] ?? [];
        $hadPluginList = array_key_exists('ZC_USING_PLUGIN_LIST', $zbp->option);
        $savedPluginList = $zbp->option['ZC_USING_PLUGIN_LIST'] ?? '';

        try {
            ClearFilterPlugin('Filter_Plugin_Admin_TopMenu');
            Add_Filter_Plugin('Filter_Plugin_Admin_TopMenu', self::class . '::addTopMenuFixture');
            $zbp->option['ZC_USING_PLUGIN_LIST'] = '';

            $items = ZbpAdminNext_Nav();
            $dashboard = array_values(array_filter(
                $items,
                static fn (array $item): bool => ($item['id'] ?? '') === 'dashboard'
            ));
            $legacy = array_values(array_filter(
                $items,
                static fn (array $item): bool => ($item['title'] ?? '') === 'Legacy top fixture'
            ));

            $this->assertCount(1, $dashboard);
            $this->assertCount(1, $legacy);
            $this->assertTrue($legacy[0]['legacy']);
            $this->assertTrue($legacy[0]['external']);
        } finally {
            if ($hadHook) {
                $GLOBALS['hooks']['Filter_Plugin_Admin_TopMenu'] = $savedHook;
            } else {
                unset($GLOBALS['hooks']['Filter_Plugin_Admin_TopMenu']);
            }
            $GLOBALS['topmenus'] = $savedTopMenus;
            $GLOBALS['leftmenus'] = $savedLeftMenus;
            if ($hadPluginList) {
                $zbp->option['ZC_USING_PLUGIN_LIST'] = $savedPluginList;
            } else {
                unset($zbp->option['ZC_USING_PLUGIN_LIST']);
            }
        }
    }

    /** @test */
    public function testMapIconFallsBackForUnknownClass()
    {
        $this->assertSame('puzzle', ZbpAdminNext_MapIcon('icon-puzzle-fill'));
        $this->assertSame('folder', ZbpAdminNext_MapIcon('icon-folder-fill extra-class'));
        $this->assertSame('link', ZbpAdminNext_MapIcon('icon-does-not-exist'));
        $this->assertSame('link', ZbpAdminNext_MapIcon(null));
        $this->assertSame('link', ZbpAdminNext_MapIcon(''));
    }

    /** @test */
    public function testRightsArrayCoversAllActions()
    {
        global $actions;

        $this->swapUser(zbpn_test_member(['Level' => 1]));

        $rights = ZbpAdminNext_Rights();

        foreach (array_keys($actions) as $action) {
            $this->assertArrayHasKey($action, $rights);
        }

        $this->assertTrue($rights['admin']);
    }

    /** @test */
    public function testParsesFullAdminNode()
    {
        $admin = $this->parse(
            '<plugin><admin version="2">'
            . '<menu>'
            . '<item id="panel" title="Panel" route="/plugin/DemoPanel" icon="puzzle" group="extend" order="150" require="admin"/>'
            . '<item id="about" title="About" route="/plugin/DemoPanel/about" icon="info"/>'
            . '</menu>'
            . '<spa><entry>dist/admin.js</entry><style>dist/admin.css</style></spa>'
            . '<legacy>main.php</legacy>'
            . '</admin></plugin>'
        );

        $this->assertIsArray($admin);
        $this->assertSame(2, $admin['version']);
        $this->assertSame('dist/admin.js', $admin['entry']);
        $this->assertSame('dist/admin.css', $admin['style']);
        $this->assertSame('main.php', $admin['legacy']);
        $this->assertCount(2, $admin['menu']);

        $this->assertSame('panel', $admin['menu'][0]['id']);
        $this->assertSame('admin', $admin['menu'][0]['require']);
        $this->assertSame(150, $admin['menu'][0]['order']);

        $this->assertNull($admin['menu'][1]['require']);
        $this->assertSame(0, $admin['menu'][1]['order']);
    }

    /** @test */
    public function testIgnoresMissingOrUnknownVersion()
    {
        $this->assertNull($this->parse('<plugin><id>X</id></plugin>'));
        $this->assertNull($this->parse('<plugin><admin version="1"><spa><entry>x.js</entry></spa></admin></plugin>'));
        $this->assertNull($this->parse('<plugin><admin version="3"><spa><entry>x.js</entry></spa></admin></plugin>'));
        $this->assertNull($this->parse('<plugin><admin><spa><entry>x.js</entry></spa></admin></plugin>'));
    }

    /** @test */
    public function testSkipsMenuItemsWithoutIdOrTitle()
    {
        $admin = $this->parse(
            '<plugin><admin version="2"><menu>'
            . '<item title="no id"/>'
            . '<item id="no_title"/>'
            . '<item id="ok" title="OK"/>'
            . '</menu></admin></plugin>'
        );

        $this->assertCount(1, $admin['menu']);
        $this->assertSame('ok', $admin['menu'][0]['id']);
    }

    /** @test */
    public function testBuildManifestFromDeclaration()
    {
        $manifest = ZbpAdminNext_BuildManifest($this->adminWithMenu(), $this->makeManifestInfo());

        $this->assertIsArray($manifest);
        $this->assertSame('DemoPanel', $manifest['appId']);
        $this->assertSame('plugin', $manifest['type']);
        $this->assertSame('admin.js', $manifest['entry']);
        $this->assertCount(1, $manifest['menu']);
        $this->assertSame('DemoPanel_panel', $manifest['menu'][0]['id']);
        $this->assertSame('puzzle', $manifest['menu'][0]['icon']);
        $this->assertSame('extend', $manifest['menu'][0]['group']);
    }

    /** @test */
    public function testBuildManifestRejectsMissingEntry()
    {
        $admin = $this->parse(
            '<plugin><admin version="2"><spa><entry>dist/admin.js</entry></spa></admin></plugin>'
        );

        $this->assertNull(ZbpAdminNext_BuildManifest($admin, $this->makeManifestInfo()));
    }

    /** @test */
    public function testBuildManifestRejectsUnsafeEntryPaths()
    {
        foreach (['../../etc/passwd', '/etc/passwd'] as $entry) {
            $admin = $this->parse(
                '<plugin><admin version="2"><spa><entry>' . $entry . '</entry></spa></admin></plugin>'
            );

            $this->assertNull(
                ZbpAdminNext_BuildManifest($admin, $this->makeManifestInfo()),
                'unsafe entry rejected: ' . $entry
            );
        }
    }

    /** @test */
    public function testBuildManifestRejectsRouteOutsideNamespace()
    {
        $admin = $this->parse(
            '<plugin><admin version="2">'
            . '<menu>'
            . '<item id="bad" title="Bad" route="/settings"/>'
            . '<item id="good" title="Good" route="/plugin/DemoPanel/x"/>'
            . '</menu>'
            . '<spa><entry>admin.js</entry></spa>'
            . '</admin></plugin>'
        );

        $manifest = ZbpAdminNext_BuildManifest($admin, $this->makeManifestInfo());

        $this->assertCount(1, $manifest['menu']);
        $this->assertSame('DemoPanel_good', $manifest['menu'][0]['id']);
    }

    /** @test */
    public function testBuildManifestDefaultsRouteToAppRoot()
    {
        $manifest = ZbpAdminNext_BuildManifest(
            $this->parse(
                '<plugin><admin version="2">'
                . '<menu><item id="panel" title="Panel"/></menu>'
                . '<spa><entry>admin.js</entry></spa>'
                . '</admin></plugin>'
            ),
            $this->makeManifestInfo()
        );

        $this->assertSame('/plugin/DemoPanel', $manifest['menu'][0]['route']);
    }

    /** @test */
    public function testMenuOnlyDeclarationWithoutSpa()
    {
        $manifest = ZbpAdminNext_BuildManifest(
            $this->parse(
                '<plugin><admin version="2"><menu><item id="panel" title="Panel"/></menu></admin></plugin>'
            ),
            $this->makeManifestInfo()
        );

        $this->assertSame('', $manifest['entry']);
        $this->assertCount(1, $manifest['menu']);
    }

    /** @test */
    public function testEmptyDeclarationProducesNoManifest()
    {
        $admin = $this->parse('<plugin><admin version="2"></admin></plugin>');

        $this->assertNull(ZbpAdminNext_BuildManifest($admin, $this->makeManifestInfo()));
    }

    /** @test */
    public function testThemeDeclarationsDefaultToThemeGroup()
    {
        $info = $this->makeManifestInfo();
        $info['type'] = 'theme';

        $manifest = ZbpAdminNext_BuildManifest(
            $this->parse(
                '<theme><admin version="2">'
                . '<menu><item id="panel" title="Settings"/></menu>'
                . '<spa><entry>admin.js</entry></spa>'
                . '</admin></theme>'
            ),
            $info
        );

        $this->assertSame('theme', $manifest['menu'][0]['group']);
    }

    /** @test */
    public function testValidateAppId()
    {
        $this->assertTrue(ZbpAdminNext_ValidAppId('DemoPanel'));
        $this->assertTrue(ZbpAdminNext_ValidAppId('app_zblogcn_admin_css'));
        $this->assertTrue(ZbpAdminNext_ValidAppId('x-1'));

        $this->assertFalse(ZbpAdminNext_ValidAppId(''));
        $this->assertFalse(ZbpAdminNext_ValidAppId('../etc'));
        $this->assertFalse(ZbpAdminNext_ValidAppId('a/b'));
        $this->assertFalse(ZbpAdminNext_ValidAppId('a b'));
        $this->assertFalse(ZbpAdminNext_ValidAppId(str_repeat('a', 101)));
    }

    private function parse(string $xml): ?array
    {
        $node = @simplexml_load_string($xml);

        $this->assertNotFalse($node, 'fixture XML parse failed');

        return App::ParseAdminNode($node);
    }

    private function adminWithMenu(): array
    {
        return $this->parse(
            '<plugin><admin version="2">'
            . '<menu><item id="panel" title="Panel" route="/plugin/DemoPanel" require="admin"/></menu>'
            . '<spa><entry>admin.js</entry></spa>'
            . '<legacy>main.php</legacy>'
            . '</admin></plugin>'
        );
    }

    private function makeManifestInfo(string $entry = 'admin.js'): array
    {
        file_put_contents($this->fixtureDir . '/' . $entry, 'export function mount() {}');

        return [
            'id'      => 'DemoPanel',
            'type'    => 'plugin',
            'name'    => 'Demo',
            'version' => '1.0',
            'path'    => $this->fixtureDir . '/',
            'baseUrl' => 'https://example.com/zb_users/plugin/DemoPanel/',
        ];
    }

    public static function addTopMenuFixture(array &$topmenus): void
    {
        global $zbp;

        $topmenus[] = MakeTopMenu(
            'admin',
            'Duplicate dashboard',
            $zbp->cmdurl . '?act=admin',
            '',
            'fixture_top_dashboard'
        );
        $topmenus[] = MakeTopMenu(
            'admin',
            'Legacy top fixture',
            $zbp->host . 'zb_users/plugin/LegacyTopPanel/main.php',
            '',
            'fixture_top_legacy'
        );
    }

    private function swapUser($member)
    {
        $zbp = $GLOBALS['zbp'];
        $zbp->user = $member;
        $zbp->islogin = true;
    }
}
