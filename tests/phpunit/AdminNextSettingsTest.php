<?php

require_once dirname(__DIR__) . '/AdminNextBootstrap.php';

if (!defined('ZBP_PATH')) {
    // Load the framework once, before PHPUnit snapshots the output buffer level.
    zbpn_boot();
}

class AdminNextSettingsTest extends PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        zbpn_boot();

        require_once ZBP_PATH . 'zb_system/api/system.php';
    }

    public function testSettingsPageCoversEveryApiSettingKeyExactlyOnce(): void
    {
        global $setting_keys;

        $source = file_get_contents(
            dirname(__DIR__) . '/../utils/admin-next-src/src/pages/settings.tsx'
        );
        preg_match_all('/key:\s*"([^"]+)"/', $source, $matches);
        $uiKeys = $matches[1];

        $this->assertCount(43, $setting_keys);
        $this->assertCount(43, $uiKeys);
        $this->assertSame(count($uiKeys), count(array_unique($uiKeys)));

        $settingKeys = $setting_keys;
        sort($settingKeys);
        sort($uiKeys);
        $this->assertSame($settingKeys, $uiKeys);
    }

    public function testBlogHostRemainsReadOnlyWithLegacySettingsLink(): void
    {
        $source = file_get_contents(
            dirname(__DIR__) . '/../utils/admin-next-src/src/pages/settings.tsx'
        );

        $this->assertMatchesRegularExpression(
            '/key:\s*"ZC_BLOG_HOST",[\s\S]*?type:\s*"readonly",[\s\S]*?legacyHref:/',
            $source
        );
        $this->assertStringContainsString('站点地址只读', $source);
        $this->assertStringContainsString('打开旧版后台（legacy）网站设置', $source);
        $this->assertStringContainsString('act=SettingMng&force=1', $source);
    }

    public function testDeleteMemberDataOptionIsExposedAsSwitch(): void
    {
        $source = file_get_contents(
            dirname(__DIR__) . '/../utils/admin-next-src/src/pages/settings.tsx'
        );

        $this->assertMatchesRegularExpression(
            '/key:\s*"ZC_DELMEMBER_WITH_ALLDATA",[\s\S]*?type:\s*"switch"/',
            $source
        );
    }

    public function testMemberListExposesDeleteMemberDataOption(): void
    {
        $source = file_get_contents(ZBP_PATH . 'zb_system/api/member.php');

        $this->assertMatchesRegularExpression(
            "/'delete_member_with_all_data'\s*=>\s*\(bool\)\s*\\\$zbp->option\['ZC_DELMEMBER_WITH_ALLDATA'\]/",
            $source
        );
    }

    public function testMemberDeleteConfirmationDescribesBothContentModesAndAttachments(): void
    {
        $source = file_get_contents(
            dirname(__DIR__) . '/../utils/admin-next-src/src/pages/members.tsx'
        );

        $this->assertStringContainsString('data?.delete_member_with_all_data', $source);
        $this->assertStringContainsString('文章、评论和附件将被永久删除', $source);
        $this->assertStringContainsString('文章和评论将保留', $source);
        $this->assertStringContainsString('附件仍会被删除', $source);
    }
}
