<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = (new Finder())
    ->in(__DIR__)
    ->exclude(['node_modules', 'vendor'])
;

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        // PSR-12 基础编码规范。
        '@PSR12' => true,
        // PhpCsFixer 完整规则集。
        '@PhpCsFixer' => true,

        /* 后续显式覆盖或添加的规则 */

        // // 单行注释后确保有一个空格，然而实际没有这个规则，不知道是不是 PhpCsFixer 版本太低。
        // 'single_line_comment_spacing' => true,
        // 多行数组与参数列表保留尾随逗号。
        'trailing_comma_in_multiline' => ['elements' => ['arrays', 'arguments']],
        // 垂直对齐 PHPDoc 注解列。
        'phpdoc_align' => ['align' => 'vertical'],
        // 规范单行注释，禁用 # 形式。
        'single_line_comment_style' => ['comment_types' => ['hash']],
        // 设置缩进为 4 个空格（PSR 标准常用）
        'indentation_type' => true,
        // 移除行尾多余空格
        'no_trailing_whitespace' => true,
        // 确保文件以换行结束
        'single_blank_line_at_eof' => true,
        // 使用短数组语法 []，替代 array()。
        'array_syntax' => ['syntax' => 'short'],
        // 使用短列表语法 []。
        'list_syntax' => ['syntax' => 'short'],
        // 各种二元运算符周围使用单个空格，但保持 '=>' 对齐方式不变。
        'binary_operator_spaces' => [
            'default' => 'single_space',
            'operators' => [
                '=>' => null,
            ],
        ],
        // 连接符号 . 前后保留单个空格。
        'concat_space' => ['spacing' => 'one'],
    ])
    ->setFinder($finder)
;
