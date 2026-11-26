<?php

require_once dirname(__DIR__) . '/AdminNextBootstrap.php';

if (!defined('ZBP_PATH')) {
    // Load the framework once, before PHPUnit snapshots the output buffer level.
    zbpn_boot();
}

class CommentApiTestZbp extends ZBlogPHP
{
    public function AddBuildModule($moduleFileName, $parameters = null)
    {
    }

    public function BuildModule()
    {
    }

    public function SaveCache()
    {
        return true;
    }
}

class CommentApiTest extends PHPUnit\Framework\TestCase
{
    private $zbp;
    private $zbpBackup;
    private $actionsBackup;
    private $postBackup = [];
    private $getBackup = [];
    private $requestBackup = [];

    protected function setUp(): void
    {
        zbpn_boot();

        $this->zbpBackup = $GLOBALS['zbp'];
        $this->actionsBackup = $GLOBALS['actions'];
        $this->postBackup = $_POST;
        $this->getBackup = $_GET;
        $this->requestBackup = $_REQUEST;

        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite is required for comment API persistence tests.');
        }

        $zbp = new CommentApiTestZbp();
        $GLOBALS['zbp'] = $zbp;
        $this->zbp = $zbp;

        $db = new Database__PDO_SQLite();
        $this->assertTrue($db->Open([':memory:', 'zbp_']));
        $zbp->db = $db;
        $zbp->user = zbpn_test_member(['ID' => 1, 'Level' => 1]);
        $zbp->lang['error'][6] = 'Permission denied';
        $zbp->lang['error'][46] = 'Comment content is required';
        $zbp->lang['error'][97] = 'Comment not found';
        $zbp->lang['msg']['operation_succeed'] = 'Operation succeeded';

        $this->createTable('Post');
        $this->createTable('Comment');

        $post = new Post();
        $post->AuthorID = 99;
        $post->Title = 'Comment API test post';
        $post->Status = ZC_POST_STATUS_PUBLIC;
        $post->Type = ZC_POST_TYPE_ARTICLE;
        $post->Save();
        $zbp->AddCache($post);

        require_once ZBP_PATH . 'zb_system/api/comment.php';
    }

    protected function tearDown(): void
    {
        if ($this->zbp !== null) {
            $this->zbp->db->Close();
        }

        $_POST = $this->postBackup;
        $_GET = $this->getBackup;
        $_REQUEST = $this->requestBackup;
        $GLOBALS['actions'] = $this->actionsBackup;
        $GLOBALS['zbp'] = $this->zbpBackup;

        $this->zbp = null;
    }

    private function createTable(string $type): void
    {
        $columns = [];
        foreach ($this->zbp->datainfo[$type] as $key => $info) {
            $field = $info[0];
            if ('ID' === $key) {
                $columns[] = $field . ' integer primary key autoincrement';
            } elseif (in_array($info[1], ['integer', 'boolean'], true)) {
                $columns[] = $field . " integer not null default '0'";
            } else {
                $columns[] = $field . " text not null default ''";
            }
        }

        $this->zbp->db->QueryMulti(
            'CREATE TABLE ' . $this->zbp->table[$type] . ' (' . implode(', ', $columns) . ')'
        );
    }

    private function createComment(): Comment
    {
        $comment = new Comment();
        $comment->LogID = 1;
        $comment->RootID = 3;
        $comment->ParentID = 2;
        $comment->AuthorID = 42;
        $comment->Name = 'Original Author';
        $comment->Content = 'Original content';
        $comment->Email = 'author@example.com';
        $comment->HomePage = 'https://author.example.com/';
        $comment->PostTime = 1234567890;
        $comment->IP = '192.0.2.42';
        $comment->Agent = 'Original agent';
        $comment->IsChecking = true;
        $comment->Metas->source = 'api-test';
        $comment->Save();
        $this->zbp->AddCache($comment);

        return $comment;
    }

    private function setRequest(array $data): void
    {
        $data['csrf_token'] = $this->zbp->GetCSRFToken('api');
        $_POST = $data;
        $_GET = [];
        // ApiLoadPostData() 只合并 JSON body 到 $_POST；不要同时填充
        // $_REQUEST，否则会掩盖 comment.put 的真实 CSRF 读取路径错误。
        $_REQUEST = [];
    }

    private function commentCount(): int
    {
        $rows = $this->zbp->db->Query(
            'SELECT COUNT(*) AS count FROM ' . $this->zbp->table['Comment']
        );

        return (int) $rows[0]['count'];
    }

    public function testPutUpdatesInPlaceAndPreservesImmutableCommentData(): void
    {
        $comment = $this->createComment();
        $id = (int) $comment->ID;
        $countBefore = $this->commentCount();
        $metaBefore = $comment->Meta;

        $this->setRequest([
            'ID' => $id,
            'Content' => 'Updated content',
            'AuthorID' => 1,
            'PostTime' => 9999999999,
            'Meta' => serialize(['source' => 'changed']),
            'IsChecking' => 0,
            'LogID' => 999,
            'RootID' => 999,
            'ParentID' => 999,
            'IP' => '192.0.2.1',
            'Agent' => 'Changed agent',
        ]);

        $result = api_comment_put();

        $this->assertSame(200, $result['code'] ?? 200);
        $this->assertSame($id, (int) $result['data']['comment']['ID']);
        $this->assertSame($countBefore, $this->commentCount());

        $stored = new Comment();
        $this->assertTrue($stored->LoadInfoByID($id));
        $this->assertSame('Updated content', $stored->Content);
        $this->assertSame(42, (int) $stored->AuthorID);
        $this->assertSame('Original Author', $stored->Name);
        $this->assertSame(1234567890, (int) $stored->PostTime);
        $this->assertSame($metaBefore, $stored->Meta);
        $this->assertSame(['source' => 'api-test'], $stored->Metas->GetData());
        $this->assertTrue((bool) $stored->IsChecking);
        $this->assertSame(1, (int) $stored->LogID);
        $this->assertSame(3, (int) $stored->RootID);
        $this->assertSame(2, (int) $stored->ParentID);
        $this->assertSame('192.0.2.42', $stored->IP);
        $this->assertSame('Original agent', $stored->Agent);
    }

    public function testPutCanUpdateEditableAuthorDetailsWithoutChangingAuthorId(): void
    {
        $comment = $this->createComment();
        $this->setRequest([
            'id' => $comment->ID,
            'Content' => 'Updated content',
            'Name' => 'Updated Display Name',
            'Email' => 'updated@example.com',
            'HomePage' => 'https://updated.example.com/',
            'AuthorID' => 1,
        ]);

        $result = api_comment_put();

        $this->assertSame(200, $result['code'] ?? 200);
        $stored = new Comment();
        $this->assertTrue($stored->LoadInfoByID($comment->ID));
        $this->assertSame('UpdatedDisplayName', $stored->Name);
        $this->assertSame('updated@example.com', $stored->Email);
        $this->assertSame('https://updated.example.com/', $stored->HomePage);
        $this->assertSame(42, (int) $stored->AuthorID);
        $this->assertSame(1, $this->commentCount());
    }

    public function testPutRequiresCommentEdtRight(): void
    {
        $comment = $this->createComment();
        $this->zbp->actions['CommentEdt'] = 0;
        $this->setRequest(['ID' => $comment->ID, 'Content' => 'Updated']);

        $this->expectException(ZbpErrorException::class);
        api_comment_put();
    }

    public function testPutRequiresCommentSavRight(): void
    {
        $comment = $this->createComment();
        $this->zbp->actions['CommentSav'] = 0;
        $this->setRequest(['ID' => $comment->ID, 'Content' => 'Updated']);

        $this->expectException(ZbpErrorException::class);
        api_comment_put();
    }

    public function testPutRejectsForeignCommentWithoutCommentAllRight(): void
    {
        $comment = $this->createComment();
        $this->zbp->user = zbpn_test_member(['ID' => 1, 'Level' => 5]);
        $this->setRequest(['ID' => $comment->ID, 'Content' => 'Updated']);

        $result = api_comment_put();

        $this->assertSame(403, $result['code']);
        $stored = new Comment();
        $stored->LoadInfoByID($comment->ID);
        $this->assertSame('Original content', $stored->Content);
    }

    public function testPutAllowsPostAuthorWithoutCommentAllRight(): void
    {
        $comment = $this->createComment();
        $this->zbp->user = zbpn_test_member(['ID' => 99, 'Level' => 5]);
        $this->setRequest(['ID' => $comment->ID, 'Content' => 'Updated by post author']);

        $result = api_comment_put();

        $this->assertSame(200, $result['code'] ?? 200);
        $stored = new Comment();
        $stored->LoadInfoByID($comment->ID);
        $this->assertSame('Updated by post author', $stored->Content);
        $this->assertSame(42, (int) $stored->AuthorID);
    }

    public function testPutReturnsNotFoundForMissingComment(): void
    {
        $this->setRequest(['ID' => 999, 'Content' => 'Updated']);

        $result = api_comment_put();

        $this->assertSame(404, $result['code']);
        $this->assertSame(0, $this->commentCount());
    }
}
