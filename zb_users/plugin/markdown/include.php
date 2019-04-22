<?php

//注册插件
RegisterPlugin("markdown", "ActivePlugin_markdown");

require dirname(__FILE__) . '/Markdown.php';

function ActivePlugin_markdown()
{
    Add_Filter_Plugin('Filter_Plugin_Edit_Begin', 'markdown_addscript_begin');

    Add_Filter_Plugin('Filter_Plugin_Edit_End', 'markdown_addscript_end');

    Add_Filter_Plugin('Filter_Plugin_PostPage_Core', 'markdown_mk2html');

    Add_Filter_Plugin('Filter_Plugin_PostArticle_Core', 'markdown_mk2html');

    //Add_Filter_Plugin('Filter_Plugin_Cmd_Ajax', 'markdown_upload_ajax');

    Add_Filter_Plugin('Filter_Plugin_Html_Js_Add', 'markdown_Js_Add');
}

function InstallPlugin_markdown()
{
}

function UninstallPlugin_markdown()
{
}

//执行ajax函数
function markdown_upload_ajax($src_type)
{
    global $zbp;
    if (isset($src_type) && $src_type == 'image') {
        call_user_func('markdown_upload_ajax_' . $src_type);
    }
}

function markdown_upload_ajax_image()
{
    global $zbp;
    $xhr = array('error' => "", 'url' => "");
    foreach ($_FILES as $key => $value) {
        if ($_FILES[$key]['error'] == 0) {
            if (is_uploaded_file($_FILES[$key]['tmp_name'])) {
                $tmp_name = $_FILES[$key]['tmp_name'];
                $name = $_FILES[$key]['name'];

                $upload = new Upload();
                $upload->Name = $_FILES[$key]['name'];
                $upload->SourceName = $_FILES[$key]['name'];
                $upload->MimeType = $_FILES[$key]['type'];
                $upload->Size = $_FILES[$key]['size'];
                $upload->AuthorID = $zbp->user->ID;

                if (!$upload->CheckExtName()) {
                    $xhr['error'] = $this->lang['error'][26];
                }
                if (!$upload->CheckSize()) {
                    $xhr['error'] = $this->lang['error'][27];
                }

                $upload->SaveFile($_FILES[$key]['tmp_name']);
                $upload->Save();
            }
        }
    }
    if (isset($upload)) {
        CountMemberArray(array($upload->AuthorID), array(0, 0, 0, +1));
        $xhr['url'] = $upload->Url;
    }
    echo json_encode($xhr);
}

function markdown_html2mk()
{
}

function markdown_mk2html(&$post)
{
    $post->Content = Markdown::defaultTransform($post->Content);
    $post->Intro = Markdown::defaultTransform($post->Intro);
}

function markdown_Js_Add()
{
    global $zbp;
    if (!$zbp->option['ZC_SYNTAXHIGHLIGHTER_ENABLE']) {
        return;
    }
    echo "\r\n" . 'document.writeln("<link rel=\'stylesheet\' href=\'' . $zbp->host . 'zb_users/plugin/markdown/css/prettify.css\' /><script src=\'' . $zbp->host . 'zb_users/plugin/markdown/js/prettify.js\'></script>");' . "\r\n";
    echo "\r\n$(document).ready(function(){ $('code').wrap('<pre class=\"prettyprint\"></pre>'); prettyPrint(); });\r\n";
}

function markdown_addscript_begin()
{
    global $zbp,$article;

    echo '<link rel="stylesheet" href="' . $zbp->host . 'zb_users/plugin/markdown/css/bootstrap.min.css" />';
    echo '<link rel="stylesheet" href="' . $zbp->host . 'zb_users/plugin/markdown/css/bootstrap-markdown.min.css" />';
    echo '<style type="text/css">.divHeader2{padding-bottom:38px;}#edtDateTime{width:180px!important;}</style>';
    echo '<script src="' . $zbp->host . 'zb_users/plugin/markdown/js/bootstrap.min.js"></script>';
    echo '<script src="' . $zbp->host . 'zb_users/plugin/markdown/js/markdown.js"></script>';
    //echo '<script src="' . $zbp->host . 'zb_users/plugin/markdown/js/he.js"></script>';
    echo '<script src="' . $zbp->host . 'zb_users/plugin/markdown/js/to-markdown.js"></script>';
    echo '<script src="' . $zbp->host . 'zb_users/plugin/markdown/js/bootstrap-markdown.js"></script>';
    echo '<script src="' . $zbp->host . 'zb_users/plugin/markdown/locale/bootstrap-markdown.zh.js"></script>';
    echo '<script src="' . $zbp->host . 'zb_users/plugin/markdown/js/jquery.hotkeys.js"></script>';
    echo '<script src="' . $zbp->host . 'zb_users/plugin/markdown/js/main.js"></script>';

    $article->Content = str_replace('<hr class="more" />', '<!--more-->', $article->Content);
    $article->Content = html_entity_decode($article->Content);
    $article->Intro = html_entity_decode($article->Intro);
}

function markdown_addscript_end()
{
    global $zbp;

    $s = <<<'js'
<script type="text/javascript">
$('#editor_content').val( toMarkdown( $('#editor_content').val() ) );
$('#editor_intro').val( toMarkdown( $('#editor_intro').val() ) );
$('#editor_content').attr("data-provide","markdown");
$("#editor_content, #editor_intro").markdown({language:'zh'});
</script>
js;
    echo $s;
}
