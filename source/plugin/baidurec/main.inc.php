<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

$pluginConfig = C::t('common_plugin')->fetch_by_identifier('baidurec');
$pluginVersion = $pluginConfig['version'];
$pluginId = $pluginConfig['pluginid'];

$tuijianUrl = 'http://tuijian.baidu.com';
$siteurl = urlencode($_G['siteurl']);
$url = sprintf('%s/rec-web/discuz/?siteurl=%s&pid=%s&cv=%s', $tuijianUrl, $siteurl, $pluginId, $pluginVersion);
?>
<script>
window.location = "<?php echo $url; ?>";
</script>
