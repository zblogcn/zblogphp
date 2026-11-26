<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/AdminNextBootstrap.php';

zbpn_boot();

require_once ZBP_PATH . 'zb_system/function/c_system_updatedb.php';

require_once ZBP_PATH . 'zb_system/admin-next/bootstrap/schema.php';

/**
 * 拦截配置保存，确保迁移测试不接触站点 c_option.php 或配置表.
 */
class UpdateDbMigrationTestZBlogPHP extends ZBlogPHP
{
    public int $saveOptionCalls = 0;

    public function SaveOption()
    {
        ++$this->saveOptionCalls;

        return true;
    }
}

/**
 * 旧库迁移回归测试.
 *
 * 每个用例都创建并删除独立临时 SQLite 库，不连接现有测试站点。
 *
 * @internal
 *
 * @coversNothing
 */
class UpdateDbMigrationTest extends PHPUnit\Framework\TestCase
{
    private $originalZbp;
    private $originalDb;
    private $originalVersion;
    private $hadVersion = false;
    private $db;
    private $databaseFile;

    protected function setUp(): void
    {
        global $zbp;

        zbpn_boot();

        if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('PDO SQLite driver is required for the isolated migration test.');
        }

        $this->originalZbp = $zbp;
        $this->originalDb = $zbp->db;
        $this->hadVersion = array_key_exists('ZC_LAST_VERSION', $zbp->option);
        $this->originalVersion = $zbp->option['ZC_LAST_VERSION'] ?? null;

        $this->databaseFile = tempnam(sys_get_temp_dir(), 'zbp-updatedb-');
        if (false === $this->databaseFile) {
            $this->fail('Unable to create a temporary SQLite database.');
        }

        $this->db = new Database__PDO_SQLite();
        $this->assertTrue($this->db->Open([$this->databaseFile, 'zbp_']));

        $zbp = new UpdateDbMigrationTestZBlogPHP();
        $GLOBALS['zbp'] = $zbp;
        $zbp->db = $this->db;
        $zbp->option['ZC_LAST_VERSION'] = 162000;

        $this->createLegacyTables();
    }

    protected function tearDown(): void
    {
        global $zbp;

        if (null !== $this->db) {
            $this->db->Close();
        }

        if (is_string($this->databaseFile)) {
            foreach ([$this->databaseFile, $this->databaseFile . '-journal', $this->databaseFile . '-wal', $this->databaseFile . '-shm'] as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }

        if (null !== $this->originalZbp) {
            $zbp = $this->originalZbp;
            $GLOBALS['zbp'] = $zbp;
            $zbp->db = $this->originalDb;

            if ($this->hadVersion) {
                $zbp->option['ZC_LAST_VERSION'] = $this->originalVersion;
            } else {
                unset($zbp->option['ZC_LAST_VERSION']);
            }
        }
    }

    public function testMigrationAddsLegacyColumnsAndAdvancesVersion(): void
    {
        global $zbp;

        updatedb();

        $this->assertSame((int) ZC_LAST_VERSION, (int) $zbp->option['ZC_LAST_VERSION']);
        $this->assertSame(1, $zbp->saveOptionCalls);
        $this->assertSame([], ZbpAdminNext_MissingSchemaColumns());

        foreach ([
            'Category' => ['cate_CreateTime', 'cate_UpdateTime', 'cate_PostTime'],
            'Tag'      => ['tag_CreateTime', 'tag_UpdateTime', 'tag_PostTime'],
            'Member'   => ['mem_CreateTime', 'mem_UpdateTime'],
            'Post'     => ['log_CreateTime', 'log_UpdateTime'],
        ] as $entity => $columns) {
            foreach ($columns as $column) {
                $this->assertTrue(
                    $this->db->ExistColumn($zbp->table[$entity], $column),
                    $entity . '.' . $column . ' was not added',
                );
            }
        }

        $rows = $this->db->Query('SELECT log_UpdateTime FROM ' . $this->tableName('Post') . ' WHERE log_ID = 1');
        $this->assertSame(1700000000, (int) $rows[0]['log_UpdateTime']);
    }

    public function testMigrationCanRunRepeatedlyWithoutChangingData(): void
    {
        global $zbp;

        updatedb();
        updatedb();

        $this->assertSame((int) ZC_LAST_VERSION, (int) $zbp->option['ZC_LAST_VERSION']);
        $this->assertSame(2, $zbp->saveOptionCalls);
        $this->assertSame([], ZbpAdminNext_MissingSchemaColumns());

        $rows = $this->db->Query('SELECT log_UpdateTime FROM ' . $this->tableName('Post') . ' WHERE log_ID = 1');
        $this->assertSame(1700000000, (int) $rows[0]['log_UpdateTime']);
    }

    public function testAddDdlFailureStopsMigrationWithoutAdvancingVersion(): void
    {
        global $zbp;

        $this->db->Query('DROP TABLE ' . $this->tableName('Member'));

        try {
            updatedb();
            $this->fail('Migration should stop when an ADD COLUMN statement fails.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('失败', $e->getMessage());
            $this->assertStringContainsString('mem_CreateTime', $e->getMessage());
        }

        $this->assertSame(162000, (int) $zbp->option['ZC_LAST_VERSION']);
        $this->assertSame(0, $zbp->saveOptionCalls);
        $this->assertTrue(
            $this->db->ExistColumn($zbp->table['Post'], $zbp->datainfo['Post']['CreateTime'][0]),
            'Earlier migration steps should have completed before the failure.',
        );
    }

    public function testModifyDdlFailureDoesNotAdvanceVersion(): void
    {
        global $zbp;

        // 先在 SQLite 上补齐字段，再让历史 MySQL MODIFY 分支在 SQLite 上
        // 真实失败，以覆盖不同于 ADD 的 DDL 返回路径。
        updatedb();
        $zbp->option['ZC_LAST_VERSION'] = 162314;
        $zbp->saveOptionCalls = 0;
        $this->db->type = 'mysql';

        try {
            updatedb();
            $this->fail('Migration should stop when a MODIFY COLUMN statement fails.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('失败', $e->getMessage());
            $this->assertStringContainsString('log_Type', $e->getMessage());
        }

        $this->assertSame(162314, (int) $zbp->option['ZC_LAST_VERSION']);
        $this->assertSame(0, $zbp->saveOptionCalls);
    }

    /**
     * 创建旧版结构：保留当前 datainfo 的其余列，只省略历史迁移会补齐的列.
     */
    private function createLegacyTables(): void
    {
        global $zbp;

        $migrationFields = [
            'Config'   => ['Key'],
            'Post'     => ['CreateTime', 'UpdateTime'],
            'Category' => ['Type', 'Group', 'CreateTime', 'UpdateTime', 'PostTime'],
            'Tag'      => ['Type', 'Group', 'CreateTime', 'UpdateTime', 'PostTime'],
            'Member'   => ['CreateTime', 'UpdateTime'],
        ];

        foreach ($migrationFields as $entity => $omittedFields) {
            $definitions = [];

            foreach ($zbp->datainfo[$entity] as $field => $definition) {
                if (in_array($field, $omittedFields, true)) {
                    continue;
                }

                $column = (string) $definition[0];
                if ('ID' === $field) {
                    $sqlType = 'INTEGER PRIMARY KEY';
                } elseif (in_array($definition[1] ?? '', ['integer', 'boolean'], true)) {
                    $sqlType = 'INTEGER NOT NULL DEFAULT 0';
                } elseif (($definition[1] ?? '') === 'float') {
                    $sqlType = 'REAL NOT NULL DEFAULT 0';
                } else {
                    $sqlType = "TEXT NOT NULL DEFAULT ''";
                }

                $definitions[] = '"' . $column . '" ' . $sqlType;
            }

            $this->db->Query('CREATE TABLE "' . $this->tableName($entity) . '" (' . implode(', ', $definitions) . ')');
        }

        $this->assertSame([], $this->db->error, 'Temporary schema setup should not produce database errors.');
        $this->db->Query('INSERT INTO ' . $this->tableName('Post') . ' (log_ID, log_PostTime) VALUES (1, 1700000000)');
    }

    private function tableName(string $entity): string
    {
        global $zbp;

        return str_replace('%pre%', $this->db->dbpre, $zbp->table[$entity]);
    }
}
