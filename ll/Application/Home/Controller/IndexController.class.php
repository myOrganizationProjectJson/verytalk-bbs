<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    //URL访问规则：
    //http://localhost/ll/index.php/Home/Index/users
    private $_record;
    private $_thread;
    private $_forum;
    private $_users;
    public function _initialize() {
        $this->_record = M ( 'ForumPost' );
        $this->_thread = M ( 'ForumThread' );
        $this->_forum = M ( 'ForumForum' );
        $this->_users = M ( 'CommonMember' );
    }
    /**
     * 新增随机回复入口
     */
    public function index(){
       $this->display();
    }
    /**
     * 新增随机帖子入口
     */
    public function index_t(){
        $this->display();
    }
    /**
     * 新增随机回复
     */
    public function start_post(){
        if(!empty($_POST)){
            $GOD=$_POST;
            $s=0;$e=0;
            $gettype=$GOD['gettype'];
            if($GOD['type']==rand){
                if($GOD['member']){
                    for($i=0;$i<$GOD['member'];$i++){
                         $result=$this-> addpost('','','','','','',false,false,$gettype);
                         if(is_array($result)&&$result!=0){
                             $s++;
                         }else{
                             $e++;
                         }
                    }
                }else{
                    //新增随机回复
                   $result=$this-> addpost('','','','','','',false,false,$gettype);
                    $s++;
                   }
                 $this->upforum();
                 $this->success("插入成功 :".$s."失败:".$e);
             
             }else if($GOD['type']==no_rand){   
                 $fid=$GOD['fid'];
                 $tid=$GOD['tid'];
                 $subject=$GOD['subject'];
                 $message=$GOD['message'];
                 $author_post_id=$GOD['author_post_id'];
                 $result=$this->addpost($fid,$tid,'','',$subject,$message,true,$author_post_id,$gettype);
                 if(is_array($result)&&$result!=0){
                             $s++;
                         }else{
                             $e++;
                         }
                $this->upforum();
                $this->success("插入成功 :".$s."失败:".$e);
           }else{
               //出错了
               $this->error('出错嘞...');
           }
        }else{
                 
                 $this->error('出错嘞...');
            }
    }
    /**
     * 新增随机帖子
     */
    public function start_thread(){
        if(!empty($_POST)){
            $GOD=$_POST;
            $s=0;$e=0;
            $gettype=$GOD['gettype'];
            if($GOD['type']==rand){
                //新增随机回复
                if($GOD['member']){
                    for($i=0;$i<$GOD['member'];$i++){
                        $result=$this->addthread('','','','',false, $gettype);
                        if(is_array($result)&&$result!=0){
                            $s++;
                        }else{
                            $e++;
                        }
                    }
                }else{
                     $result=$this->addthread('','','','',false, $gettype);
                    $s++;
                }   
                    $this->upforum();
                    $this->success("插入成功 :".$s."失败:".$e);
            }else if($GOD['type']==no_rand){
                $fid=$GOD['fid'];
                $subject=$GOD['subject'];
                $message=$GOD['message'];
                $author_post_id=$GOD['author_post_id'];
                $result= $this->addthread($author_post_id,$fid,$subject,$message,true,$gettype);
                 if(is_array($result)&&$result!=0){
                        $s++;
                    }else{
                        $e++;
                    }
                    $this->upforum();
                    $this->success("插入成功 :".$s."失败:".$e);
            }else{
                 $this->error('出错嘞...');
            }
        }else{
                 $this->error('出错嘞...');
            }
    }
    
    /**
     * 获取用户
     */
    private function users($uid=''){
        $password='28cef27409ecd417e4134fc29d256804';
        if(empty($uid)){
            $where=array('password'=>$password);
        }else{
            $where=array('uid'=>$uid);
        }
        $users=$this->_users->where($where)->field('uid,username')->select();
        return $users;
    }
    
    /**
     * 获取随机用户
     */
    private function rand_user(){
        $users=$this->users();
        $rand_user_id=array_rand($users,1);
        return $users[$rand_user_id];
    }
    /**
     * 获取板块id
     */
    private function get_forum($get=false){
       $fup=array('NEQ','0');
       $fid=$this->_forum->where(array('status'=>'1','fup'=>$fup))->field('fid')->select();
       if($get==false){
       $fid_key=array_rand($fid,1);
       return $fid[$fid_key];
       }else if($get==true){
           return $fid;
       }
    }
    /**
     * 获取回复id
     * @return unknown
     */
    private function get_thread($fid='',$get=false){
        if(empty($fid)){
            $fid='1=1';
        }else{
            $fid=array('fid'=>$fid);
        }
        $tid=$this->_thread->where($fid)->field('tid')->select();
        if($get==false){
        $tid_key=array_rand($tid,1);
        return $tid[$tid_key];
        }else if($get==true){
        return $tid;
        }
        
    }
    
    /**
     * 获取文章标题和文章
     * @param string $type
     * @return unknown
     * 
     *          $type
     *          <option value="1">随机回复</option>
				<option value="2">随机帖子</option>
				<option value="3">情感专题</option>
				<option value="4">幽默笑话</option>
				<option value="5">长篇别论</option>
				<option value="6">其他</option>
     */
    public function getsubject($type=''){
        if(empty($type)){
            $condition=array(
                'status'=>'1'
            );
        }else{
            $condition=array(
                'status'=>'1',
                'type'=>$type
            );
        }
       $Subject=M('SubjectMessage')->where($condition)->field('subject,message')->select();
       $Subject_key=array_rand($Subject,1);
       return $Subject[$Subject_key];
    }
    /**
     * 新增帖子
     */
    public function addthread($user_id='',$fid='',$subject='',$message='',$type=false, $gettype=2){
        $tid=$this->_thread->field('max(tid)')->find();
        $tid=$tid['max(tid)']+1;
        if($type==false){
        $rand_user= $this->rand_user();
        $forum=$this->get_forum();
        $fid=$forum['fid'];
        $authorid=$rand_user['uid'];
        $author=$rand_user['username'];
        $getsubject=$this->getsubject($gettype);
        $subject=$getsubject['subject'];
        $message=$getsubject['message'];
        }else{
            if(!empty($user_id)){
                $author_post=$this->users($user_id);
                $authorid=$author_post[0]['uid'];
                $author=$author_post[0]['username'];
            }
            if(empty($fid)){
                $forum=$this->get_forum();
                $fid=$forum['fid'];
            }
            if(empty($subject) || empty($message)){
                $getsubject=$this->getsubject($gettype);
                $subject=$getsubject['subject'];
                $message=$getsubject['message'];
            }
        }
        $time=time();
        $sql="INSERT INTO `pre_forum_thread` (
	`tid`,
	`fid`,
	`posttableid`,
	`typeid`,
	`sortid`,
	`readperm`,
	`price`,
	`author`,
	`authorid`,
	`subject`,
	`dateline`,
	`lastpost`,
	`lastposter`,
	`views`,
	`replies`,
	`displayorder`,
	`highlight`,
	`digest`,
	`rate`,
	`special`,
	`attachment`,
	`moderated`,
	`closed`,
	`stickreply`,
	`recommends`,
	`recommend_add`,
	`recommend_sub`,
	`heats`,
	`status`,
	`isgroup`,
	`favtimes`,
	`sharetimes`,
	`stamp`,
	`icon`,
	`pushedaid`,
	`cover`,
	`replycredit`,
	`relatebytag`,
	`maxposition`,
	`bgcolor`,
	`comments`,
	`hidden`
)
VALUES
	(
		'$tid',
		'$fid',
		'0',
		'0',
		'0',
		'0',
		'0',
		'$author',
		'$authorid',
		'$subject',
		'$time',
		'$time',
		'$author',
		'1',
		'0',
		'0',
		'0',
		'0',
		'0',
		'0',
		'0',
		'0',
		'0',
		'0',
		'0',
		'0',
		'0',
		'0',
		'0',
		'0',
		'0',
		'0',
		'-1',
		'-1',
		'0',
		'0',
		'0',
		'0',
		'0',
		'',
		'0',
		'0'
	);
 ";
         $re= M()->query($sql);
         $this->addpost($fid,$tid,$authorid,$author,$subject,$message,$type=true);
         if(is_array($re)){
            return array(
                'author'=>$author,
                 'fid'=>$fid,
                 'tid'=> $tid,
                 'subject'=>$subject,
                 'message'=>$message
             );
         }else{
             return 0;
         }
    }
    /**
     * 新增回复内容
     * @param string $fid
     * @param string $tid
     * @param string $authorid
     * @param string $author
     * @param string $subject
     * @param string $message
     * @param string $type
     */
    private function addpost($fid='',$tid='',$authorid='',$author='',$subject='',$message='',$type=false,$author_post_id=false,$gettype=1){
        $pid=$this->_record->field('max(pid)')->find();
        $pid=$pid['max(pid)']+1;
        $randport=$this->randport();
        if($type==false){
            $rand_user= $this->rand_user();
            $forum=$this->get_forum();
            $fid=$forum['fid'];
            $authorid=$rand_user['uid'];
            $author=$rand_user['username'];
            $tid=$this->get_thread($fid);
            $tid=$tid['tid'];
            $getsubject=$this->getsubject($gettype);
            $subject=$getsubject['subject'];
            $message=$getsubject['message'];
            $first=0;
        }else{
            $first=1;
        }
        if($author_post_id!=false){
            $author_post=$this->users($author_post_id);
            $authorid=$author_post[0]['uid'];
            $author=$author_post[0]['username'];
            if(empty($fid)){
                $forum=$this->get_forum();
                $fid=$forum['fid'];
            }
            if(!empty($fid)&&empty($tid)){
                $tid=$this->get_thread($fid);
                $tid=$tid['tid'];
            }
         
            if(empty($subject) || empty($message)){
                $getsubject=$this->getsubject($gettype);
                $subject=$getsubject['subject'];
                $message=$getsubject['message'];
            }
        }
         $position=$this->_record->where(array('tid'=>$tid))->field('max(position)')->find();
         $position=$position['max(position)']+1;
         
         $this->_thread->where(array('tid'=>$tid))->save(array('replies'=>$position));
         
         $this->_thread->where(array('tid'=>$tid))->setInc('views',3);
         
         $time=time();
         $sql="INSERT INTO `pre_forum_post` (
    	`pid`,
    	`fid`,
    	`tid`,
    	`first`,
    	`author`,
    	`authorid`,
    	`subject`,
    	`dateline`,
    	`message`,
    	`useip`,
    	`port`,
    	`invisible`,
    	`anonymous`,
    	`usesig`,
    	`htmlon`,
    	`bbcodeoff`,
    	`smileyoff`,
    	`parseurloff`,
    	`attachment`,
    	`rate`,
    	`ratetimes`,
    	`status`,
    	`tags`,
    	`comment`,
    	`replycredit`,
    	`position`
    )
    VALUES
    	(
    		'$pid',
    		'$fid',
    		'$tid',
    		'$first',
    		'$author',
    		'$authorid',
    		'$subject',
    		'$time',
    		'$message',
    		'::1',
    		'$randport',
    		'0',
    		'0',
    		'1',
    		'0',
    		'-1',
    		'-1',
    		'0',
    		'0',
    		'0',
    		'0',
    		'0',
    		'',
    		'0',
    		'0',
    		'$position'
    	);
    ";
         $re= M()->query($sql);
         if(is_array($re)){
             return array(
                'author'=>$author,
                 'fid'=>$fid,
                 'tid'=> $tid,
                 'subject'=>$subject,
                 'message'=>$message
             );
         }else{
             return 0;
         }
    }
    /**
     * 更新昨日帖子数
     */
    public function upyepoint(){
       echo  $this->todaypoint(38);
    }
    /**
     * 更新今日帖子数
     */
    private function todaypoint($fid=''){
        $btime = date('Y-m-d'.'00:00:00',time());
        $btimestr = strtotime($btime);
        $etime = date('Y-m-d'.'23:59:59',time());
        $etimestr = strtotime($etime);
        if(!empty($fid)){
            $condition['fid'] = array('eq',$fid);
        }
        $condition['dateline'] = array(between,array($btimestr,$etimestr));
        $count=$this->_thread->where($condition)->count('tid');
        $data['todayposts']=$count;
        $this->_forum->where(array('fid'=>$fid))->save($data);
        return $count;
    }
    /**
     * 更新模块帖子数
     */
    private function upposts($fid=''){
        $condition['fid'] = array('eq',$fid);
        $count=$this->_record->where($condition)->count('pid');
        $data['posts']=$count;
        $this->_forum->where(array('fid'=>$fid))->save($data);
        return $count;
    }
    /**
     * 更新模块回复
     */
    private function upthreads($fid=''){
        $condition['fid'] = array('eq',$fid);
        $count=$this->_thread->where($condition)->count('tid');
        $data['threads']=$count;
        $this->_forum->where(array('fid'=>$fid))->save($data);
        return $count;
    }
    /**
     * 
     * @param string $fid
     * @return unknown
     */
    private function upforum(){
        $thread=$this->get_forum(true);
        foreach($thread as $row){
           $fid=$row['fid'];
           $this->todaypoint($fid);
           $this->upposts($fid);
           $this->upthreads($fid);
        }
    }
    /**
     * 随机数
     * @return number
     */
    private function randport(){
        for($i=0;$i<5;$i++){
            @$port.= mt_rand(0,9);
        }
        return $port;
    }
    public function upreplises($tid=''){
//         if(){
            
//         }
        $this->_record->where()->select();
        
    }
}