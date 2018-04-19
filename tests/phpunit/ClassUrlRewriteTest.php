<?php

class ClassUrlRewriteTest extends PHPUnit_Framework_TestCase
{
    protected $backupGlobalsBlacklist = array('zbp');
    protected $testRules = array(
        'article' => array(
            array('{%host%}post/{%id%}.html',
                array('post/1.html' => array('id' => '1')),
            ),
            array('{%host%}post/{%alias%}.html',
                array('post/alias.html' => array('alias' => 'alias')),
            ),
            array('{%host%}{%year%}/{%month%}/{%id%}/',
                array('2016/11/233' => array('year' => '2016', 'month' => '11', 'id' => '233')),
            ),
            array('{%host%}{%category%}/{%alias%}',
                array(
                    'category/alias'      => array('category' => 'category', 'alias' => 'alias'),
                    'post/category/alias' => array('category' => 'post/category', 'alias' => 'alias'),
                ),
            ),
        ),
        'page' => array(
            array('{%host%}{%id%}.html',
                array('1.html' => array('id' => '1')),
            ),
            array('{%host%}{%alias%}.html',
                array('alias.html' => array('alias' => 'alias')),
            ),
            array('{%host%}{%alias%}/',
                array('alias' => array('alias' => 'alias')),
            ),
            array('{%host%}{%alias%}',
                array('alias' => array('alias' => 'alias')),
            ),
        ),
        'index' => array(
            array('{%host%}page_{%page%}.html',
                array('page_1.html' => array('page' => '1')),
            ),
            array('{%host%}page_{%page%}/',
                array('page_1' => array('page' => '1')),
            ),
            array('{%host%}page_{%page%}',
                array('page_1' => array('page' => '1')),
            ),
        ),
        'cate' => array(
            array('{%host%}category-{%id%}_{%page%}.html',
                array(
                    'category-1.html'   => array('id' => '1'),
                    'category-1_1.html' => array('id' => '1', 'page' => '1'),
                ),
            ),
            array('{%host%}category-{%alias%}_{%page%}.html',
                array(
                    'category-alias.html'   => array('alias' => 'alias'),
                    'category-alias_1.html' => array('alias' => 'alias', 'page' => '1'),
                ),
            ),
            array('{%host%}category/{%alias%}/{%page%}/',
                array(
                    'category/alias'   => array('alias' => 'alias'),
                    'category/alias/1' => array('alias' => 'alias', 'page' => '1'),
                ),
            ),
            array('{%host%}category/{%id%}/{%page%}',
                array(
                    'category/123' => array('id' => '123'),
                    //'category/alias/alias2/' => ['alias' => 'alias/alias2'],
                    'category/123/1' => array('id' => '123', 'page' => '1'),
                ),
            ),
        ),
        'tags' => array(
            array('{%host%}tags-{%id%}_{%page%}.html',
                array(
                    'tags-23.html'   => array('id' => '23'),
                    'tags-23_1.html' => array('id' => '23', 'page' => '1'),
                ),
            ),
            array('{%host%}tags-{%alias%}_{%page%}.html',
                array(
                    'tags-alias.html'   => array('alias' => 'alias'),
                    'tags-alias_1.html' => array('alias' => 'alias', 'page' => '1'),
                ),
            ),
        ),
        'date' => array(
            array('{%host%}date-{%date%}_{%page%}.html',
                array(
                    'date-2016-10.html'    => array('date' => '2016-10'),
                    'date-2016-10_44.html' => array('page' => '44'),
                ),
            ),
            array('{%host%}post/{%date%}_{%page%}.html',
                array(
                    'post/2016-10.html'    => array('date' => '2016-10'),
                    'post/2016-10_44.html' => array('page' => '44'),
                ),
            ),
        ),
        'auth' => array(
            array('{%host%}author-{%id%}_{%page%}.html',
                array(
                    'author-23.html'   => array('id' => '23'),
                    'author-23_1.html' => array('id' => '23', 'page' => '1'),
                ),
            ),
            array('{%host%}author/{%id%}/{%page%}/',
                array(
                    'author/23'   => array('id' => '23'),
                    'author/23/2' => array('id' => '23', 'page' => '2'),
                ),
            ),
        ),
    );

    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    private function generateAndMatch($url, $regex, $type, $hasPage = false)
    {
        $m = array();
        $r = UrlRule::OutputUrlRegEx($regex, $type, $hasPage);
        preg_match($r, $url, $m);

        return $m;
    }

    public function testRegExsSingle()
    {
        foreach ($this->testRules as $key => $rule) {
            foreach ($rule as $data) {
                foreach ($data[1] as $testUrl => $testSubset) {
                    $t = $this->generateAndMatch($testUrl, $data[0], $key, isset($testSubset['page']));
                    $this->assertArraySubset($testSubset, $t);
                }
            }
        }
    }

    /*
    public function testRegExsCross() { // TODO: Have a better way?
        foreach ($this->testRules as $key => $rule) {
            foreach ($rule as $data) {
                foreach ($data[1] as $testUrl => $testSubset) {
                    foreach ($this->testRules as $anotherKey => $anotherRules) {
                        if ($anotherKey == $key || $anotherKey == 'page' || $anotherKey == 'date') continue;
                        foreach ($anotherRules as $newData) {
                            $t = $this->generateAndMatch($testUrl, $newData[0], $anotherKey, isset($testSubset['page']));
                            var_dump($t);
                            var_dump($testUrl);
                            var_dump($newData[0]);
                            $this->assertEmpty($t);
                        }
                    }
                }
            }
        }
    }
    */

    private function getRegExpResult($str)
    {
    }
}
