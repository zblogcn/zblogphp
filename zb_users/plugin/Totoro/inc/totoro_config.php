<?php

return array(
    'SV_RULE' => array(
        'HYPERLINK_VALUE' => array(
            'VALUE'   => 0,
            'NAME'    => '链接评分',
            'DESC'    => '每多一个链接SV翻倍加分',
            'DEFAULT' => 10,
        ),
        'INTERVAL_VALUE' => array(
            'VALUE'   => 0,
            'NAME'    => '提交频率评分',
            'DESC'    => '根据1小时内同一IP的评论数量加分。规则为：1小时内5条评论加SV的1/5，以此类推，不设上限。',
            'DEFAULT' => 30,
        ),
        'BADWORD_VALUE' => array(
            'VALUE'   => 0,
            'NAME'    => '黑词加分',
            'DESC'    => '每多一个黑词加一次SV',
            'DEFAULT' => 50,
        ),
        'LEVEL_VALUE' => array(
            'VALUE'   => 0,
            'NAME'    => '用户信任度评分',
            'DESC'    => '从游客到管理员自动减去 (6-用户等级)*SV 分',
            'DEFAULT' => 10,
        ),
        'NAME_VALUE' => array(
            'VALUE'   => 0,
            'NAME'    => '访客熟悉度评分',
            'DESC'    => '同一IP在今天之外评论1-10条内减10分，10-20条的SV减(10+SV)分，20-50条的SV减(10+2*SV)，大于50条的减(10+3*SV)。',
            'DEFAULT' => 45,
        ),
        'NUMBER_VALUE' => array(
            'VALUE'   => 0,
            'NAME'    => '数字长度评分',
            'DESC'    => '若数字长度达到10位，自动加上(数字长度-10)*SV分。',
            'DEFAULT' => 0,
        ),
        'SIMILAR_VALUE' => array(
            'VALUE'   => 0,
            'NAME'    => '相似度评分',
            'DESC'    => '一旦相似度达到规则，则加上对应分数（如服务器配置不足，建议设为0）',
            'DEFAULT' => 50,
        ),
        'CHINESESV' => array(
            'VALUE'   => 0,
            'NAME'    => '汉字评分',
            'DESC'    => '一旦评论内没有汉字自动加SV',
            'DEFAULT' => 150,
        ),
    ),
    'SV_SETTING' => array(
        'REPLACE_KEYWORD' => array(
            'TYPE'    => 'STRING',
            'VALUE'   => '',
            'NAME'    => '敏感词替换',
            'DESC'    => '自动替换敏感词',
            'DEFAULT' => '**',
        ),
        'SV_THRESHOLD' => array(
            'TYPE'    => 'INT',
            'VALUE'   => 0,
            'NAME'    => '审核分数',
            'DESC'    => '分数达到设定值进入审核列表。低于0则游客评论全部进入审核。',
            'DEFAULT' => 50,
        ),
        'SV_THRESHOLD2' => array(
            'TYPE'    => 'INT',
            'VALUE'   => 0,
            'NAME'    => '删除分数',
            'DESC'    => '分数达到设定值直接删除，不加入审核列表。为0则禁用删除功能。',
            'DEFAULT' => 150,
        ),
        'KILLIP' => array(
            'TYPE'    => 'INT',
            'VALUE'   => 0,
            'NAME'    => 'IP回溯值',
            'DESC'    => '一旦某个IP一天内被审核的评论超过设定的值，则将该IP一天内的评论全部进入审核。若该IP有一条评论直接被拦截，所有评论也将进入审核状态。',
            'DEFAULT' => 5,
        ),
    ),
    'SIMILAR_CONFIG' => array(
        'SIMILAR_AUDIT_COMMCOUNT' => array(
            'TYPE'    => 'INT',
            'VALUE'   => 0,
            'NAME'    => '取未审核评论数量',
            'DESC'    => '取出最近24小时内发布的x条未审核评论计算相似程度',
            'DEFAULT' => 10,
        ),
        'SIMILAR_PASS_COMMCOUNT' => array(
            'TYPE'    => 'INT',
            'VALUE'   => 0,
            'NAME'    => '取已通过评论数量',
            'DESC'    => '取出最近24小时内发布的x条已通过评论计算相似程度',
            'DEFAULT' => 10,
        ),
        'SIMILAR_PERCENT' => array(
            'TYPE'    => 'INT',
            'VALUE'   => 80,
            'NAME'    => '评论相似度',
            'DESC'    => '如新发评论与最近x条评论的相似程度百分比有y条大于此数值，则加上y次对应SV（未审核评论为SV * 2）',
            'DEFAULT' => 80,
        ),
    ),
    'BUILD_CONFIG' => array(
        'CONHXW' => array(
            'VALUE'   => true,
            'NAME'    => '火星文转换',
            'DESC'    => '将把斯拉夫字母罗马数字列表符全角字符汉语拼音菊花文HTML编码转换为半角英文字母、半角数字、半角符号再进行反SPAM测试，不影响实际显示的评论',
            'DEFAULT' => true,
        ),
        'TRANTOSIMP' => array(
            'VALUE'   => true,
            'NAME'    => '简繁转换',
            'DESC'    => '将把正体中文转为简化字再进行反SPAM测试，不影响实际显示的评论',
            'DEFAULT' => true,
        ),
        'FILTERPUNCT' => array(
            'VALUE'   => true,
            'NAME'    => '标点过滤',
            'DESC'    => '把大部分标点和HTML代码过滤再进行反SPAM测试，不影响实际显示的评论',
            'DEFAULT' => true,
        ),
        'AUTOBANURL' => array(
            'VALUE'   => false,
            'NAME'    => 'URL审核',
            'DESC'    => '将某条评论加入审核时自动将其URL加入过滤列表',
            'DEFAULT' => false,
        ),
    ),
    /*'DEL_DIRECTLY' => array(
        'TYPE' => 'BOOL',
        'VALUE' => TRUE,
        'NAME' => '后台审核',
        'DESC' => '点击【加入审核】提取域名后直接删除评论（关闭则为加入审核列表）',
        'DEFAULT' => TRUE
    */
    'STRING_BACK' => array(
        'CHECKSTR' => array(
            'TYPE'    => 'STRING',
            'VALUE'   => '',
            'NAME'    => '评论被过滤时的提示',
            'DESC'    => '',
            'DEFAULT' => 'Totoro大显神威！你的评论被怀疑是垃圾评论已经被提交审核。',
        ),
        'THROWSTR' => array(
            'TYPE'    => 'STRING',
            'VALUE'   => '',
            'NAME'    => '评论被拦截时的提示',
            'DESC'    => '',
            'DEFAULT' => 'Totoro大显神威！你的评论被怀疑是垃圾评论已经被删除。',
        ),
        'KILLIPSTR' => array(
            'TYPE'    => 'STRING',
            'VALUE'   => '',
            'NAME'    => 'IP被过滤时的提示',
            'DESC'    => '',
            'DEFAULT' => 'Totoro大显神威！你的IP不合法不允许提交评论。',
        ),
    ),
    'BLACK_LIST' => array(
        'BADWORD_LIST' => array(
            'VALUE'   => '',
            'NAME'    => '黑词列表',
            'DESC'    => '使用正则表达式，最后一个字符不能是“|”',
            'DEFAULT' => Totoro_BadWordDefault(),
        ),
        'REPLACE_LIST' => array(
            'VALUE'   => '',
            'NAME'    => '敏感词列表',
            'DESC'    => '使用正则表达式，最后一个字符不能是“|”',
            'DEFAULT' => '',
        ),
        'IPFILTER_LIST' => array(
            'VALUE'   => '',
            'NAME'    => 'IP过滤列表',
            'DESC'    => '用|分隔，支持*屏蔽IP段',
            'DEFAULT' => '',
        ),
    ),
);

function Totoro_BadWordDefault()
{
    return urldecode('(%e6%8e%a8%e5%b9%bf%7c%e7%be%a4%e5%8f%91%7c%e5%b9%bf%e5%91%8a%7c%e8%a7%a3%e5%af%86%7c%e8%b5%8c%e5%8d%9a%7c%e5%8c%85%e9%9d%92%e5%a4%a9%7c%e5%b9%bf%e5%91%8a%7c%e9%98%bf%e5%87%a1%e6%8f%90%7c%e5%8f%91%e8%b4%b4%7c%e9%a1%b6%e8%b4%b4%7c(%e9%92%88%e5%ad%94%7c%e9%9a%90%e5%bd%a2%7c%e9%9a%90%e8%94%bd%e5%bc%8f)%e6%91%84%e5%83%8f%7c%e5%b9%b2%e6%89%b0%7c%e9%a1%b6%e5%b8%96%7c%e5%8f%91%e5%b8%96%7c%e6%b6%88%e5%a3%b0%7c%e9%81%a5%e6%8e%a7%7c%e8%a7%a3%e7%a0%81%7c%e7%aa%83%e5%90%ac%7c%e8%ba%ab%e4%bb%bd%e8%af%81%e7%94%9f%e6%88%90%7c%e6%8b%a6%e6%88%aa%7c%e5%a4%8d%e5%88%b6%7c%e7%9b%91%e5%90%ac%7c%e5%ae%9a%e4%bd%8d%7c%e6%b6%88%e5%a3%b0%7c%e4%bd%9c%e5%bc%8a%7c%e6%89%a9%e6%95%a3%7c%e4%be%a6%e6%8e%a2%7c%e8%bf%bd%e6%9d%80)(%e6%9c%ba%7c%e5%99%a8%7c%e8%bd%af%e4%bb%b6%7c%e8%ae%be%e5%a4%87%7c%e7%b3%bb%e7%bb%9f)%7c(%e6%b1%82%7c%e6%8d%a2%7c%e6%9c%89%e5%81%bf%7c%e4%b9%b0%7c%e5%8d%96%7c%e5%87%ba%e5%94%ae)(%e8%82%be%7c%e5%99%a8%e5%ae%98%7c%e7%9c%bc%e8%a7%92%e8%86%9c%7c%e8%a1%80)%7c%e8%82%be%e6%ba%90%7c(%e5%81%87%7c%e6%af%95%e4%b8%9a)(%e8%af%81%7c%e6%96%87%e5%87%ad%7c%e5%8f%91%e7%a5%a8%7c%e5%b8%81)%7c(%e6%89%8b%e6%a6%b4%7c%e4%ba%ba%7c%e9%ba%bb%e9%86%89%7c%e9%9c%b0)%e5%bc%b9%7c%e6%b2%bb%e7%96%97(%e8%82%bf%e7%98%a4%7c%e4%b9%99%e8%82%9d%7c%e6%80%a7%e7%97%85%7c%e7%ba%a2%e6%96%91%e7%8b%bc%e7%96%ae)%7c%e9%87%8d%e4%ba%9a%e7%a1%92%e9%85%b8%e9%92%a0%7c(%e7%b2%98%e6%b0%af%7c%e5%8e%9f%e7%a0%b7)%e9%85%b8%7c%e9%ba%bb%e9%86%89%e4%b9%99%e9%86%9a%7c%e5%8e%9f%e8%97%9c%e8%8a%a6%e7%a2%b1A%7c%e6%b0%b8%e4%bc%8f%e8%99%ab%7c%e8%9d%87%e6%af%92%7c%e7%bd%82%e7%b2%9f%7c%e9%93%b6%e6%b0%b0%e5%8c%96%e9%92%be%7c%e6%b0%af%e8%83%ba%e9%85%ae%7c%e5%9b%a0%e6%af%92(%e7%a1%ab%e7%a3%b7%7c%e7%a3%b7)%7c%e5%bc%82%e6%b0%b0%e9%85%b8(%e7%94%b2%e9%85%af%7c%e8%8b%af%e9%85%af)%7c%e5%bc%82%e7%a1%ab%e6%b0%b0%e9%85%b8%e7%83%af%e4%b8%99%e9%85%af%7c%e4%b9%99%e9%85%b0(%e4%ba%9a%e7%a0%b7%e9%85%b8%e9%93%9c%7c%e6%9b%bf%e7%a1%ab%e8%84%b2)%7c%e4%b9%99%e7%83%af%e7%94%b2%e9%86%87%7c%e4%b9%99%e9%85%b8(%e4%ba%9a%e9%93%8a%7c%e9%93%8a%7c%e4%b8%89%e4%b9%99%e5%9f%ba%e9%94%a1%7c%e4%b8%89%e7%94%b2%e5%9f%ba%e9%94%a1%7c%e7%94%b2%e6%b0%a7%e5%9f%ba%e4%b9%99%e5%9f%ba%e6%b1%9e%7c%e6%b1%9e)%7c%e4%b9%99%e7%a1%bc%e7%83%b7%7c%e4%b9%99%e9%86%87%e8%85%88%7c%e4%b9%99%e6%92%91%e4%ba%9a%e8%83%ba%7c%e4%b9%99%e6%92%91%e6%b0%af%e9%86%87%7c%e4%bc%8a%e7%9a%ae%e6%81%a9%7c%e6%b5%b7%e6%b4%9b%e5%9b%a0%7c%e4%b8%80%e6%b0%a7(%e5%8c%96%e6%b1%9e%7c%e5%8c%96%e4%ba%8c%e6%b0%9f)%7c%e4%b8%80%e6%b0%af(%e4%b9%99%e9%86%9b%7c%e4%b8%99%e9%85%ae)%7c%e6%b0%a7%e6%b0%af%e5%8c%96%e7%a3%b7%7c%e6%b0%a7%e5%8c%96(%e4%ba%9a%e9%93%8a%7c%e9%93%8a%7c%e6%b1%9e%7c%e4%ba%8c%e4%b8%81%e5%9f%ba%e9%94%a1)%7c%e7%83%9f%e7%a2%b1%7c%e4%ba%9a%e7%a1%9d%e9%85%b0%e4%b9%99%e6%b0%a7%7c%e4%ba%9a%e7%a1%9d%e9%85%b8%e4%b9%99%e9%85%af%7c%e4%ba%9a%e7%a1%92%e9%85%b8%e6%b0%a2%e9%92%a0%7c%e4%ba%9a%e7%a1%92%e9%85%b8%e9%92%a0%7c%e4%ba%9a%e7%a1%92%e9%85%b8%e9%95%81%7c%e4%ba%9a%e7%a1%92%e9%85%b8%e4%ba%8c%e9%92%a0%7c%e4%ba%9a%e7%a1%92%e9%85%b8%7c%e4%ba%9a%e7%a0%b7%e9%85%b8(%e9%92%a0%7c%e9%92%be%7c%e9%85%90)%7c%e5%86%b0%e6%af%92%7c%e9%a2%84%e6%b5%8b%e7%ad%94%e6%a1%88%7c%e8%80%83%e5%89%8d%e9%a2%84%e6%b5%8b%7c%e6%8a%bc%e9%a2%98%7c%e4%bb%a3%e5%86%99%e8%ae%ba%e6%96%87%7c(%e6%8f%90%e4%be%9b%7c%e5%8f%b8%e8%80%83%7c%e7%ba%a7%7c%e4%bc%a0%e9%80%81%7c%e8%80%83%e4%b8%ad%7c%e7%9f%ad%e4%bf%a1)%e7%ad%94%e6%a1%88%7c(%e5%be%85%7c%e4%bb%a3%7c%e5%b8%a6%7c%e6%9b%bf%7c%e5%8a%a9)%e8%80%83%7c(%e5%8c%85%7c%e9%a1%ba%e5%88%a9%7c%e4%bf%9d)%e8%bf%87%7c%e8%80%83%e5%90%8e%e4%bb%98%e6%ac%be%7c%e6%97%a0%e7%ba%bf%e8%80%b3%e6%9c%ba%7c%e8%80%83%e8%af%95%e4%bd%9c%e5%bc%8a%7c%e8%80%83%e5%89%8d%e5%af%86%e5%8d%b7%7c%e6%bc%8f%e9%a2%98%7c%e4%b8%ad%e7%89%b9%7c%e4%b8%80%e8%82%96%7c%e6%8a%a5%e7%a0%81%7c(%e5%90%88%7c%e9%a6%99%e6%b8%af)%e5%bd%a9%7c%e5%bd%a9%e5%ae%9d%7c3D%e8%bd%ae%e7%9b%98%7cliuhecai%7c%e4%b8%80%e7%a0%81%7c(%e7%9a%87%e5%ae%b6%7c%e4%bf%84%e7%bd%97%e6%96%af)%e8%bd%ae%e7%9b%98%7c%e8%b5%8c%e5%85%b7%7c%e7%89%b9%e7%a0%81%7c%e7%9b%97(%e5%8f%b7%7cqq%7c%e5%af%86%e7%a0%81)%7c%e7%9b%97%e5%8f%96(%e5%af%86%e7%a0%81%7cqq)%7c%e5%97%91%e8%8d%af%7c%e5%b8%ae%e6%8b%9b%e4%ba%ba%7c%e7%a4%be%e4%bc%9a%e6%b7%b7%7c%e6%8b%9c%e5%a4%a7%e5%93%a5%7c%e7%94%b5%e8%ad%a6%e6%a3%92%7c%e5%b8%ae%e4%ba%ba%e6%80%80%e5%ad%95%7c%e5%be%81%e5%85%b5%e8%ae%a1%e5%88%92%7c%e5%88%87%e8%85%b9%7cVE%e8%a7%86%e8%a7%89%7c%e7%94%b5%e9%b8%a1%7c%e4%bb%bf%e7%9c%9f%e6%89%8b%e6%9e%aa%7c%e5%81%9a%e7%82%b8%e5%bc%b9%7c%e8%b5%b0%e7%a7%81%7c%e9%99%aa%e8%81%8a%7ch(%e5%9b%be%7c%e6%bc%ab%7c%e7%bd%91)%7c%e5%bc%80%e8%8b%9e%7c%e6%89%be(%e7%94%b7%7c%e5%a5%b3)%7c%e5%8f%a3%e6%b7%ab%7c%e5%8d%96%e8%ba%ab%7c%e5%85%83%e4%b8%80%e5%a4%9c%7c(%e7%94%b7%7c%e5%a5%b3)%e5%a5%b4%7c%e5%8f%8c(%e7%ad%92%7c%e6%a1%b6)%7c%e7%9c%8bJJ%7c%e5%81%9a%e5%8f%b0%7c%e5%8e%95%e5%a5%b4%7c%e9%aa%9a%e5%a5%b3%7c%e5%ab%a9%e9%80%bc%7c%e4%b8%80%e5%a4%9c%e6%bf%80%e6%83%85%7c%e4%b9%b1%e4%bc%a6%7c%e6%b3%a1%e5%8f%8b%7c%e5%af%8c(%e5%a7%90%7c%e5%a9%86)%7c(%e8%b6%b3%7c%e7%be%a4%7c%e8%8c%b9)%e4%ba%a4%7c%e9%98%b4%e6%88%b7%7c%e6%80%a7(%e6%9c%8d%e5%8a%a1%7c%e4%bc%b4%e4%be%a3%7c%e4%bc%99%e4%bc%b4%7c%e4%ba%a4)%7c%e6%9c%89%e5%81%bf(%e6%8d%90%e7%8c%ae%7c%e6%9c%8d%e5%8a%a1)%7c(%e6%9c%89%7c%e6%97%a0)%e7%a0%81%7c%e5%8c%85%e5%85%bb%7c(%e7%8a%ac%7c%e5%85%bd%7c%e5%b9%bc)%e4%ba%a4%7c%e6%a0%b9%e6%b5%b4%7c%e6%8f%b4%e4%ba%a4%7c%e5%b0%8f%e5%8f%a3%e5%be%84%7c%e6%80%a7(%e8%99%90%7c%e7%88%b1%7c%e6%81%af)%7c%e5%88%bb%e7%ab%a0%7c%e6%91%87%e5%a4%b4%e4%b8%b8%7c%e7%9b%91%e5%90%ac%e7%8e%8b%7c%e6%98%8f%e8%8d%af%7c%e4%be%a6%e6%8e%a2%e8%ae%be%e5%a4%87%7c%e6%80%a7%e5%a5%b4%7c%e9%80%8f%e8%a7%86%e7%9c%bc(%e7%9d%9b%7c%e9%95%9c)%7c%e6%8b%8d%e8%82%a9%e7%a5%9e%7c(%e5%a4%b1%e5%bf%86%7c%e5%82%ac%e6%83%85%7c%e8%bf%b7(%e5%b9%bb%7c%e6%98%8f%7c%e5%a5%b8)%3f%7c%e5%ae%89%e5%ae%9a)(%e8%8d%af%7c%e7%89%87%7c%e9%a6%99)%7c%e6%b8%b8%e6%88%8f%e6%9c%ba%e7%a0%b4%e8%a7%a3%7c%e9%9a%90%e5%bd%a2%e8%80%b3%e6%9c%ba%7c%e9%93%b6%e8%a1%8c%e5%8d%a1%e5%a4%8d%e5%88%b6%e8%ae%be%e5%a4%87%7c%e4%b8%80%e5%8d%a1%e5%a4%9a%e5%8f%b7%7c%e4%bf%a1%e7%94%a8%e5%8d%a1%e5%a5%97%e7%8e%b0%7c%e6%b6%88%e9%98%b2%5b%e7%81%ad%e7%81%ab%5d%3f%e6%9e%aa%7c%e9%a6%99%e6%b8%af%e7%94%9f%e5%ad%90%7c%e5%9c%9f%e7%82%ae%7c%e8%83%8e%e7%9b%98%7c%e6%89%8b%e6%9c%ba%e9%ad%94%e5%8d%a1%7c%e5%ae%b9%e5%bc%b9%e9%87%8f%7c%e6%9e%aa%e6%a8%a1%7c%e9%93%85%e5%bc%b9%7c%e6%b1%bd(%e6%9e%aa%7c%e7%8b%97%7c%e8%b5%b0%e8%a1%a8%e5%99%a8)%7c%e6%b0%94%e6%9e%aa%7c%e6%b0%94%e7%8b%97%7c%e4%bc%9f%e5%93%a5%7c%e7%ba%bd%e6%89%a3%e6%91%84%e5%83%8f%e6%9c%ba%7c%e5%85%8d%e7%94%b5%e7%81%af%7c%e5%8d%96QQ%e5%8f%b7%e7%a0%81%7c%e9%ba%bb%e9%86%89%e8%8d%af%7c%e5%ba%b7%e7%94%9f%e4%b8%b9%7c%e8%ad%a6%e5%be%bd%7c%e8%ae%b0%e5%8f%b7%e6%89%91%e5%85%8b%7c%e6%bf%80%e5%85%89(%e6%b1%bd%7c%e6%b0%94)%7c%e7%ba%a2%e5%ba%8a%7c%e7%8b%97%e5%8f%8b%7c%e5%8f%8d%e9%9b%b7%e8%be%be%e6%b5%8b%e9%80%9f%7c%e7%9f%ad%e4%bf%a1%e6%8a%95%e7%a5%a8%e4%b8%9a%e5%8a%a1%7c%e7%94%b5%e5%ad%90%e7%8b%97%e5%af%bc%e8%88%aa%e6%89%8b%e6%9c%ba%7c%e5%bc%b9(%e7%a7%8d%7c%e5%a4%b9)%7c(%e8%bf%bd%7c%e8%ae%a8)%e5%80%ba%7c%e8%bd%a6%e7%94%a8%e7%94%b5%e5%ad%90%e7%8b%97%7c%e9%81%bf%e5%ad%95%7c%e5%8a%9e%e7%90%86(%e8%af%81%e4%bb%b6%7c%e6%96%87%e5%87%ad)%7c%e6%96%91%e8%9d%a5%7c%e6%9a%97%e8%ae%bf%e5%8c%85%7cSIM%e5%8d%a1%e5%a4%8d%e5%88%b6%e5%99%a8%7cBB(%e6%9e%aa%7c%e5%bc%b9)%7c%e9%9b%b7%e7%ae%a1%7c%e5%bc%93%e5%bc%a9%7c(%e7%94%b5%7c%e9%95%bf)%e7%8b%97%7c%e5%af%bc%e7%88%86%e7%b4%a2%7c%e7%88%86%e7%82%b8%e7%89%a9%7c%e7%88%86%e7%a0%b4%7c%e5%b7%a6%e6%a3%8d%7c%e5%a9%8a%e5%ad%90%7c%e6%8d%a2%e5%a6%bb%7c%e6%88%90%e4%ba%ba%e7%89%87%7c%e6%b7%ab(%e9%9d%a1%7c%e6%b0%b4%7c%e5%85%bd)%7c%e9%98%b4(%e6%af%9b%7c%e8%92%82%7c%e9%81%93%7c%e5%94%87)%7c%e5%b0%8f%e7%a9%b4%7c%e7%bc%a9%e9%98%b4%7c%e5%b0%91%e5%a6%87%e8%87%aa%e6%8b%8d%7c(%e4%b8%89%e7%ba%a7%7c%e8%89%b2%e6%83%85%7c%e6%bf%80%e6%83%85%7c%e9%bb%84%e8%89%b2%7c%e5%b0%8f)(%e7%89%87%7c%e7%94%b5%e5%bd%b1%7c%e8%a7%86%e9%a2%91%7c%e4%ba%a4%e5%8f%8b%7c%e7%94%b5%e8%af%9d)%7c%e8%82%89%e6%a3%92%7c(%e6%83%85%7c%e5%a5%b8)%e6%9d%80%7c%e8%a3%b8%e7%85%a7%7c%e4%b9%b1%e4%bc%a6%7c%e5%8f%a3%e4%ba%a4%7c%e7%a6%81(%e7%bd%91%7c%e7%89%87)%7c%e6%98%a5%e5%ae%ab%e5%9b%be%7cSM%e7%94%a8%e5%93%81%7c%e8%87%aa%e5%8a%a8%e7%be%a4%e5%8f%91%7c%e7%a7%81%e5%ae%b6%e4%be%a6%e6%8e%a2%e6%9c%8d%e5%8a%a1%7c%e7%94%9f%e6%84%8f%e5%ae%9d%7c%e5%95%86%e5%8a%a1(%e5%bf%ab%e8%bd%a6%7c%e7%9f%ad%e4%bf%a1)%7c%e6%85%a7%e8%81%aa%7c%e4%be%9b%e5%ba%94%e5%8f%91%e7%a5%a8%7c%e5%8f%91%e7%a5%a8%e4%bb%a3%e5%bc%80%7c%e7%9f%ad%e4%bf%a1%e7%be%a4%e5%8f%91%7c%e7%9f%ad%e4%bf%a1%e7%8c%ab%7c%e7%82%b9%e9%87%91%e5%95%86%e5%8a%a1%7c%e5%a3%ab%e7%9a%84%e5%ae%81%7c%e5%a3%ab%e7%9a%84%e5%b9%b4%7c%e5%85%ad%e5%90%88(%e9%87%87%7c%e5%bd%a9)%7c%e4%b9%90%e9%80%8f%e7%a0%81%7c%e5%bd%a9%e7%a5%a8%7c%e7%99%be%e4%b9%90%e4%ba%8c%e5%91%93%7c%e7%99%be%e5%ae%b6%e4%b9%90%7c%e9%bb%84%e9%a1%b5%7c%e5%87%ba%e7%a7%9f%7c%e6%b1%82%e8%b4%ad%7c%e7%95%99%e5%ad%a6%e5%92%a8%e8%af%a2%7c%e5%a4%96%e6%8c%82%7c%e6%b7%98%e5%ae%9d%7c%e7%be%a4%e5%8f%91%7c%e8%b4%a7%e5%88%b0%e4%bb%98%e6%ac%be%7c%e6%b1%bd%e8%bd%a6%e9%85%8d%e4%bb%b6%7c%e6%8e%a8%e5%b9%bf%e8%81%94%e7%9b%9f%7c%e5%8a%b3%e5%8a%a1%e6%b4%be%e9%81%a3%7c%e7%bd%91%e7%bb%9c(%e5%85%bc%e8%81%8c%7c%e8%b5%9a%e9%92%b1)%7c(%e8%af%81%e4%bb%b6%7c%e5%a9%9a%e5%ba%86%7c%e7%bf%bb%e8%af%91%7c%e6%90%ac%e5%ae%b6%7c%e8%bf%bd%e5%80%ba%7c%e5%80%ba%e5%8a%a1)%e5%85%ac%e5%8f%b8%7c%e6%89%8b%e6%9c%ba(%e6%b8%b8%e6%88%8f%7c%e7%aa%83%e5%90%ac%7c%e7%9b%91%e5%90%ac%7c%e9%93%83%e5%a3%b0%7c%e5%9b%be%e7%89%87)%7c%e4%b8%89%e5%94%91%e4%bb%91%7c%e5%a5%87%e8%bf%b9%e4%b8%96%e7%95%8c%7c%e5%b7%a5%e4%bd%9c%e6%9c%8d%7c%e8%ae%ba%e6%96%87%7c%e9%93%83%e5%a3%b0%7c%e5%bd%a9(%e4%bf%a1%7c%e9%93%83%7c%e7%a5%a8)%7c%e6%98%be%e7%a4%ba%e5%b1%8f%7c%e6%8a%95%e5%bd%b1%e4%bb%aa%7c%e8%99%9a%e6%8b%9f%e4%b8%bb%e6%9c%ba%7c(%e5%9f%9f%e5%90%8d%7c%e4%b8%93%e4%b8%9a)%e6%b3%a8%e5%86%8c%7c%e8%90%a5%e9%94%80%7c%e6%9c%8d%e5%8a%a1%e5%99%a8%e6%89%98%e7%ae%a1%7c%e7%bd%91%e7%ab%99%e5%bb%ba%e8%ae%be%7c(google%7c%e7%99%be%e5%ba%a6)%e6%8e%92%e5%90%8d%7c%e6%95%b0%e6%8d%ae%e6%81%a2%e5%a4%8d%7c%e5%8c%bb%e9%99%a2%7c%e6%80%a7%e7%97%85%7c%e4%b8%8d%e5%ad%95%e4%b8%8d%e8%82%b2%7c%e4%b9%b3%e8%85%ba%e7%97%85%7c%e5%b0%96%e9%94%90%e6%b9%bf%e7%96%a3%7c%e7%9a%ae%e8%82%a4%e7%97%85%7c%e5%87%8f%e8%82%a5%7c%e7%98%a6%7c3P%7c%e4%ba%ba%e5%85%bd%7c%e4%bb%a3%e5%ad%95%7c%e6%89%93%e7%82%ae%7c%e6%89%be%e5%b0%8f%e5%a7%90%7c%e5%88%bb%e7%ab%a0%7c%e4%b9%b1%e4%bc%a6%7c%e4%b8%ad%e5%87%ba%7c%e6%a5%bc%e5%87%a4%7c%e5%8d%96%e6%b7%ab%7c%e8%8d%a1%e5%a6%87%7c%e7%be%a4%e4%ba%a4%7c%e5%b9%bc%e5%a5%b3%7c18%e7%a6%81%7c%e4%bc%a6%e7%90%86%e7%94%b5%e5%bd%b1%7c(%e5%82%ac%e6%83%85%7c%e8%92%99%e6%b1%97%7c%e8%92%99%e6%b1%89%7c%e6%98%a5)%e8%8d%af%7c%e6%83%85%e8%b6%a3%e7%94%a8%e5%93%81%7c%e6%88%90%e4%ba%ba.%2b%3f(%e7%94%b5%e5%bd%b1%7c%e7%94%a8%e5%93%81)%7c%e6%bf%80%e6%83%85(%e8%a7%86%e9%a2%91%7c%e7%94%b5%e5%bd%b1%7c%e5%bd%b1%e9%99%a2)%7c%e7%88%bd%e7%89%87%7c%e6%80%a7%e6%84%9f%e7%be%8e%e5%a5%b3%7c%e4%ba%a4%e5%8f%8b%7c%e6%80%80%e5%ad%95%7c%e8%a3%b8%e8%81%8a%7c%e5%88%b6%e6%9c%8d%e8%af%b1%e6%83%91%7c%e4%b8%9d%e8%a2%9c%7c%e9%95%bf%e8%85%bf%7c%e5%af%82%e5%af%9e%e5%a5%b3%e5%ad%90%7c%e5%85%8d%e8%b4%b9%e7%94%b5%e5%bd%b1%7c%e5%8f%8c%e8%89%b2%e7%90%83%7c%e7%a6%8f%e5%bd%a9%7c%e4%bd%93%e5%bd%a9%7c6%e5%90%88%e5%bd%a9%7c%e6%97%b6%e6%97%b6%e5%bd%a9%7c%e5%8f%8c%e8%89%b2%e7%90%83%7c%e5%92%a8%e8%af%a2%e7%83%ad%e7%ba%bf%7c%e8%82%a1%e7%a5%a8%7c%e8%8d%90%e8%82%a1%7c%e5%bc%80%e8%82%a1%7c%e7%a7%81%e6%9c%8d%7c%e6%9e%aa%7c%e8%ad%a6%e6%a3%92%7c%e8%ad%a6%e6%9c%8d%7c%e9%ba%bb%e9%86%89%7c%e8%af%9a%e6%8b%9b%e5%8a%a0%e7%9b%9f%7c%e8%af%9a%e4%bf%a1%e7%bb%8f%e8%90%a5%7c%e6%9d%80%e6%89%8b%7c(%e6%b8%b8%e6%88%8f%7c%e9%87%91)%e5%b8%81%7c%e7%be%a4%e5%8f%91%7c%e6%b3%a8%e5%86%8c.%2b%3f%e5%85%ac%e5%8f%b8%7c%e5%85%ac%e5%8f%b8%e6%b3%a8%e5%86%8c%7c%e5%8f%91%e7%a5%a8%7c%e4%bb%a3%e5%bc%80%7c%e6%b7%98%e5%ae%9d%7c%e8%bf%94%e5%88%a9%7c%e5%9b%a2%e8%b4%ad%7c%e5%9f%b9%e8%ae%ad%7c%e6%8a%98%e6%89%a3%7c(%e6%89%93%e5%8c%85%7c%e8%af%95%e9%aa%8c%7c%e6%89%93%e6%a0%87%7c%e7%a0%b4%e7%a2%8e%7c%e7%81%8c%e8%a3%85%7c%e5%8d%87%e9%99%8d%7c%e5%b9%b2%e7%87%a5%7c%e7%83%98%e5%b9%b2)%e6%9c%ba%7c%e6%9d%a1%e7%a0%81%7c%e6%a0%87%e7%ad%be%e7%ba%b8%7c%e5%8d%87%e9%99%8d%e5%b9%b3%e5%8f%b0%7c%e5%9c%b0%e6%ba%90%e7%83%ad%e6%b3%b5%7c%e9%a3%8e%e6%9c%ba%e7%9b%98%e7%ae%a1%7c%e4%ba%8c%e6%89%8b(%e8%bd%a6%7c%e7%94%b5%e8%84%91)%7c%e6%89%8b%e8%a1%a8%7c%e5%8a%a0%e7%9b%9f%7c%e5%90%8d%e8%a1%a8%7c%e7%89%b9%e5%8d%96%7c%e8%af%81%e4%b9%a6%7c%e8%81%8a%e5%a4%a9%e5%ae%a4%7c%e5%88%86%e9%94%80');
}
