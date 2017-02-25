</section>
<?php
foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_Footer'] as $fpname => &$fpsignal) {
    $fpname();
}
?>
</body>
</html>
