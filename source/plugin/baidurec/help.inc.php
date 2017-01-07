<?php
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}
$redirect = 'http://hi.baidu.com/bdtuijian/item/55e174d45971490be3108f8e';
?>
<script>
    window.open("<?php echo $redirect; ?>");
    history.back(-1);
</script>