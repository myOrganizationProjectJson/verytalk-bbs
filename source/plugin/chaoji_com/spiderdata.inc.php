<?php 
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

require_once(dirname(__FILE__) . '/include/function.php');
require_once dirname(__FILE__) . '/include/check.php';

	echo '<script src="source/plugin/chaoji_com/resource/js/My97DatePicker/WdatePicker.js" type="text/javascript"></script><script type="text/javascript" language="javascript" src="source/plugin/chaoji_com/resource/js/jquery.min.js"></script>
<script type="text/javascript">var jq = jQuery.noConflict();var cookiepre=\'' . $_G['config']['cookie']['cookiepre'] . '\';var disallowfloat=false;var clientinfo_status=0;var clientinfo_str=\'\';var PLUGIN_URL = \'' . CJ_PLUGIN_URL . '\'; var FORMHASH = \'' . FORMHASH . '\';</script>
<script type="text/javascript" src="source/plugin/chaoji_com/resource/js/highcharts.js" language="javascript"></script>
<script type="text/javascript" src="source/plugin/chaoji_com/resource/js/tableorder.js" language="javascript"></script>
<script src="source/plugin/chaoji_com/resource/js/template-native.js" type="text/javascript"></script><script src="source/plugin/chaoji_com/resource/js/cj-rivalcompare.js?' . TIMESTAMP . '" type="text/javascript"></script>
<link href="source/plugin/chaoji_com/resource/css/style.css?' . TIMESTAMP . '" rel="stylesheet" type="text/css" />
<script type="text/javascript" language="javascript" src="source/plugin/chaoji_com/resource/js/script.js?' . TIMESTAMP . '"></script>';
	chaojiapp_top_menu('spiderdata');

cpmsg(chaojiapp_lang('comming soon'), CJ_PLUGIN_URL . "pmod=overview", 'error');
?>