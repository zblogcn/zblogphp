<label id="cmt<?php  echo $comment->ID; ?>"></label><blockquote id="cmt<?php  echo $comment->ID; ?>">
    <cite><b><a href="<?php  echo $comment->Author->HomePage; ?>" rel="nofollow" target="_blank"><?php  echo $comment->Author->Name; ?></a></b> <time>发表时间 <?php  echo $comment->Time(); ?></time></cite>
    <q><?php  echo $comment->Content; ?>
&nbsp;&nbsp;<i class="revertcomment"><a href="#comment" onclick="RevertComment('<?php  echo $comment->ID; ?>')">回复</a></i>
<?php  foreach ($comment->Comments as $comment) {
    ?>
<?php  include $this->GetTemplate('comment'); ?>
<?php
}   ?>
    </q>
</blockquote>
