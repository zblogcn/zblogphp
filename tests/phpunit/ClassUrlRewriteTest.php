<?php
class ClassUrlRewriteTest extends PHPUnit_Framework_TestCase
{
    protected $backupGlobalsBlacklist = ['zbp'];
    protected $testRules = [
        'article' => [
            ['{%host%}post/{%id%}.html',
                ['post/1.html' => ['id' => '1']]
            ],
            ['{%host%}post/{%alias%}.html',
                ['post/alias.html' => ['alias' => 'alias']]
            ],
            ['{%host%}{%year%}/{%month%}/{%id%}/',
                ['2016/11/233' => ['year' => '2016', 'month' => '11', 'id' => '233']]
            ],
            ['{%host%}{%category%}/{%alias%}',
                [
                    'category/alias' => ['category' => 'category', 'alias' => 'alias'],
                    'post/category/alias' => ['category' => 'post/category', 'alias' => 'alias']
                ]
            ]
        ],
        'page' => [
            ['{%host%}{%id%}.html',
                ['1.html' => ['id' => '1']]
            ],
            ['{%host%}{%alias%}.html',
                ['alias.html' => ['alias' => 'alias']]
            ],
            ['{%host%}{%alias%}/',
                ['alias' => ['alias' => 'alias']]
            ],
            ['{%host%}{%alias%}',
                ['alias' => ['alias' => 'alias']]
            ],
        ],
        'index' => [
            ['{%host%}page_{%page%}.html',
                ['page_1.html' => ['page' => '1']]
            ],
            ['{%host%}page_{%page%}/',
                ['page_1' => ['page' => '1']]
            ],
            ['{%host%}page_{%page%}',
                ['page_1' => ['page' => '1']]
            ],
        ],
        'cate' => [
            ['{%host%}category-{%id%}_{%page%}.html',
                [
                    'category-1.html' => ['id' => '1'],
                    'category-1_1.html' => ['id' => '1', 'page' => '1']
                ]
            ],
            ['{%host%}category-{%alias%}_{%page%}.html',
                [
                    'category-alias.html' => ['alias' => 'alias'],
                    'category-alias_1.html' => ['alias' => 'alias', 'page' => '1']
                ]
            ],
            ['{%host%}category/{%alias%}/{%page%}/',
                [
                    'category/alias' => ['alias' => 'alias'],
                    'category/alias/1' => ['alias' => 'alias', 'page' => '1']
                ]
            ],
            ['{%host%}category/{%id%}/{%page%}',
                [
                    'category/123' => ['id' => '123'],
                    //'category/alias/alias2/' => ['alias' => 'alias/alias2'],
                    'category/123/1' => ['id' => '123', 'page' => '1']
                ]
            ],
        ],
        'tags' => [
            ['{%host%}tags-{%id%}_{%page%}.html',
                [
                    'tags-23.html' => ['id' => '23'],
                    'tags-23_1.html' => ['id' => '23', 'page' => '1']
                ]
            ],
            ['{%host%}tags-{%alias%}_{%page%}.html',
                [
                    'tags-alias.html' => ['alias' => 'alias'],
                    'tags-alias_1.html' => ['alias' => 'alias', 'page' => '1']
                ]
            ],
        ],
        'date' => [
            ['{%host%}date-{%date%}_{%page%}.html',
                [
                    'date-2016-10.html' => ['date' => '2016-10'],
                    'date-2016-10_44.html' => ['page' => '44'],
                ]
            ],
            ['{%host%}post/{%date%}_{%page%}.html',
                [
                    'post/2016-10.html' => ['date' => '2016-10'],
                    'post/2016-10_44.html' => ['page' => '44'],
                ]
            ],
        ],
        'auth' => [
            ['{%host%}author-{%id%}_{%page%}.html',
                [
                    'author-23.html' => ['id' => '23'],
                    'author-23_1.html' => ['id' => '23', 'page' => '1']
                ]
            ],
            ['{%host%}author/{%id%}/{%page%}/',
                [
                    'author/23' => ['id' => '23'],
                    'author/23/2' => ['id' => '23', 'page' => '2']
                ]
            ],
        ]
    ];

    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    private function generateAndMatch($url, $regex, $type, $hasPage = false)
    {
        $m = [];
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
