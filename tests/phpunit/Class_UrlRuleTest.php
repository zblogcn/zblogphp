<?php
/**
 * Useless test
 * only to require mock.
 */
//require_once './tests/CommandMock.php';
//commandmock_loadzbp();

class Class_UrlRuleTest extends PHPUnit\Framework\TestCase
{
    public function testUseless()
    {
        UrlRule::$categoryLayer = 4;
        $s = '{%host%}{%category%}/{%alias%}.html';
        $s0 = '/(?J)^(?P<category>([^\.\/_]*\/?){1,4})\/(?P<alias>[^\/]+)\.html$/';
        $s1 = UrlRule::OutputUrlRegEx_V2($s, 'article', true, false);
        $s2 = UrlRule::OutputUrlRegEx($s, 'article', true, false);
        $this->assertEquals($s0, $s1);
        //$this->assertEquals($s0, $s2);

        $s = '{%host%}';
        $s0 = '';
        $s00 = '/(?J)^$/';
        $s1 = UrlRule::OutputUrlRegEx_V2($s, 'list', true, false);
        $s2 = UrlRule::OutputUrlRegEx($s, 'list', true, false);
        $this->assertEquals($s0, $s1);
        $this->assertEquals($s00, $s2);

        $s = '';
        $s0 = '';
        $s00 = '/(?J)^$/';
        $s1 = UrlRule::OutputUrlRegEx_V2($s, 'list', true, false);
        $s2 = UrlRule::OutputUrlRegEx($s, 'list', true, false);
        $this->assertEquals($s0, $s1);
        $this->assertEquals($s00, $s2);

    }
}
