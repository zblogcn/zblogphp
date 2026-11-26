<?php

require_once dirname(__DIR__) . '/AdminNextBootstrap.php';

if (!defined('ZBP_PATH')) {
    // Load the framework once, before PHPUnit snapshots the output buffer level.
    zbpn_boot();
}

/**
 * admin-next legacy hook compatibility diagnostics.
 */
class AdminNextCompatTest extends PHPUnit\Framework\TestCase
{
    private array $hookNames = [
        'Filter_Plugin_Edit_Begin',
        'Filter_Plugin_Edit_End',
        'Filter_Plugin_Edit_SubMenu',
        'Filter_Plugin_Edit_Response',
        'Filter_Plugin_Edit_Response2',
        'Filter_Plugin_Edit_Response3',
        'Filter_Plugin_Edit_Response4',
        'Filter_Plugin_Edit_Response5',
        'Filter_Plugin_Admin_Header',
    ];

    private array $savedHooks = [];
    private array $savedPlugins = [];

    protected function setUp(): void
    {
        zbpn_boot();

        foreach ($this->hookNames as $hook) {
            $this->savedHooks[$hook] = [
                'exists' => isset($GLOBALS['hooks'][$hook]),
                'value'  => $GLOBALS['hooks'][$hook] ?? [],
            ];
            if (!isset($GLOBALS['hooks'][$hook])) {
                DefinePluginFilter($hook);
            }
            ClearFilterPlugin($hook);
        }
        $this->savedPlugins = (array) ($GLOBALS['plugins'] ?? []);
    }

    protected function tearDown(): void
    {
        foreach ($this->savedHooks as $hook => $saved) {
            if ($saved['exists']) {
                $GLOBALS['hooks'][$hook] = $saved['value'];
            } else {
                unset($GLOBALS['hooks'][$hook]);
            }
        }
        $GLOBALS['plugins'] = $this->savedPlugins;
    }

    /** @test */
    public function testDetectsRegisteredLegacyEditHookAndPlugin()
    {
        $GLOBALS['plugins']['CustomMeta'] = 'ActivePlugin_CustomMeta';
        Add_Filter_Plugin('Filter_Plugin_Edit_Response', 'CustomMeta_Edit_Response');
        Add_Filter_Plugin('Filter_Plugin_Admin_Header', 'CustomMeta_CSS_Response');

        $compat = ZbpAdminNext_CompatData();

        $this->assertTrue($compat['legacyEditRequired']);
        $this->assertSame(['CustomMeta'], $compat['legacyEditPlugins']);
        $this->assertSame('Filter_Plugin_Edit_Response', $compat['legacyEditHooks'][0]['hook']);
        $this->assertSame('CustomMeta_Edit_Response', $compat['legacyEditHooks'][0]['callback']);
        $this->assertSame('CustomMeta', $compat['legacyEditHooks'][0]['plugin']);
        $this->assertSame('Filter_Plugin_Admin_Header', $compat['adminHeaderHooks'][0]['hook']);
    }

    /** @test */
    public function testAdminHeaderHookAloneDoesNotRequireLegacyEditor()
    {
        $GLOBALS['plugins']['AdminColor'] = 'ActivePlugin_AdminColor';
        Add_Filter_Plugin('Filter_Plugin_Admin_Header', 'AdminColor_Css');

        $compat = ZbpAdminNext_CompatData();

        $this->assertFalse($compat['legacyEditRequired']);
        $this->assertSame([], $compat['legacyEditHooks']);
        $this->assertCount(1, $compat['adminHeaderHooks']);
    }

    /** @test */
    public function testRecognizesEveryLegacyEditHook()
    {
        $GLOBALS['plugins']['LegacyEditor'] = 'ActivePlugin_LegacyEditor';
        foreach ($this->hookNames as $index => $hook) {
            if (str_starts_with($hook, 'Filter_Plugin_Edit_')) {
                Add_Filter_Plugin($hook, 'LegacyEditor_Hook' . $index);
            }
        }

        $compat = ZbpAdminNext_CompatData();

        $this->assertTrue($compat['legacyEditRequired']);
        $this->assertSame(
            array_values(array_filter(
                $this->hookNames,
                static fn (string $hook): bool => str_starts_with($hook, 'Filter_Plugin_Edit_')
            )),
            array_column($compat['legacyEditHooks'], 'hook')
        );
    }

    /** @test */
    public function testUnknownCallbackStillRequiresFallback()
    {
        Add_Filter_Plugin('Filter_Plugin_Edit_Response5', 'third_party_edit_fields');

        $compat = ZbpAdminNext_CompatData();

        $this->assertTrue($compat['legacyEditRequired']);
        $this->assertNull($compat['legacyEditHooks'][0]['plugin']);
        $this->assertSame([], $compat['legacyEditPlugins']);
    }

    /** @test */
    public function testNoRegisteredEditHookKeepsSpaEditor()
    {
        $compat = ZbpAdminNext_CompatData();

        $this->assertFalse($compat['legacyEditRequired']);
        $this->assertSame([], $compat['legacyEditHooks']);
        $this->assertSame([], $compat['legacyEditPlugins']);
    }

    /** @test */
    public function testShellPublishesCompatibilityDiagnostics()
    {
        $shell = (string) file_get_contents(
            dirname(__DIR__, 2) . '/zb_system/admin-next/bootstrap/shell.php'
        );
        $entry = (string) file_get_contents(
            dirname(__DIR__, 2) . '/zb_system/admin-next/index.php'
        );

        $this->assertStringContainsString("'compat'    => ZbpAdminNext_CompatData()", $shell);
        $this->assertStringContainsString("require __DIR__ . '/bootstrap/compat.php';", $entry);
    }
}
