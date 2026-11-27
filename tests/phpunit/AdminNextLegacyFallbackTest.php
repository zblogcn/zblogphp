<?php

/**
 * admin-next legacy fallback entry contract.
 *
 * @internal
 *
 * @coversNothing
 */
class AdminNextLegacyFallbackTest extends PHPUnit\Framework\TestCase
{
    /** @test */
    public function testLegacyQueryRedirectsStraightToForcedLegacyAdmin()
    {
        $source = $this->entrySource();

        $this->assertStringContainsString(
            "if (GetVars('ui', 'GET') === 'legacy') {",
            $source,
        );
        $this->assertStringContainsString(
            "Redirect(\$zbp->host . 'zb_system/admin/index.php?force=1');",
            $source,
        );
        $this->assertStringNotContainsString(
            "Redirect(\$zbp->cmdurl . '?act=admin');",
            $source,
        );
    }

    /** @test */
    public function testCompatibilityFallbackAlsoBypassesTheNextUiRedirect()
    {
        $source = $this->entrySource();

        $this->assertStringContainsString(
            '<a href="../admin/index.php?force=1">返回旧版后台</a>',
            $source,
        );
        $this->assertStringNotContainsString('cmd.php?act=admin', $source);
    }

    /** @test */
    public function testLegacyEntryHonorsForceQuery()
    {
        $file = dirname(__DIR__, 2) . '/zb_system/admin/index.php';
        $source = (string) file_get_contents($file);

        $this->assertStringContainsString(
            "AdminUiIsNext() && GetVars('force', 'GET') !== '1'",
            $source,
        );
    }

    private function entrySource(): string
    {
        $file = dirname(__DIR__, 2) . '/zb_system/admin-next/index.php';

        $this->assertFileExists($file);

        return (string) file_get_contents($file);
    }
}
