SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `pre_strayer_article_content`
-- ----------------------------
DROP TABLE IF EXISTS `pre_strayer_article_content`;
CREATE TABLE `pre_strayer_article_content` (
  `cid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `aid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `pageorder` smallint(6) unsigned NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL,
  `postid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`cid`),
  KEY `aid` (`aid`,`pageorder`),
  KEY `pageorder` (`pageorder`)
) ENGINE=MyISAM AUTO_INCREMENT=50493 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of pre_strayer_article_content
-- ----------------------------

-- ----------------------------
-- Table structure for `pre_strayer_article_title`
-- ----------------------------
DROP TABLE IF EXISTS `pre_strayer_article_title`;
CREATE TABLE `pre_strayer_article_title` (
  `is_bbs` tinyint(1) unsigned NOT NULL,
  `username` char(15) NOT NULL,
  `public_time` int(10) unsigned NOT NULL,
  `forum_typeid` mediumint(8) unsigned NOT NULL,
  `forum_fid` mediumint(8) unsigned NOT NULL,
  `blog_small_cid` mediumint(8) unsigned NOT NULL,
  `blog_big_cid` mediumint(8) unsigned NOT NULL,
  `portal_cid` mediumint(8) unsigned NOT NULL,
  `view_num` mediumint(8) unsigned NOT NULL,
  `from` varchar(255) NOT NULL,
  `fromurl` varchar(255) NOT NULL,
  `forum_id` mediumint(8) unsigned NOT NULL,
  `blog_id` mediumint(8) unsigned NOT NULL,
  `portal_id` mediumint(8) unsigned NOT NULL,
  `raids` text NOT NULL,
  `uid` mediumint(8) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `author` varchar(100) NOT NULL,
  `is_water_img` tinyint(1) NOT NULL,
  `article_tag` varchar(255) NOT NULL,
  `tag` tinyint(8) unsigned NOT NULL,
  `summary` varchar(255) NOT NULL,
  `is_download_img` tinyint(1) unsigned NOT NULL,
  `pid` int(10) unsigned NOT NULL,
  `pic` tinyint(3) unsigned NOT NULL,
  `file_count` tinyint(3) unsigned NOT NULL,
  `url_hash` char(32) NOT NULL,
  `url` varchar(255) NOT NULL,
  `last_modify` int(10) unsigned NOT NULL,
  `dateline` int(10) unsigned NOT NULL,
  `contents` smallint(6) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `reply_num` smallint(5) unsigned NOT NULL,
  `aid` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `article_dateline` int(10) unsigned NOT NULL,
  `public_pid` text NOT NULL,
  `reward_price` smallint(5) NOT NULL,
  `special` tinyint(1) unsigned NOT NULL,
  `cover_pic` varchar(255) NOT NULL,
  `sortid` smallint(6) unsigned NOT NULL,
  `best_answer_cid` int(9) unsigned NOT NULL,
  `attach_filesize_count` int(10) unsigned NOT NULL,
  PRIMARY KEY (`aid`),
  KEY `aid` (`aid`,`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=8861 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of pre_strayer_article_title
-- ----------------------------

-- ----------------------------
-- Table structure for `pre_strayer_attach`
-- ----------------------------
DROP TABLE IF EXISTS `pre_strayer_attach`;
CREATE TABLE `pre_strayer_attach` (
  `aid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL,
  `pid` int(10) unsigned NOT NULL,
  `save_name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `filesize` int(10) unsigned NOT NULL,
  `description` varchar(255) NOT NULL,
  `isimage` tinyint(1) unsigned NOT NULL,
  `hash` char(40) NOT NULL,
  `url_hash` char(40) NOT NULL,
  PRIMARY KEY (`aid`),
  KEY `tid` USING BTREE (`tid`,`pid`) 
) ENGINE=MyISAM AUTO_INCREMENT=15596 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of pre_strayer_attach
-- ----------------------------

-- ----------------------------
-- Table structure for `pre_strayer_category`
-- ----------------------------
DROP TABLE IF EXISTS `pre_strayer_category`;
CREATE TABLE `pre_strayer_category` (
  `displayorder` smallint(6) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `cid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=gbk;


-- ----------------------------
-- Table structure for `pre_strayer_evo`
-- ----------------------------
DROP TABLE IF EXISTS `pre_strayer_evo`;
CREATE TABLE `pre_strayer_evo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content_get_type` tinyint(1) NOT NULL,
  `content_rules` text NOT NULL,
  `theme_get_type` text NOT NULL,
  `theme_rules` text NOT NULL,
  `detail_ID` varchar(255) NOT NULL,
  `detail_ID_test` varchar(255) NOT NULL,
  `domain_hash` char(32) NOT NULL,
  `domain` varchar(100) NOT NULL,
  `hit_num` int(10) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  `dateline` int(10) unsigned NOT NULL,
  `evo_text_info` text NOT NULL,
  `evo_title_info` text NOT NULL,
  `detail_ID_hash` char(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `domain_hash` USING BTREE (`domain_hash`,`detail_ID_hash`) 
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of pre_strayer_evo
-- ----------------------------

-- ----------------------------
-- Table structure for `pre_strayer_evo_log`
-- ----------------------------
DROP TABLE IF EXISTS `pre_strayer_evo_log`;
CREATE TABLE `pre_strayer_evo_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `type` tinyint(1) unsigned NOT NULL,
  `why` tinyint(1) unsigned NOT NULL,
  `dateline` int(10) unsigned NOT NULL,
  `data_id` int(9) unsigned NOT NULL,
  `rules_name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of pre_strayer_evo_log
-- ----------------------------

-- ----------------------------
-- Table structure for `pre_strayer_fastpick`
-- ----------------------------
DROP TABLE IF EXISTS `pre_strayer_fastpick`;
CREATE TABLE `pre_strayer_fastpick` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dateline` int(9) unsigned NOT NULL,
  `rules_type` tinyint(1) unsigned NOT NULL,
  `rules_name` varchar(255) NOT NULL,
  `is_login` tinyint(1) unsigned NOT NULL,
  `login_cookie` text NOT NULL,
  `detail_ID` varchar(255) NOT NULL,
  `theme_url_test` varchar(255) NOT NULL,
  `theme_get_type` tinyint(1) unsigned NOT NULL,
  `theme_rules` text NOT NULL,
  `is_fiter_title` tinyint(1) unsigned NOT NULL,
  `title_replace_rules` text NOT NULL,
  `title_filter_rules` text NOT NULL,
  `content_rules` text NOT NULL,
  `is_fiter_content` tinyint(1) unsigned NOT NULL,
  `content_replace_rules` text NOT NULL,
  `content_filter_html` text NOT NULL,
  `content_filter_rules` text NOT NULL,
  `content_page_get_type` tinyint(1) unsigned NOT NULL,
  `content_page_rules` text NOT NULL,
  `content_page_get_mode` tinyint(1) unsigned NOT NULL,
  `rule_desc` varchar(255) NOT NULL,
  `rules_hash` char(32) NOT NULL,
  `content_get_type` tinyint(1) unsigned NOT NULL,
  `is_get_other` tinyint(1) unsigned NOT NULL,
  `from_get_type` tinyint(1) unsigned NOT NULL,
  `from_get_rules` text NOT NULL,
  `author_get_type` tinyint(1) unsigned NOT NULL,
  `author_get_rules` text NOT NULL,
  `dateline_get_type` tinyint(3) unsigned NOT NULL,
  `dateline_get_rules` text NOT NULL,
  `rule_author` varchar(200) NOT NULL,
  `charset_type` tinyint(1) unsigned NOT NULL,
  `is_get_threadtypes` tinyint(1) unsigned NOT NULL,
  `forum_threadtype_id` smallint(5) unsigned NOT NULL,
  `is_setting_article_page` tinyint(1) unsigned NOT NULL,
  `is_fiter_content_page_link` tinyint(1) unsigned NOT NULL,
  `content_page_link_replace_rules` text NOT NULL,
  `content_page_url_contain` text NOT NULL,
  `content_page_url_no_contain` text NOT NULL,
  `is_attach_setting` tinyint(1) unsigned NOT NULL,
  `attach_redirect_url_get_type` tinyint(1) unsigned NOT NULL,
  `attach_redirect_url_get_rules` text NOT NULL,
  `attach_download_url_get_type` tinyint(1) unsigned NOT NULL,
  `attach_download_url_get_rules` text NOT NULL,
  `forum_threadtypes` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of pre_strayer_fastpick
-- ----------------------------

-- ----------------------------
-- Table structure for `pre_strayer_member`
-- ----------------------------
DROP TABLE IF EXISTS `pre_strayer_member`;
CREATE TABLE `pre_strayer_member` (
  `username` char(15) NOT NULL,
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gender` char(8) NOT NULL DEFAULT '0',
  `birthyear` smallint(6) unsigned NOT NULL,
  `birthmonth` tinyint(3) unsigned NOT NULL,
  `birthday` tinyint(3) unsigned NOT NULL,
  `birthprovince` varchar(255) NOT NULL DEFAULT '',
  `birthcity` varchar(255) NOT NULL DEFAULT '',
  `birthdist` varchar(25) NOT NULL DEFAULT '',
  `birthcommunity` varchar(255) NOT NULL DEFAULT '',
  `resideprovince` varchar(255) NOT NULL DEFAULT '',
  `residecity` varchar(255) NOT NULL DEFAULT '',
  `residedist` varchar(20) NOT NULL DEFAULT '',
  `residecommunity` varchar(255) NOT NULL DEFAULT '',
  `residesuite` varchar(255) NOT NULL DEFAULT '',
  `email` char(40) NOT NULL DEFAULT '',
  `site` varchar(255) NOT NULL DEFAULT '',
  `bio` text NOT NULL,
  `zipcode` varchar(255) NOT NULL DEFAULT '',
  `interest` text NOT NULL,
  `oltime` smallint(6) unsigned NOT NULL,
  `regdate` int(9) unsigned NOT NULL,
  `lastvisit` int(9) unsigned NOT NULL,
  `address` varchar(255) NOT NULL DEFAULT '',
  `regip` char(15) NOT NULL DEFAULT '',
  `lastip` char(15) NOT NULL DEFAULT '',
  `lastactivity` int(10) unsigned NOT NULL DEFAULT '0',
  `lastpost` int(10) unsigned NOT NULL,
  `sightml` text NOT NULL,
  `idcardtype` varchar(255) NOT NULL DEFAULT '',
  `idcard` varchar(255) NOT NULL DEFAULT '',
  `bloodtype` varchar(255) NOT NULL DEFAULT '',
  `height` varchar(255) NOT NULL,
  `weight` varchar(255) NOT NULL DEFAULT '',
  `qq` varchar(255) NOT NULL DEFAULT '',
  `msn` varchar(255) NOT NULL DEFAULT '',
  `taobao` varchar(255) NOT NULL DEFAULT '',
  `yahoo` varchar(255) NOT NULL DEFAULT '',
  `icq` varchar(255) NOT NULL DEFAULT '',
  `alipay` varchar(255) NOT NULL DEFAULT '',
  `lookingfor` varchar(255) NOT NULL DEFAULT '',
  `position` varchar(255) NOT NULL DEFAULT '',
  `occupation` varchar(255) NOT NULL DEFAULT '',
  `education` varchar(255) NOT NULL DEFAULT '',
  `company` varchar(255) NOT NULL DEFAULT '',
  `graduateschool` varchar(255) NOT NULL DEFAULT '',
  `revenue` varchar(255) NOT NULL DEFAULT '',
  `telephone` varchar(255) NOT NULL DEFAULT '',
  `mobile` varchar(255) NOT NULL DEFAULT '',
  `constellation` varchar(255) NOT NULL DEFAULT '',
  `realname` varchar(255) NOT NULL DEFAULT '',
  `zodiac` varchar(255) NOT NULL,
  `affectivestatus` varchar(255) NOT NULL DEFAULT '',
  `data_uid` int(10) NOT NULL DEFAULT '0',
  `get_dateline` int(10) unsigned NOT NULL,
  `get_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `public_dateline` int(10) unsigned NOT NULL,
  `get_web_url` varchar(255) NOT NULL,
  `avatar_root_url` varchar(255) NOT NULL,
  `extcredits1` int(10) unsigned NOT NULL,
  `extcredits2` int(10) unsigned NOT NULL,
  `extcredits3` int(10) unsigned NOT NULL,
  `extcredits4` int(10) unsigned NOT NULL,
  `extcredits5` int(10) unsigned NOT NULL,
  `extcredits6` int(10) unsigned NOT NULL,
  `extcredits7` int(10) unsigned NOT NULL,
  `extcredits8` int(10) unsigned NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `get_dateline` USING BTREE (`get_dateline`,`uid`) 
) ENGINE=MyISAM AUTO_INCREMENT=10741 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of pre_strayer_member
-- ----------------------------

-- ----------------------------
-- Table structure for `pre_strayer_picker`
-- ----------------------------
DROP TABLE IF EXISTS `pre_strayer_picker`;
CREATE TABLE `pre_strayer_picker` (
  `pid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pick_cid` smallint(6) unsigned NOT NULL,
  `public_class` varchar(250) NOT NULL,
  `public_type` tinyint(1) unsigned NOT NULL,
  `pick_lastrun` int(10) unsigned NOT NULL,
  `pick_nextrun` int(10) unsigned NOT NULL,
  `timing_lastrun` int(10) NOT NULL,
  `timing_nextrun` int(10) unsigned NOT NULL,
  `login_test_url` varchar(250) NOT NULL,
  `login_cookie` text NOT NULL,
  `is_login` tinyint(1) unsigned NOT NULL,
  `is_public_del` tinyint(1) unsigned NOT NULL,
  `is_page_public` tinyint(1) unsigned NOT NULL,
  `reply_uid` varchar(250) NOT NULL,
  `public_reply_seq` tinyint(1) NOT NULL,
  `is_public_reply` tinyint(1) unsigned NOT NULL,
  `reply_max_num` varchar(100) NOT NULL,
  `article_min_len` smallint(6) unsigned NOT NULL,
  `run_times` smallint(5) unsigned NOT NULL,
  `only_in_domain` tinyint(1) unsigned NOT NULL,
  `max_redirs` tinyint(1) unsigned NOT NULL,
  `reply_get_type` tinyint(1) unsigned NOT NULL,
  `time_out` tinyint(1) unsigned NOT NULL,
  `page_url_auto` tinyint(1) unsigned NOT NULL,
  `is_auto_public` tinyint(1) unsigned NOT NULL,
  `content_page_get_mode` tinyint(1) unsigned NOT NULL,
  `content_page_get_type` tinyint(1) unsigned NOT NULL,
  `public_start_time` int(10) NOT NULL,
  `public_end_time` int(10) NOT NULL,
  `reply_filter_rules` text NOT NULL,
  `content_filter_rules` text NOT NULL,
  `title_filter_rules` text NOT NULL,
  `rss_url` text NOT NULL,
  `reply_filter_html` text NOT NULL,
  `content_filter_html` text NOT NULL,
  `many_page_list` text NOT NULL,
  `manyou_max_level` tinyint(1) unsigned NOT NULL,
  `manyou_start_url` varchar(250) NOT NULL,
  `rules_type` tinyint(1) unsigned NOT NULL,
  `rules_hash` char(32) NOT NULL,
  `rules_var` text NOT NULL,
  `is_download_file` tinyint(1) NOT NULL,
  `auto_del_ad` tinyint(1) NOT NULL,
  `is_auto_pick` tinyint(1) NOT NULL,
  `jump_num` tinyint(5) unsigned NOT NULL,
  `public_uid` varchar(250) NOT NULL,
  `reply_fiter_replace` text NOT NULL,
  `is_fiter_reply` tinyint(1) NOT NULL,
  `page_link_rules` text NOT NULL,
  `page_get_type` tinyint(1) NOT NULL,
  `page_fiter` tinyint(1) NOT NULL,
  `page_url_other` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL,
  `url_range_type` tinyint(1) NOT NULL,
  `reply_is_extend` tinyint(1) unsigned NOT NULL,
  `page_url_auto_step` tinyint(5) NOT NULL DEFAULT '1',
  `rules_match_url` varchar(250) NOT NULL,
  `name` varchar(50) NOT NULL,
  `view_num` char(20) NOT NULL,
  `url_page_range` varchar(250) NOT NULL,
  `theme_get_type` tinyint(1) NOT NULL,
  `content_page_rules` text NOT NULL,
  `page_url_auto_start` mediumint(8) NOT NULL,
  `reply_replace_rules` text NOT NULL,
  `title_replace_rules` text NOT NULL,
  `page_url_auto_end` mediumint(8) NOT NULL,
  `many_list_start_url` varchar(250) NOT NULL,
  `page_url_no_contain` text NOT NULL,
  `page_url_contain` text NOT NULL,
  `pick_num` smallint(6) unsigned NOT NULL,
  `is_download_img` tinyint(1) NOT NULL,
  `is_water_img` tinyint(1) NOT NULL,
  `page_url_no_other` text NOT NULL,
  `theme_url_test` text NOT NULL,
  `page_url_test` varchar(250) NOT NULL,
  `content_get_type` tinyint(1) NOT NULL,
  `keyword_title` text NOT NULL,
  `theme_rules` text NOT NULL,
  `content_is_page` tinyint(1) NOT NULL,
  `keyword_flag` tinyint(1) unsigned NOT NULL,
  `keyword_title_exclude` text NOT NULL,
  `keyword_content` text NOT NULL,
  `page_link_fiter` tinyint(1) unsigned NOT NULL,
  `page_link_url_contain` text NOT NULL,
  `page_link_url_no_contain` text NOT NULL,
  `keyword_content_exclude` text NOT NULL,
  `public_uid_type` tinyint(1) unsigned NOT NULL,
  `public_uid_group` varchar(255) NOT NULL,
  `reply_uid_type` tinyint(1) unsigned NOT NULL,
  `reply_uid_group` varchar(255) NOT NULL,
  `is_fiter_title` tinyint(1) unsigned NOT NULL,
  `content_rules` text NOT NULL,
  `is_fiter_content` tinyint(1) unsigned NOT NULL,
  `content_replace_rules` text NOT NULL,
  `reply_rules` text NOT NULL,
  `is_word_replace` tinyint(1) unsigned NOT NULL,
  `stop_time` char(15) NOT NULL,
  `displayorder` smallint(6) unsigned NOT NULL,
  `picker_hash` char(32) NOT NULL,
  `article_public_sort` tinyint(1) unsigned NOT NULL,
  `is_get_other` tinyint(1) unsigned NOT NULL,
  `from_get_type` tinyint(1) unsigned NOT NULL,
  `from_get_rules` text NOT NULL,
  `author_get_type` tinyint(1) unsigned NOT NULL,
  `author_get_rules` text NOT NULL,
  `dateline_get_type` tinyint(1) unsigned NOT NULL,
  `dateline_get_rules` text NOT NULL,
  `public_time_type` tinyint(3) unsigned NOT NULL,
  `reply_dateline` varchar(25) NOT NULL,
  `charset_type` tinyint(1) unsigned NOT NULL,
  `is_fiter_page_link` tinyint(1) unsigned NOT NULL,
  `page_link_replace_rules` text NOT NULL,
  `is_get_threadtypes` tinyint(1) unsigned NOT NULL,
  `forum_threadtype_id` smallint(6) unsigned NOT NULL,
  `forum_threadtypes` mediumtext NOT NULL,
  `is_fiter_content_page_link` tinyint(1) unsigned NOT NULL,
  `content_page_link_replace_rules` text NOT NULL,
  `content_page_url_contain` text NOT NULL,
  `content_page_url_no_contain` text NOT NULL,
  `pick_article_num` smallint(5) unsigned NOT NULL,
  `pick_cron_loop_type` varchar(10) NOT NULL,
  `pick_cron_loop_daytime` varchar(50) NOT NULL,
  `timing_article_num` smallint(6) unsigned NOT NULL,
  `timing_cron_loop_type` varchar(10) NOT NULL,
  `timing_cron_loop_daytime` varchar(50) NOT NULL,
  `is_auto_timing` tinyint(1) unsigned NOT NULL,
  `is_attach_setting` tinyint(1) unsigned NOT NULL,
  `is_set_referer` tinyint(1) unsigned NOT NULL,
  `is_auto_add_reply` tinyint(1) unsigned NOT NULL,
  `auto_add_reply_min_num` smallint(5) unsigned NOT NULL,
  `auto_add_reply_num` varchar(15) NOT NULL,
  `article_num` mediumint(9) unsigned NOT NULL,
  `article_import_num` mediumint(9) NOT NULL,
  `visit_url_num` int(11) unsigned NOT NULL,
  `is_pick_cover_from_listpage` tinyint(1) unsigned NOT NULL,
  `pick_cover_rules_get_type` tinyint(1) unsigned NOT NULL,
  `pick_cover_rules_get_rules` text NOT NULL,
  `attach_redirect_url_get_type` tinyint(1) unsigned NOT NULL,
  `attach_redirect_url_get_rules` text NOT NULL,
  `attach_download_url_get_type` tinyint(1) unsigned NOT NULL,
  `attach_download_url_get_rules` text NOT NULL,
  `cookie_test_no_hava` text NOT NULL,
  `cookie_test_hava` text NOT NULL,
  `is_get_thread_user` tinyint(1) unsigned NOT NULL,
  `is_get_post_user` tinyint(1) unsigned NOT NULL,
  `thread_user_get_type` tinyint(1) unsigned NOT NULL,
  `thread_user_get_rules` text NOT NULL,
  `thread_dateline_get_type` tinyint(1) unsigned NOT NULL,
  `thread_dateline_get_rules` text NOT NULL,
  `post_user_get_type` tinyint(1) NOT NULL,
  `post_user_get_rules` text NOT NULL,
  `post_dateline_get_type` tinyint(1) NOT NULL,
  `post_dateline_get_rules` text NOT NULL,
  `is_setting_best_answer` tinyint(3) unsigned NOT NULL,
  `best_answer_get_type` tinyint(1) unsigned NOT NULL,
  `best_answer_get_rules` text NOT NULL,
  `is_use_thread_setting` tinyint(1) unsigned NOT NULL,
  `attach_download_allow_ext` text NOT NULL,
  `is_pick_download_on` tinyint(1) unsigned NOT NULL,
  `is_html_public` tinyint(1) NOT NULL,
  `content_no_contain` text NOT NULL,
  `open_seo` tinyint(1) unsigned NOT NULL,
  `push_title_header` text NOT NULL,
  `push_title_footer` text NOT NULL,
  `push_content_header` text NOT NULL,
  `push_content_footer` text NOT NULL,
  `push_reply_header` text NOT NULL,
  `push_reply_footer` text NOT NULL,
  `is_get_reply` tinyint(1) unsigned NOT NULL,
  `is_setting_article_page` tinyint(1) unsigned NOT NULL,
  `is_check_title` tinyint(1) unsigned NOT NULL,
  `best_answer_flag` text NOT NULL,
  `ask_reward_price_get_type` tinyint(1) unsigned NOT NULL,
  `ask_reward_price_get_rules` text NOT NULL,
  `is_get_user_other` tinyint(1) unsigned NOT NULL,
  `user_other_rules` text NOT NULL,
  `auto_pick_from_last` tinyint(1) unsigned NOT NULL,
  `timing_article_public_type` tinyint(1) unsigned NOT NULL,
  `attach_link_hava` text NOT NULL,
  `attach_link_text_hava` text NOT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=113 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of pre_strayer_picker
-- ----------------------------

-- ----------------------------
-- Table structure for `pre_strayer_rules`
-- ----------------------------
DROP TABLE IF EXISTS `pre_strayer_rules`;
CREATE TABLE `pre_strayer_rules` (
  `reply_get_type` tinyint(1) unsigned NOT NULL,
  `dateline` int(10) unsigned NOT NULL,
  `content_page_get_type` tinyint(1) unsigned NOT NULL,
  `content_page_get_mode` tinyint(1) unsigned NOT NULL,
  `content_page_rules` text NOT NULL,
  `is_fiter_reply` tinyint(1) unsigned NOT NULL,
  `content_get_type` tinyint(1) unsigned NOT NULL,
  `rules_name` varchar(80) NOT NULL,
  `page_url_test` varchar(250) NOT NULL,
  `reply_page_link_rules` varchar(250) NOT NULL,
  `content_rules` text NOT NULL,
  `reply_replace_rules` text NOT NULL,
  `rule_desc` varchar(250) NOT NULL,
  `filter_rules` varchar(250) NOT NULL,
  `page_get_type` tinyint(4) NOT NULL,
  `reply_filter_rules` text NOT NULL,
  `list_ID` varchar(250) NOT NULL,
  `list_ID_test` varchar(250) NOT NULL,
  `url_var` text NOT NULL,
  `detail_ID_test` varchar(250) NOT NULL,
  `detail_ID` varchar(250) NOT NULL,
  `reply_rules` varchar(250) NOT NULL,
  `theme_get_type` tinyint(1) NOT NULL,
  `page_link_rules` text NOT NULL,
  `is_fiter_content` tinyint(1) NOT NULL,
  `reply_is_extend` tinyint(1) NOT NULL,
  `is_fiter_title` tinyint(1) NOT NULL,
  `title_replace_rules` text NOT NULL,
  `content_filter_rules` text NOT NULL,
  `title_filter_rules` text NOT NULL,
  `content_replace_rules` text NOT NULL,
  `theme_rules` varchar(250) NOT NULL,
  `reply_page_url_test` varchar(250) NOT NULL,
  `theme_url_test` varchar(250) NOT NULL,
  `page_url` varchar(250) NOT NULL,
  `rules_type` tinyint(1) unsigned NOT NULL,
  `rules_hash` char(32) NOT NULL,
  `rid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `is_get_other` tinyint(1) unsigned NOT NULL,
  `from_get_type` tinyint(1) unsigned NOT NULL,
  `author_get_type` tinyint(1) unsigned NOT NULL,
  `from_get_rules` text NOT NULL,
  `dateline_get_type` tinyint(1) unsigned NOT NULL,
  `author_get_rules` text NOT NULL,
  `dateline_get_rules` text NOT NULL,
  `rule_author` varchar(200) NOT NULL,
  `charset_type` tinyint(1) unsigned NOT NULL,
  `is_fiter_page_link` tinyint(1) unsigned NOT NULL,
  `page_link_replace_rules` text NOT NULL,
  `page_url_no_contain` text NOT NULL,
  `page_url_contain` text NOT NULL,
  `content_no_contain` text NOT NULL,
  `is_pick_cover_from_listpage` tinyint(1) unsigned NOT NULL,
  `pick_cover_rules_get_type` tinyint(1) unsigned NOT NULL,
  `pick_cover_rules_get_rules` text NOT NULL,
  `is_get_thread_user` tinyint(1) unsigned NOT NULL,
  `thread_user_get_type` tinyint(1) unsigned NOT NULL,
  `thread_user_get_rules` text NOT NULL,
  `thread_dateline_get_type` tinyint(1) unsigned NOT NULL,
  `thread_dateline_get_rules` text NOT NULL,
  `is_get_user_other` tinyint(1) unsigned NOT NULL,
  `user_other_rules` text NOT NULL,
  `is_get_threadtypes` tinyint(1) unsigned NOT NULL,
  `forum_threadtype_id` tinyint(1) unsigned NOT NULL,
  `is_get_reply` tinyint(1) NOT NULL,
  `is_setting_best_answer` tinyint(1) unsigned NOT NULL,
  `best_answer_get_type` tinyint(1) unsigned NOT NULL,
  `best_answer_get_rules` text NOT NULL,
  `best_answer_flag` tinyint(1) unsigned NOT NULL,
  `ask_reward_price_get_type` tinyint(1) unsigned NOT NULL,
  `ask_reward_price_get_rules` text NOT NULL,
  `is_get_post_user` tinyint(1) unsigned NOT NULL,
  `post_user_get_type` tinyint(1) unsigned NOT NULL,
  `post_user_get_rules` text NOT NULL,
  `post_dateline_get_type` tinyint(1) unsigned NOT NULL,
  `post_dateline_get_rules` text NOT NULL,
  `is_setting_article_page` tinyint(1) unsigned NOT NULL,
  `is_fiter_content_page_link` tinyint(1) unsigned NOT NULL,
  `content_page_link_replace_rules` text NOT NULL,
  `content_page_url_contain` text NOT NULL,
  `content_page_url_no_contain` text NOT NULL,
  `is_attach_setting` tinyint(1) unsigned NOT NULL,
  `attach_redirect_url_get_type` tinyint(1) unsigned NOT NULL,
  `attach_redirect_url_get_rules` text NOT NULL,
  `attach_download_url_get_type` tinyint(1) NOT NULL,
  `attach_download_url_get_rules` text NOT NULL,
  `forum_threadtypes` mediumtext NOT NULL,
  PRIMARY KEY (`rid`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of pre_strayer_rules
-- ----------------------------

-- ----------------------------
-- Table structure for `pre_strayer_searchindex`
-- ----------------------------
DROP TABLE IF EXISTS `pre_strayer_searchindex`;
CREATE TABLE `pre_strayer_searchindex` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain_hash` char(32) NOT NULL,
  `rid` int(9) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL,
  `dateline` int(10) unsigned NOT NULL,
  `path_hash` char(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `domain_hash` (`domain_hash`),
  KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=312 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of pre_strayer_searchindex
-- ----------------------------

-- ----------------------------
-- Table structure for `pre_strayer_setting`
-- ----------------------------
DROP TABLE IF EXISTS `pre_strayer_setting`;
CREATE TABLE `pre_strayer_setting` (
  `skey` varchar(255) NOT NULL DEFAULT '',
  `svalue` text NOT NULL,
  PRIMARY KEY (`skey`)
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of pre_strayer_setting
-- ----------------------------
INSERT INTO `pre_strayer_setting` VALUES ('url', 'http://www.discuz.net/');
INSERT INTO `pre_strayer_setting` VALUES ('uid_range', '424738,45895958');
INSERT INTO `pre_strayer_setting` VALUES ('is_login', '2');
INSERT INTO `pre_strayer_setting` VALUES ('login_cookie', '');
INSERT INTO `pre_strayer_setting` VALUES ('num', '100000');
INSERT INTO `pre_strayer_setting` VALUES ('member_field', 'N;');
INSERT INTO `pre_strayer_setting` VALUES ('jump_num', '50');
INSERT INTO `pre_strayer_setting` VALUES ('username_chinese', '2');
INSERT INTO `pre_strayer_setting` VALUES ('reg_pwd', 'dfG56#$fg');
INSERT INTO `pre_strayer_setting` VALUES ('reg_num', '100000');
INSERT INTO `pre_strayer_setting` VALUES ('reg_jump_num', '500');
INSERT INTO `pre_strayer_setting` VALUES ('regdate_type', '1');
INSERT INTO `pre_strayer_setting` VALUES ('regdate_start_time', '');
INSERT INTO `pre_strayer_setting` VALUES ('public_end_time', '');
INSERT INTO `pre_strayer_setting` VALUES ('lastvisit_type', '1');
INSERT INTO `pre_strayer_setting` VALUES ('public_start_time', '');
INSERT INTO `pre_strayer_setting` VALUES ('lastactivity_type', '1');
INSERT INTO `pre_strayer_setting` VALUES ('lastactivity_start_time', '');
INSERT INTO `pre_strayer_setting` VALUES ('lastactivity_end_time', '');
INSERT INTO `pre_strayer_setting` VALUES ('regip', '');
INSERT INTO `pre_strayer_setting` VALUES ('oltime', '0');
INSERT INTO `pre_strayer_setting` VALUES ('extcredits1_type', '2');
INSERT INTO `pre_strayer_setting` VALUES ('extcredits1', '0');
INSERT INTO `pre_strayer_setting` VALUES ('extcredits2_type', '2');
INSERT INTO `pre_strayer_setting` VALUES ('extcredits2', '0');
INSERT INTO `pre_strayer_setting` VALUES ('extcredits3_type', '2');
INSERT INTO `pre_strayer_setting` VALUES ('extcredits3', '0');
INSERT INTO `pre_strayer_setting` VALUES ('avata_jump_num', '50');
INSERT INTO `pre_strayer_setting` VALUES ('set_type', '1');
INSERT INTO `pre_strayer_setting` VALUES ('oltime_type', '2');
INSERT INTO `pre_strayer_setting` VALUES ('avatar_user_set', '1,28169');
INSERT INTO `pre_strayer_setting` VALUES ('avatar_setting_member', '1');
INSERT INTO `pre_strayer_setting` VALUES ('rand_ip', '202.106.189.3,202.106.189.4,202.106.189.6,218.247.166.82,218.30.119.114,218.30.119.114 4408,218.64.220.220,218.64.220.2,219.148.122.113,219.232.236.116,221.225.1.239,222.188.10.1,222.223.65.3,222.73.26.211,58.211.0.113,58.214.238.238,202.105.55.38,221.179.35.71,222.64.185.148,114.255.171.231,125.77.200.134');
INSERT INTO `pre_strayer_setting` VALUES ('public_groupid', 'N;');
INSERT INTO `pre_strayer_setting` VALUES ('avatar_web_url', 'http://uc.discuz.net/');
INSERT INTO `pre_strayer_setting` VALUES ('avata_from_uid', '1571450');
INSERT INTO `pre_strayer_setting` VALUES ('cover_avatar', '1');
INSERT INTO `pre_strayer_setting` VALUES ('regdate_end_time', '');
INSERT INTO `pre_strayer_setting` VALUES ('ip_type', '1');
INSERT INTO `pre_strayer_setting` VALUES ('push_title_header', '');
INSERT INTO `pre_strayer_setting` VALUES ('push_title_footer', '');
INSERT INTO `pre_strayer_setting` VALUES ('push_content_header', '');
INSERT INTO `pre_strayer_setting` VALUES ('push_content_body', '');
INSERT INTO `pre_strayer_setting` VALUES ('push_content_footer', '');
INSERT INTO `pre_strayer_setting` VALUES ('push_reply_header', '');
INSERT INTO `pre_strayer_setting` VALUES ('push_reply_body', '');
INSERT INTO `pre_strayer_setting` VALUES ('push_reply_footer', '');
INSERT INTO `pre_strayer_setting` VALUES ('vir_open', '2');
INSERT INTO `pre_strayer_setting` VALUES ('vir_online_member_count', '20');
INSERT INTO `pre_strayer_setting` VALUES ('vir_online_guest_count', '60,80');
INSERT INTO `pre_strayer_setting` VALUES ('vir_data_bei', '0');
INSERT INTO `pre_strayer_setting` VALUES ('online_data_from', '2');
INSERT INTO `pre_strayer_setting` VALUES ('online_data_user_set', '23423|435634');
INSERT INTO `pre_strayer_setting` VALUES ('vir_must_online', '');
INSERT INTO `pre_strayer_setting` VALUES ('vir_data_forum', '');
INSERT INTO `pre_strayer_setting` VALUES ('vir_data_usergroup', 'a:3:{i:0;s:2:\"10\";i:1;s:2:\"11\";i:2;s:2:\"12\";}');
INSERT INTO `pre_strayer_setting` VALUES ('vir_cache_time', '10');
INSERT INTO `pre_strayer_setting` VALUES ('fp_open', '2');
INSERT INTO `pre_strayer_setting` VALUES ('fp_cloud_open', '1');
INSERT INTO `pre_strayer_setting` VALUES ('fp_usergroup', 'a:1:{i:0;s:1:\"1\";}');
INSERT INTO `pre_strayer_setting` VALUES ('fp_forum', '');
INSERT INTO `pre_strayer_setting` VALUES ('fp_article_from', '1');
INSERT INTO `pre_strayer_setting` VALUES ('fp_seo_open', '2');
INSERT INTO `pre_strayer_setting` VALUES ('fp_word_replace_open', '2');
INSERT INTO `pre_strayer_setting` VALUES ('fp_open_auto', '1');
INSERT INTO `pre_strayer_setting` VALUES ('0', '1');
INSERT INTO `pre_strayer_setting` VALUES ('fp_open_evo', '1');
INSERT INTO `pre_strayer_setting` VALUES ('push_open_bbshide', '2');
INSERT INTO `pre_strayer_setting` VALUES ('article_batch_num', '15');
INSERT INTO `pre_strayer_setting` VALUES ('fp_open_mod', 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}');
INSERT INTO `pre_strayer_setting` VALUES ('login_test_url', 'http://www.discuz.net/home.php?mod=space&uid=1575902&do=profile');
INSERT INTO `pre_strayer_setting` VALUES ('clear_log', '1406702732');
INSERT INTO `pre_strayer_setting` VALUES ('clear_search_index', '1406570957');
INSERT INTO `pre_strayer_setting` VALUES ('pick_clear_cache', '1406702732');
INSERT INTO `pre_strayer_setting` VALUES ('open_seo', '1');
INSERT INTO `pre_strayer_setting` VALUES ('open_seo_mod', 'a:3:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";}');
INSERT INTO `pre_strayer_setting` VALUES ('is_cron', '2');
INSERT INTO `pre_strayer_setting` VALUES ('is_cron_run_sametime_open', '1');
INSERT INTO `pre_strayer_setting` VALUES ('is_log_cron', '1');
INSERT INTO `pre_strayer_setting` VALUES ('is_timing', '2');
INSERT INTO `pre_strayer_setting` VALUES ('open_tag', '1');
INSERT INTO `pre_strayer_setting` VALUES ('is_set_referer', '2');
INSERT INTO `pre_strayer_setting` VALUES ('is_thread_htmlon', '2');
INSERT INTO `pre_strayer_setting` VALUES ('is_reply_htmlon', '2');
INSERT INTO `pre_strayer_setting` VALUES ('is_check_title', '2');
INSERT INTO `pre_strayer_setting` VALUES ('is_pick_download_on', '1');
INSERT INTO `pre_strayer_setting` VALUES ('is_ask_mode_open', '2');
INSERT INTO `pre_strayer_setting` VALUES ('is_reply_hide_on', '1');
INSERT INTO `pre_strayer_setting` VALUES ('pick_tips', 'a:1:{s:13:\"check_version\";s:1:\"1\";}');
INSERT INTO `pre_strayer_setting` VALUES ('target_key_code', '');
INSERT INTO `pre_strayer_setting` VALUES ('trans_type', '1');
INSERT INTO `pre_strayer_setting` VALUES ('local_key_code', '');
INSERT INTO `pre_strayer_setting` VALUES ('tran_picker_cid', '0');
INSERT INTO `pre_strayer_setting` VALUES ('tran_picker_pid', '0');
INSERT INTO `pre_strayer_setting` VALUES ('cron_check_time', '5');
INSERT INTO `pre_strayer_setting` VALUES ('skydrive_type', '0');
INSERT INTO `pre_strayer_setting` VALUES ('baidu_bucket', '');
INSERT INTO `pre_strayer_setting` VALUES ('baidu_ak', '');
INSERT INTO `pre_strayer_setting` VALUES ('baidu_sk', '');
INSERT INTO `pre_strayer_setting` VALUES ('tran_is_open', '2');
INSERT INTO `pre_strayer_setting` VALUES ('tran_open_par', '2');
INSERT INTO `pre_strayer_setting` VALUES ('tran_title_mode', '0');
INSERT INTO `pre_strayer_setting` VALUES ('tran_open_user_words', '2');
INSERT INTO `pre_strayer_setting` VALUES ('tran_user_words', '');
INSERT INTO `pre_strayer_setting` VALUES ('fanyi_min_length', '');
INSERT INTO `pre_strayer_setting` VALUES ('tran_api_type', '1');
INSERT INTO `pre_strayer_setting` VALUES ('tran_api_test_words', '');
INSERT INTO `pre_strayer_setting` VALUES ('tran_api_key', 'a:2:{s:5:\"baidu\";a:1:{s:7:\"app_key\";s:0:\"\";}s:6:\"google\";a:1:{s:7:\"app_key\";s:0:\"\";}}');
INSERT INTO `pre_strayer_setting` VALUES ('tran_aplay_picker', 'a:4:{i:0;s:2:\"48\";i:1;s:2:\"43\";i:2;s:2:\"57\";i:3;s:2:\"54\";}');
INSERT INTO `pre_strayer_setting` VALUES ('start_dateline', '1404199541');
INSERT INTO `pre_strayer_setting` VALUES ('avatar_from_type', '1');
INSERT INTO `pre_strayer_setting` VALUES ('pick_today', 'a:2:{s:3:\"day\";s:4:\"0730\";s:18:\"article_public_num\";s:1:\"8\";}');

-- ----------------------------
-- Table structure for `pre_strayer_timing`
-- ----------------------------
DROP TABLE IF EXISTS `pre_strayer_timing`;
CREATE TABLE `pre_strayer_timing` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL,
  `data_id` int(10) unsigned NOT NULL,
  `public_type` tinyint(1) unsigned NOT NULL,
  `public_dateline` int(10) unsigned NOT NULL,
  `public_info` text NOT NULL,
  `content_type` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `aid` USING BTREE (`data_id`,`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=1388 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of pre_strayer_timing
-- ----------------------------

-- ----------------------------
-- Table structure for `pre_strayer_typeoptionvar`
-- ----------------------------
DROP TABLE IF EXISTS `pre_strayer_typeoptionvar`;
CREATE TABLE `pre_strayer_typeoptionvar` (
  `optionid` smallint(6) unsigned NOT NULL,
  `value` mediumtext NOT NULL,
  `aid` int(12) unsigned NOT NULL,
  KEY `aid` USING BTREE (`aid`) 
) ENGINE=MyISAM DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of pre_strayer_typeoptionvar
-- ----------------------------

-- ----------------------------
-- Table structure for `pre_strayer_url`
-- ----------------------------
DROP TABLE IF EXISTS `pre_strayer_url`;
CREATE TABLE `pre_strayer_url` (
  `pid` mediumint(10) unsigned NOT NULL,
  `dateline` int(10) NOT NULL,
  `host` varchar(50) NOT NULL,
  `hash` char(32) NOT NULL,
  `uid` int(12) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`uid`),
  KEY `hash` (`hash`)
) ENGINE=MyISAM AUTO_INCREMENT=10370 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of pre_strayer_url
-- ----------------------------
