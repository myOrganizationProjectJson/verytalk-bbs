<?php
// 本文档自动生成，仅供测试运行
class AdminAction extends Action
{
   private $_subject;
    public function _initialize() {
        $this->_subject =M('SubjectMessage');
    }
    /**
     * 新增随机回复入口
     */
    public function index(){
       $SubjectType=M('SubjectType')->select();
       $this->assign('SubjectType',$SubjectType);
       $this->display();
    }
    /**
     * 新增随机帖子入口
     * 
     *          <option value="1">随机回复</option>
				<option value="2">随机帖子</option>
				<option value="3">情感专题</option>
				<option value="4">幽默笑话</option>
				<option value="5">长篇别论</option>
				<option value="6">其他</option>
     */
    public function doinsert(){
        $GOD= $_POST;
        $type = $GOD['type'];
        $subject = $GOD['subject'];
        $message = $GOD['message'];
        $status = 1;
        $sql="INSERT INTO `pre_subject_message` (
        `type`,
        `subject`,
        `message`,
        `status`
        )
        VALUES
        (
        '$type',
        '$subject',
        '$message',
        '$status'
        );
 ";
        $result=M()->query($sql);
        if(is_array($result)){
           print_r("新增成功！");
        }else{
            print_r('新增失败');
        }
    }
    public function type(){
        $SubjectType=M('SubjectType')->select();
        $this->assign('SubjectType',$SubjectType);
        $this->display();
    }
    public function addtype(){
        $GOD= $_POST;
        $type = $GOD['type'];
        $status = 1;
        $sql="INSERT INTO `pre_subject_type` (
        `rename`,
        `status`
        )
        VALUES
        (
        '$type',
        '$status'
        );
        ";
        $result=M()->query($sql);
        if(is_array($result)){
            print_r("新增成功！");
        }else{
            print_r('新增失败');
        }
    }
}
?>