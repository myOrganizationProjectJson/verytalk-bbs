<?php
namespace Home\Controller;
use Think\Controller;
class AdminController extends Controller {
    //URL访问规则：
    //http://localhost/ll/index.php/Home/Admin/index
    private $_subject;
    public function _initialize() {
        $this->_subject =M('SubjectMessage');
    }
    /**
     * 新增随机回复入口
     */
    public function index(){
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
        $data["type"] = $GOD['type'];
        $data["subject"] = $GOD['subject'];
        $data["message"] = $GOD['message'];
        $data["status"] = 1;
        $result=$this->_subject->add($data);
        if($result){
            $this->success("新增成功！");
        }else{
            $this->error('新增失败');
        }
    }
    public function type(){
        $this->display();
    }
    public function dotype(){
        $GOD= $_POST;
        $result=$this->_subject->where(array('type'=>$GOD['btype']))->save(array('type'=>$GOD['etype']));
        if($result){
            $this->success("转换成功！");
        }else{
            $this->error('新增失败');
        }
    }
}