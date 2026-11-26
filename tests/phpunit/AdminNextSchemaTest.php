<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/AdminNextBootstrap.php';

zbpn_boot();

require_once ZBP_PATH . 'zb_system/admin-next/bootstrap/schema.php';

/**
 * 仅供 schema 预检使用的内存数据库替身.
 */
class AdminNextSchemaTestDatabase
{
    public array $missingColumns = [];
    public array $checks = [];

    public function ExistColumn($table, $field)
    {
        $this->checks[] = [$table, $field];

        return !in_array($field, $this->missingColumns, true);
    }
}

/**
 * admin-next 缺列预检、阻断页和写请求冲突测试.
 *
 * @internal
 *
 * @coversNothing
 */
class AdminNextSchemaTest extends PHPUnit\Framework\TestCase
{
    private $server = [];
    private $db;
    private $user;
    private $host;
    private $name;
    private $httpStatus;

    protected function setUp(): void
    {
        global $zbp;

        zbpn_boot();

        $this->server = $_SERVER;
        $this->db = $zbp->db;
        $this->user = $zbp->user;
        $this->host = $zbp->host;
        $this->name = $zbp->name;
        $this->httpStatus = http_response_code();
    }

    protected function tearDown(): void
    {
        global $zbp;

        $_SERVER = $this->server;
        $zbp->db = $this->db;
        $zbp->user = $this->user;
        $zbp->host = $this->host;
        $zbp->name = $this->name;
        http_response_code($this->httpStatus ?: 200);
    }

    public function testRequirementsCoverEveryCurrentDatainfoColumn(): void
    {
        global $zbp;

        $requirements = ZbpAdminNext_SchemaRequirements();
        $expectedCount = 0;

        foreach (['Post', 'Category', 'Tag', 'Member'] as $entity) {
            foreach ($zbp->datainfo[$entity] as $field => $definition) {
                $label = $entity . '.' . $definition[0];
                $this->assertArrayHasKey($label, $requirements);
                $this->assertSame($entity, $requirements[$label]['entity']);
                $this->assertSame($field, $requirements[$label]['field']);
                $this->assertSame($zbp->table[$entity], $requirements[$label]['table']);
                ++$expectedCount;
            }
        }

        $this->assertCount($expectedCount, $requirements);
        $this->assertArrayHasKey('Category.cate_CreateTime', $requirements);
        $this->assertArrayHasKey('Category.cate_UpdateTime', $requirements);
        $this->assertArrayHasKey('Category.cate_PostTime', $requirements);
        $this->assertArrayHasKey('Tag.tag_CreateTime', $requirements);
        $this->assertArrayHasKey('Tag.tag_UpdateTime', $requirements);
        $this->assertArrayHasKey('Tag.tag_PostTime', $requirements);
    }

    public function testWriteRequestReportsMissingColumnsAsSchemaConflict(): void
    {
        global $zbp;

        $db = new AdminNextSchemaTestDatabase();
        $db->missingColumns = [
            'log_CreateTime',
            'cate_CreateTime',
            'cate_UpdateTime',
            'cate_PostTime',
            'tag_CreateTime',
            'tag_UpdateTime',
            'tag_PostTime',
            'mem_CreateTime',
        ];
        $zbp->db = $db;
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $message = ZbpAdminNext_WriteSchemaError();

        $this->assertNotNull($message);
        foreach ($db->missingColumns as $column) {
            $this->assertStringContainsString($column, $message);
        }
        $this->assertStringContainsString('数据库结构不完整', $message);
        $this->assertCount(count(ZbpAdminNext_SchemaRequirements()), $db->checks);
    }

    public function testReadRequestAndCompleteWriteSchemaAreNotBlocked(): void
    {
        global $zbp;

        $db = new AdminNextSchemaTestDatabase();
        $zbp->db = $db;

        foreach (['GET', 'HEAD', 'OPTIONS'] as $method) {
            $_SERVER['REQUEST_METHOD'] = $method;
            $this->assertNull(ZbpAdminNext_WriteSchemaError());
        }
        $this->assertSame([], $db->checks, 'Read requests must not run the write-schema preflight.');

        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $this->assertNull(ZbpAdminNext_WriteSchemaError());
        $this->assertCount(count(ZbpAdminNext_SchemaRequirements()), $db->checks);
    }

    public function testRootSeesDirectRepairLinkOnBlockingPage(): void
    {
        global $zbp;

        $zbp->user = zbpn_test_member(['Level' => 1]);
        $zbp->host = 'https://example.test/';
        $zbp->name = '<Schema Test>';

        $html = $this->renderBlock(['Category.cate_PostTime']);

        $this->assertSame(409, http_response_code());
        $this->assertStringContainsString('https://example.test/zb_system/admin/updatedb.php?updatedb=1', $html);
        $this->assertStringContainsString('运行数据库升级修复', $html);
        $this->assertStringContainsString('Category.cate_PostTime', $html);
        $this->assertStringContainsString('&lt;Schema Test&gt;', $html);
    }

    public function testNonRootIsToldToContactAdministratorWithoutRepairLink(): void
    {
        global $zbp;

        $zbp->user = zbpn_test_member(['Level' => 5]);

        $html = $this->renderBlock(['Tag.tag_CreateTime']);

        $this->assertStringNotContainsString('updatedb.php', $html);
        $this->assertStringContainsString('请联系管理员', $html);
    }

    public function testEntryPointsRunSchemaGuardBeforeRenderingOrDispatch(): void
    {
        $spa = (string) file_get_contents(ZBP_PATH . 'zb_system/admin-next/index.php');
        $api = (string) file_get_contents(ZBP_PATH . 'zb_system/admin-next/api.php');
        $updatedb = (string) file_get_contents(ZBP_PATH . 'zb_system/admin/updatedb.php');

        $this->assertStringContainsString("require __DIR__ . '/bootstrap/schema.php';", $spa);
        $this->assertStringContainsString('$missingSchemaColumns = ZbpAdminNext_MissingSchemaColumns();', $spa);
        $this->assertStringContainsString('ZbpAdminNext_RenderSchemaBlock($missingSchemaColumns);', $spa);

        $this->assertStringContainsString("require __DIR__ . '/bootstrap/schema.php';", $api);
        $this->assertStringContainsString('$schemaError = ZbpAdminNext_WriteSchemaError();', $api);
        $this->assertStringContainsString('ZbpAdminNext_JsonExit(409, $schemaError);', $api);
        $this->assertStringContainsString("isset(\$_GET['updatedb'])", $updatedb);
        $this->assertLessThan(
            strpos($api, 'ApiDispatch('),
            strpos($api, '$schemaError = ZbpAdminNext_WriteSchemaError();'),
        );
    }

    private function renderBlock(array $missing): string
    {
        ob_start();

        try {
            ZbpAdminNext_RenderSchemaBlock($missing);
        } finally {
            $html = (string) ob_get_clean();
        }

        return $html;
    }
}
