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
        $s0 = '/(?J)^(?P<category>([^\.\/_]*\/?){1,4})\/(?P<alias>[^\/]+)\.html$/';
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
        $s = '{%host%}page_{%page%}.html';
        $s0 = '/(?J)^page_(?P<page>[0-9]+)\.html$/';
        $s1 = UrlRule::OutputUrlRegEx_V2($s, 'list', true, false);
        $s2 = UrlRule::OutputUrlRegEx($s, 'list', true);
        $this->assertEquals($s0, $s1);
        $s0 = '/(?J)^page_(?P<page>[0-9]*)\.html$/';
        $this->assertEquals($s0, $s2);

        UrlRule::$categoryLayer = 4;
        $s = '{%host%}page_{%page%}.html';
        $s0 = '/(?J)^page_(?P<page>[0-9]+)\.html$/';
        $s1 = UrlRule::OutputUrlRegEx_V2($s, 'list', true, true);
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
