<?php

require_once dirname(__DIR__) . '/AdminNextBootstrap.php';

zbpn_boot();

require_once ZBP_PATH . 'zb_system/admin-next/bootstrap/api-compat.php';

require_once ZBP_PATH . 'zb_system/admin-next/bootstrap/shell.php';

/**
 * admin-next CSRF and same-origin regression tests.
 *
 * Pure request/state tests: no database connection and no session.
 *
 * @internal
 *
 * @coversNothing
 */
class AdminNextCsrfTest extends PHPUnit\Framework\TestCase
{
    private $server = [];
    private $get = [];
    private $post = [];
    private $request = [];
    private $user;
    private $guid;
    private $path;
    private $host;
    private $timezone;
    private $csrfExpiration;
    private $additionalSecurity = false;
    private $skipHooks = [];
    private $mod = '';
    private $act = '';

    protected function setUp(): void
    {
        global $zbp, $mod, $act;

        zbpn_boot();

        $this->server = $_SERVER;
        $this->get = $_GET;
        $this->post = $_POST;
        $this->request = $_REQUEST;
        $this->user = $zbp->user;
        $this->guid = $zbp->guid;
        $this->path = $zbp->path;
        $this->host = $zbp->host;
        $this->timezone = $zbp->option['ZC_TIME_ZONE_NAME'];
        $this->csrfExpiration = $zbp->csrfExpiration;
        $this->additionalSecurity = $zbp->option['ZC_ADDITIONAL_SECURITY'];
        $this->skipHooks = $GLOBALS['hooks']['Filter_Plugin_API_VerifyCSRF_Skip'] ?? [];
        $this->mod = $mod;
        $this->act = $act;

        $zbp->user = zbpn_test_member();
        $zbp->guid = 'admin-next-csrf-test';
        $zbp->path = '/';
        $zbp->option['ZC_TIME_ZONE_NAME'] = 'Asia/Shanghai';
        $zbp->option['ZC_ADDITIONAL_SECURITY'] = true;
        $zbp->csrfExpiration = 1;

        $GLOBALS['hooks']['Filter_Plugin_API_VerifyCSRF_Skip'] = [];
        $GLOBALS['zbp_admin_spa_verified'] = true;
        $mod = 'system';
        $act = 'setting';

        $_GET = [];
        $_POST = [];
        $_REQUEST = [];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_USER_AGENT'] = 'AdminNextSecurityTest/1.0';
        unset($_SERVER['HTTP_ORIGIN'], $_SERVER['HTTP_REFERER'], $_SERVER['HTTP_X_ZBP_ADMIN_SPA']);
    }

    protected function tearDown(): void
    {
        global $zbp, $mod, $act;

        $_SERVER = $this->server;
        $_GET = $this->get;
        $_POST = $this->post;
        $_REQUEST = $this->request;
        $zbp->user = $this->user;
        $zbp->guid = $this->guid;
        $zbp->path = $this->path;
        $zbp->host = $this->host;
        $zbp->option['ZC_TIME_ZONE_NAME'] = $this->timezone;
        $zbp->csrfExpiration = $this->csrfExpiration;
        $zbp->option['ZC_ADDITIONAL_SECURITY'] = $this->additionalSecurity;
        $GLOBALS['hooks']['Filter_Plugin_API_VerifyCSRF_Skip'] = $this->skipHooks;
        $mod = $this->mod;
        $act = $this->act;
        unset($GLOBALS['zbp_admin_spa_verified']);
    }

    /** @test */
    public function testSpaMarkerDoesNotBypassMissingCsrfToken()
    {
        $this->assertCsrfRejected();
    }

    /** @test */
    public function testSpaMarkerDoesNotBypassInvalidCsrfToken()
    {
        $_POST['csrf_token'] = $_REQUEST['csrf_token'] = 'invalid-token';

        $this->assertCsrfRejected();
    }

    /** @test */
    public function testSpaRequestAcceptsRealCsrfTokenWithAdditionalSecurityEnabled()
    {
        global $zbp;

        $_POST['csrf_token'] = $_REQUEST['csrf_token'] = $zbp->GetCSRFToken('api');

        $this->assertTrue(ApiVerifyCSRF(true));
    }

    /** @test */
    public function testCsrfTokenCanComeFromJsonPostBody()
    {
        global $zbp;

        $_POST['csrf_token'] = $zbp->GetCSRFToken('api');
        $_REQUEST = [];

        $this->assertTrue(ApiVerifyCSRF(true));
    }

    /** @test */
    public function testPostTokenTakesPrecedenceOverRequestToken()
    {
        global $zbp;

        $_POST['csrf_token'] = 'invalid-token';
        $_REQUEST['csrf_token'] = $zbp->GetCSRFToken('api');

        $this->assertCsrfRejected();
    }

    /** @test */
    public function testShellIssuesTokenThatMatchesApiVerification()
    {
        global $zbp;

        $token = ZbpAdminNext_ApiCsrfToken();

        $this->assertNotSame('', $token);
        $this->assertTrue($zbp->VerifyCSRFToken($token, 'api'));
    }

    /** @test */
    public function testAdminNextEntryForcesCsrfValidation()
    {
        $source = (string) file_get_contents(ZBP_PATH . 'zb_system/admin-next/api.php');

        $this->assertStringContainsString(
            "ApiVerifyCSRF(!in_array(strtoupper(\$_SERVER['REQUEST_METHOD'] ?? ''), ['GET', 'HEAD', 'OPTIONS'], true));",
            $source,
        );
        $this->assertStringContainsString('if (!ZbpAdminNext_IsSameOriginRequest())', $source);
        $this->assertStringNotContainsString('HTTP_X_ZBP_ADMIN_SPA', $source);
        $this->assertStringNotContainsString('$GLOBALS[\'zbp_admin_spa_verified\']', $source);
    }

    /** @test */
    public function testSameOriginCheckFailsClosedWithoutOriginOrReferer()
    {
        global $zbp;

        $zbp->host = 'https://example.com/';
        unset($_SERVER['HTTP_ORIGIN'], $_SERVER['HTTP_REFERER']);

        $this->assertFalse(ZbpAdminNext_IsSameOriginRequest());
    }

    /** @test */
    public function testSameOriginCheckAcceptsOnlyMatchingOriginTuple()
    {
        global $zbp;

        $zbp->host = 'https://example.com/';
        $cases = [
            ['HTTP_ORIGIN' => 'https://example.com', true],
            ['HTTP_ORIGIN' => 'https://example.com:443', true],
            ['HTTP_ORIGIN' => 'http://example.com', false],
            ['HTTP_ORIGIN' => 'https://example.com.evil.test', false],
            ['HTTP_ORIGIN' => 'https://evil.test', false],
            ['HTTP_ORIGIN' => 'null', false],
            ['HTTP_ORIGIN' => 'not a url', false],
            ['HTTP_REFERER' => 'https://example.com/posts/edit?id=1', true],
            ['HTTP_REFERER' => 'https://evil.test/posts', false],
        ];

        foreach ($cases as $case) {
            unset($_SERVER['HTTP_ORIGIN'], $_SERVER['HTTP_REFERER']);
            foreach ($case as $key => $value) {
                if (is_string($key)) {
                    $_SERVER[$key] = $value;
                }
            }

            $this->assertSame(
                $case[0],
                ZbpAdminNext_IsSameOriginRequest(),
                json_encode($case, JSON_UNESCAPED_SLASHES),
            );
        }
    }

    /** @test */
    public function testSameOriginCheckUsesRefererOnlyWhenOriginIsEmpty()
    {
        global $zbp;

        $zbp->host = 'https://example.com/';
        $_SERVER['HTTP_ORIGIN'] = '';
        $_SERVER['HTTP_REFERER'] = 'https://example.com/posts';
        $this->assertTrue(ZbpAdminNext_IsSameOriginRequest());

        $_SERVER['HTTP_ORIGIN'] = 'https://evil.test';
        $this->assertFalse(ZbpAdminNext_IsSameOriginRequest());
    }

    private function assertCsrfRejected(): void
    {
        try {
            ApiVerifyCSRF(true);
        } catch (ZbpErrorException $error) {
            $this->assertSame(419, $error->getHttpCode());

            return;
        }

        $this->fail('admin-next SPA request passed without a valid CSRF token');
    }
}
