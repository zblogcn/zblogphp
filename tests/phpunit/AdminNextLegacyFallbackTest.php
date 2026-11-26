<?php

/**
 * admin-next legacy fallback entry contract.
 */
class AdminNextLegacyFallbackTest extends PHPUnit\Framework\TestCase
{
    private function entrySource(): string
    {
        $file = dirname(__DIR__, 2) . '/zb_system/admin-next/index.php';

        $this->assertFileExists($file);

        return (string) file_get_contents($file);
    }

    /** @test */
    public function testLegacyQueryRedirectsStraightToForcedLegacyAdmin()
    {
        $source = $this->entrySource();

        $this->assertStringContainsString(
            "if (GetVars('ui', 'GET') === 'legacy') {",
            $source
        );
        $this->assertStringContainsString(
            "Redirect(\$zbp->host . 'zb_system/admin/index.php?force=1');",
            $source
        );
        $this->assertStringNotContainsString(
            "Redirect(\$zbp->cmdurl . '?act=admin');",
            $source
        );
    }

    /** @test */
    public function testCompatibilityFallbackAlsoBypassesTheNextUiRedirect()
    {
        $source = $this->entrySource();

        $this->assertStringContainsString(
            '<a href="../admin/index.php?force=1">返回旧版后台</a>',
            $source
        );
        $this->assertStringNotContainsString('cmd.php?act=admin', $source);
    }
}
