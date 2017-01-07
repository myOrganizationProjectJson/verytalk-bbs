<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

/**
 * 插件主类，管理代码和锚点的嵌入
 */
class plugin_baidurec
{
    protected $site = false;
    protected $data = false;

    public function __construct() {
        $filePath = dirname(__FILE__).DIRECTORY_SEPARATOR.'data';
        if (false === file_exists($filePath)) {
            return;
        }
        $this->data = @unserialize(file_get_contents($filePath));
        if (false === $this->data || !isset($this->data[0])) {
            return;
        }
        $this->site = $this->data[0];
        unset($this->data[0]);
        // 配置完整性检查，配置需要有planId和锚点位置信息:w
        foreach ($this->data as $index => $item) {
            if (!is_array($item) || 2 !== count($item)) {
                unset($this->data[$index]);
            }
        }
    }

    /**
     * 在Discuz的某个hook点嵌入推荐锚点
     * @param $pos
     * @return string
     */
    public function getAnchor($pos) {
        $return = '';
        foreach ($this->data as $item) {
            if ($item[1] === $pos) {
                $id = $item[0];
                $return .= '<div id="hm_t_'.$id.'" class="wp"></div>';
            }
        }
        return $return;
    }

    // 论坛首页，模块页，帖子列表页，帖子页顶部
    public function global_header() {
        global $_G;
        $pos = 0;
        $mod = $_G['mod'];
        switch ($mod) {
            case '':
                $pos = $this->isIndexPage() ? 1 : 3;
                break;
            case 'forumdisplay':
                $pos = 5;
                break;
            case 'viewthread':
                $pos = 9;
                break;
        }
        return $this->getAnchor($pos);
    }

    // 帖子页，帖子列表页，页面底部
    public function global_footer() {
        global $_G;
        $mod = $_G['mod'];
        $pos = 0;
        switch ($mod) {
            case 'forumdisplay':
                $pos = 6;
                break;
            case 'viewthread':
                $pos = 10;
                break;
        }
        return $this->getAnchor($pos);
    }

    // 论坛首页，模块列表页底部
    public function index_middle() {
        global $_G;
        $mod = $_G['mod'];
        $pos = 0;
        switch ($mod) {
            case '':
                $pos = $this->isIndexPage() ? 2 : 4;
                break;
        }
        return $this->getAnchor($pos);
    }

    // 帖子列表页顶部
    public function forumdisplay_top() {
        return $this->getAnchor(7);
    }

    public function forumdisplay_threadlist_bottom() {
        return $this->getAnchor(8);
    }

    public function global_footerlink() {
        if (false === $this->site) {
            return '';
        } else {
            $script = <<<EOT
<script type="text/javascript">
    var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F[SITE]' type='text/javascript'%3E%3C/script%3E"));
</script>
EOT;
            return str_replace('[SITE]', $this->site, $script);
        }
    }

    private function isIndexPage() {
        global $_G;
        $mod = $_G['mod'];
        $gid = isset($_GET['gid']) ? (int) $_GET['gid'] : NULL;
        return (empty($mod) && NULL === $gid);
    }
}

class plugin_baidurec_forum extends plugin_baidurec
{
    // 帖子正文页，正文下方
    public function viewthread_postbottom() {
        $return = '';
        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        if (1 === $currentPage) {
            $return = $this->getAnchor(12);
        }
        return array(0 => $return);
    }

    // 帖子正文页，正文上方
    public function viewthread_posttop() {
        $return = '';
        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        if (1 === $currentPage) {
            $return = $this->getAnchor(11);
        }
        return array(0 => $return);
    }

    // 帖子正文页，尾楼下方
    public function viewthread_endline() {
        global $_G;
        $replies = (int) $_G['thread']['replies'];
        $postPerPage = isset($_G['postperpage']) ? (int) $_G['postperpage'] : 10;
        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;

        $postCount = $replies + 1;
        $pageCount = (int) ($postCount / $postPerPage) + 1;

        if ($currentPage === $pageCount) {
            $lastPos = ($postCount % $postPerPage) - 1;
        } else {
            $lastPos = $postPerPage - 1;
        }
        return array($lastPos => $this->getAnchor(13));
    }

}
