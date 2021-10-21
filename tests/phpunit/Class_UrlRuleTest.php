<?php
/**
 * Useless test
 * only to require mock.
 */
//require_once './tests/CommandMock.php';
//commandmock_loadzbp();

class Class_UrlRuleTest extends PHPUnit\Framework\TestCase
{
    public function testRewrite()
    {
        UrlRule::$categoryLayer = 4;
        $s = '{%host%}{%category%}/{%alias%}.html';
        $s0 = '/(?J)^(?P<category>.+)\/(?P<alias>.+)\.html$/';
        $s1 = UrlRule::OutputUrlRegEx_V2($s, 'article', true, false);
        $s2 = UrlRule::OutputUrlRegEx($s, 'article', true, false);
        $this->assertEquals($s0, $s1);

        $s = '{%host%}';
        $s0 = '';
        $s00 = '/(?J)^$/';
        $s1 = UrlRule::OutputUrlRegEx_V2($s, 'list', true, false);
        $s2 = UrlRule::OutputUrlRegEx($s, 'list', true, false);
        $this->assertEquals($s0, $s1);
        $this->assertEquals($s00, $s2);

        $s = '{%host%}?page={%page%}';
        $s0 = '/(?J)^?page=(?P<page>[0-9]*)$/';
        $s02 = '/(?J)^\?page=(?P<page>[0-9]+)$/';
        $s03 = '/(?J)^\?page=(?P<page>[0-9]+)$/';
        $s1 = UrlRule::OutputUrlRegEx($s, 'list', true);
        $s2 = UrlRule::OutputUrlRegEx_V2($s, 'list', true, false);
        $s3 = UrlRule::OutputUrlRegEx_Route(array('urlrule'=>$s), true);
        $this->assertEquals($s0, $s1);
        $this->assertEquals($s02, $s2);
        $this->assertEquals($s03, $s3);

        $s = '';
        $s0 = '';
        $s00 = '/(?J)$/';
        $s1 = UrlRule::OutputUrlRegEx_V2($s, 'list', true, false);
        $s2 = UrlRule::OutputUrlRegEx($s, 'list', true, false);
        $this->assertEquals($s0, $s1);
        $this->assertEquals($s00, $s2);

    }

    public function testRewrite2()
    {
        UrlRule::$categoryLayer = 4;
        $s = '{%host%}page_{%page%}.html';
        $s0 = '/(?J)^page\.html$/';
        $s1 = UrlRule::OutputUrlRegEx_V2($s, 'list', false, false);
        $s2 = UrlRule::OutputUrlRegEx($s, 'list', false);
        $this->assertEquals($s0, $s1);
        $this->assertEquals($s0, $s2);

        UrlRule::$categoryLayer = 4;
        $s = '{%host%}category/{%alias%}_page_{%page%}.html';
        $s0 = '/(?J)^category\/(?P<cate>.+)_page_(?P<page>[0-9]+)\.html$/';
        $s1 = UrlRule::OutputUrlRegEx_V2($s, 'cate', true, false);
        $this->assertEquals($s0, $s1);

        $s00 = '/(?J)^category\/(?P<alias>([^\.\/_]*|[^\.\/_]*\/[^\.\/_]*|[^\.\/_]*\/[^\.\/_]*\/[^\.\/_]*|[^\.\/_]*\/[^\.\/_]*\/[^\.\/_]*\/[^\.\/_]*)+?)(?:_page_)(?P<page>[0-9]*)\.html$/';
        $s2 = UrlRule::OutputUrlRegEx($s, 'cate', true);
        $this->assertEquals($s00, $s2);

        UrlRule::$categoryLayer = 4;
        $s = '{%host%}page_{%page%}.html';
        $s0 = '/(?J)^page_(?P<page>[0-9]+)\.html$/';
        $s1 = UrlRule::OutputUrlRegEx_V2($s, 'list', true, true);
        $this->assertEquals($s0, $s1);

    }

    public function testRewrite3()
    {
        UrlRule::$categoryLayer = 4;
        $s = '{%host%}tags/{%alias%}/{%page%}.html';
        $s0 = '/(?J)^tags\/(?P<tags>[^\.\/_]+)\/(?P<page>[0-9]+)\.html$/';
        $s1 = UrlRule::OutputUrlRegEx_V2($s, 'tags', true, false);
        $this->assertEquals($s0, $s1);

        $s00 = '/(?J)^tags\/(?P<alias>[^\.\/_]+)(?:\/)(?P<page>[0-9]*)\.html$/';
        $s2 = UrlRule::OutputUrlRegEx($s, 'tags', true, false);
        $this->assertEquals($s00, $s2);
    }

    public function testRewrite4()
    {
        UrlRule::$categoryLayer = 4;
        $s = '{%host%}tags/{%alias%}/{%page%}.html';
        $s0 = '/(?J)^tags\/(?P<tags>[^\.\/_]+)\.html$/';
        $s1 = UrlRule::OutputUrlRegEx_V2($s, 'tags', false, false);
        $this->assertEquals($s0, $s1);

        $s00 = '/(?J)^tags\/(?P<alias>[^\.\/_]+)\.html$/';
        $s2 = UrlRule::OutputUrlRegEx($s, 'tags', false, false);
        $this->assertEquals($s00, $s2);
    }

    public function testRewrite5()
    {
        UrlRule::$categoryLayer = 4;
        $s = '{%host%}author/{%alias%}/{%page%}.html';
        $s0 = '/(?J)^author\/(?P<auth>[^\.\/_]+)\.html$/';
        $s1 = UrlRule::OutputUrlRegEx_V2($s, 'auth', false, false);
        $this->assertEquals($s0, $s1);

        $s00 = '/(?J)^author\/(?P<alias>[^\.\/_]+)\.html$/';
        $s2 = UrlRule::OutputUrlRegEx($s, 'auth', false, false);
        $this->assertEquals($s00, $s2);
    }

    public function testRewrite6()
    {
        UrlRule::$categoryLayer = 4;
        $route = array('type' => 'rewrite', 'name' => 'ddd', 'urlrule'=>'{%host%}tags/{%alias%}/{%page%}.html', 'args' => array('tags@id', 'tags@alias'=>'.+', 'page'));

        $s0 = '/(?J)^tags\/(?P<alias>.+)\/(?P<page>[0-9]+)\.html$/';
        $s1 = UrlRule::OutputUrlRegEx_Route($route, true, false);
        $this->assertEquals($s0, $s1);

    }


    public function testActive_new()
    {
        $zbp= $GLOBALS['zbp'];
        $zbp->host = 'https://localhost/';
        $this->assertEquals($zbp->host, 'https://localhost/');

        $p = new Pagebar('{%host%}zb_system/cmd.php?act=CategoryMng{&type=%type%}{&search=%search%}{&page=%page%}', false);
        $p->PageCount = 100;
        $p->PageNow = 1;
        $p->PageBarCount = 10;

        $s = $p->UrlRule->Make();
        $s0 = 'https://localhost/zb_system/cmd.php?act=CategoryMng';

        $this->assertEquals($s, $s0);
    }

    public function testActive_old()
    {
        $zbp= $GLOBALS['zbp'];
        $zbp->host = 'https://localhost/';
        $this->assertEquals($zbp->host, 'https://localhost/');

        $p = new Pagebar('{%host%}zb_system/cmd.php?act=ArticleMng{&status=%status%}{&istop=%istop%}{&category=%category%}{&search=%search%}{&page=%page%}', true);
        $p->PageCount = 100;
        $p->PageNow = 1;
        $p->PageBarCount = 10;

        $p->UrlRule->Rules['{%category%}'] = 5;
        $p->UrlRule->Rules['{%search%}'] = rawurlencode('abc');
        $p->UrlRule->Rules['{%status%}'] = 1;
        $p->UrlRule->Rules['{%istop%}'] = 2;
        $p->UrlRule->Rules['{%page%}'] = 11;
        $s = $p->UrlRule->Make();
        $s0 = 'https://localhost/zb_system/cmd.php?act=ArticleMng&amp;status=1&amp;istop=2&amp;category=5&amp;search=abc&amp;page=11';
        $this->assertEquals($s, $s0);


        $p->UrlRule->Rules['{%page%}'] = 1;
        $s0 = 'https://localhost/zb_system/cmd.php?act=ArticleMng&amp;status=1&amp;istop=2&amp;category=5&amp;search=abc';
        $s = $p->UrlRule->Make();
        $this->assertEquals($s, $s0);

    }

    public function testActive_old2()
    {
        $zbp= $GLOBALS['zbp'];
        $zbp->host = 'https://localhost/';

        $p = new Pagebar('{%host%}zb_system/cmd.php?act=ArticleMng{&status=%status%}{&istop=%istop%}{&category=%category%}{&search=%search%}{&page=%page%}', true);
        $p->PageCount = 100;
        $p->PageNow = 1;
        $p->PageBarCount = 10;

        $p->UrlRule->Rules['{%category%}'] = null;
        $p->UrlRule->Rules['{%search%}'] = null;
        $p->UrlRule->Rules['{%status%}'] = null;
        $p->UrlRule->Rules['{%istop%}'] = null;
        $p->UrlRule->Rules['{%page%}'] = 11;
        $s = $p->UrlRule->Make();
        $s0 = 'https://localhost/zb_system/cmd.php?act=ArticleMng&amp;page=11';
        $this->assertEquals($s, $s0);


        $p->UrlRule->Rules['{%page%}'] = 1;
        $s0 = 'https://localhost/zb_system/cmd.php?act=ArticleMng';
        $s = $p->UrlRule->Make();
        $this->assertEquals($s, $s0);
    }

    public function testActive_old3()
    {
        $zbp= $GLOBALS['zbp'];
        $zbp->host = 'https://localhost/';

        $p = new Pagebar('{%host%}zb_system/cmd.php?act=ArticleMng&aaa=dog&fddd={&status=%status%}&zzzz=&ggg=pig{&page=%page%}', true, true);
        $p->PageCount = 100;
        $p->PageNow = 1;
        $p->PageBarCount = 10;

        $p->UrlRule->Rules['{%category%}'] = null;
        $p->UrlRule->Rules['{%search%}'] = null;
        $p->UrlRule->Rules['{%status%}'] = null;
        $p->UrlRule->Rules['{%istop%}'] = null;
        $p->UrlRule->Rules['{%page%}'] = 11;
        $s = $p->UrlRule->Make();
        $s0 = 'https://localhost/zb_system/cmd.php?act=ArticleMng&amp;aaa=dog&amp;ggg=pig&amp;page=11';
        $this->assertEquals($s, $s0);


        $p->UrlRule->Rules['{%page%}'] = 1;
        $s0 = 'https://localhost/zb_system/cmd.php?act=ArticleMng&amp;aaa=dog&amp;ggg=pig';
        $s = $p->UrlRule->Make();
        $this->assertEquals($s, $s0);
    }

    public function testActive_old4()
    {
        $zbp= $GLOBALS['zbp'];
        $zbp->host = 'https://localhost/';

        $p = new Pagebar('{%host%}zb_system/cmd.php?act=ArticleMng&aaa=dog&fddd={&status=%status%}&zzzz=&ggg=pig{&page=%page%}', true, false, true);
        $p->PageCount = 100;
        $p->PageNow = 1;
        $p->PageBarCount = 10;

        $p->UrlRule->Rules['{%category%}'] = null;
        $p->UrlRule->Rules['{%search%}'] = null;
        $p->UrlRule->Rules['{%status%}'] = null;
        $p->UrlRule->Rules['{%istop%}'] = null;
        $p->UrlRule->Rules['{%page%}'] = 11;
        $s = $p->UrlRule->Make();
        $s0 = 'https://localhost/zb_system/cmd.php?act=ArticleMng&amp;aaa=dog&amp;ggg=pig&amp;page=11';
        $this->assertEquals($s, $s0);


        $p->UrlRule->Rules['{%page%}'] = 1;
        $s0 = 'https://localhost/zb_system/cmd.php?act=ArticleMng&amp;aaa=dog&amp;ggg=pig&amp;page=1';
        $s = $p->UrlRule->Make();
        $this->assertEquals($s, $s0);
    }
}
