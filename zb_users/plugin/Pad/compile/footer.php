<footer>
  <h5>Powered By Z-BlogPHP&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php  echo $host; ?>?mod=pad" style="text-decoration:underline">触屏版</a>&nbsp;&nbsp;|&nbsp;<a href="<?php  echo $host; ?>?mod=pc">电脑版</a></h5>
</footer>
<?php  echo $footer; ?>
<script type="text/javascript">
function GetComments(postid,page){
    $.get(bloghost+"zb_system/cmd.php?act=getcmt&postid="+postid+"&page="+page+"&mod=pad", function(data){
      $('#AjaxCommentBegin').nextUntil('#AjaxCommentEnd').remove();
      $('#AjaxCommentBegin').after(data);
    });

}
</script>
</body>
</html>
