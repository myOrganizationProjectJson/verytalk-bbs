<?php
if(!defined('IN_DISCUZ')){
	exit('Access Denied');
}


class table_caipiao_bp extends discuz_table
{
	public function __construct(){
		$this->_table	= 'caipiao_bp';
		$this->_pk		= 'bpid';

		parent::__construct();
	}
	
	
	
	public function fetch_all($nowPage=1,$limit=20){
	 		$nowPage= $nowPage-1;
			return DB::fetch_all('select * from %t  ORDER BY %i DESC  LIMIT '. ($nowPage*$limit).','.$limit, array($this->_table ,$this->_pk));
 
	}
	
	
	public function delete_by_id($id){
		DB::query("DELETE FROM %t WHERE %i = %d ", array($this->_table, $this->_pk,$id ));
	}
	

	public function inertCaiPiaoBp( $pubstr, $issue,$pid, $ssqPluginVars ){
	
 
	
		$arrRe=DB::fetch_all('select * from %t where issue=%d order by bid desc', array('caipiao_buyitem', $issue));
		foreach($arrRe as $key => $value){
			 
			 $userStr=$value['buystr'];
		
			
             	$num=0; $i=0;
				$arr1=explode(',',$pubstr);$arr2=explode(',',$userStr);
 
				for( $i=0;$i<count($arr1);$i++){
					if($arr1[$i]==$arr2[$i]){		$num++;		}
				}
				$sameNum= $num; 
				if($sameNum==3){ 
							$arr = array( 'bpid' => null, 'pid' =>$pid,'bid' => $value['bid'], 'uid' => $value['uid'], 'uname' =>$value['uname'], 
						            'issue' =>$value['issue'], 'multiple' => $value['multiple'], 'userstr' =>$value['buystr'] ,'prize' =>1,'prizemoney' => $value['multiple']*$ssqPluginVars['prizeOne']);
								DB::insert($this->_table, $arr, 1);
			
					
				}elseif($sameNum==2){
				
					$arr = array( 'bpid' => null, 'pid' =>$pid,'bid' => $value['bid'], 'uid' => $value['uid'], 'uname' =>$value['uname'], 
						            'issue' =>$value['issue'], 'multiple' => $value['multiple'], 'userstr' =>$value['buystr'] ,'prize' =>2,'prizemoney' => $value['multiple']*$ssqPluginVars['prizeTwo'] );
							DB::insert($this->_table, $arr, 1);
				
				} elseif($sameNum==1){
					
					$arr = array( 'bpid' => null, 'pid' =>$pid,'bid' => $value['bid'], 'uid' => $value['uid'], 'uname' =>$value['uname'], 
						            'issue' =>$value['issue'], 'multiple' => $value['multiple'], 'userstr' =>$value['buystr'] ,'prize' =>3,'prizemoney' => $value['multiple']*$ssqPluginVars['prizeThree']);
						DB::insert($this->_table, $arr, 1);
						
				}  else{
					
				} 
		}
			 
			 		 
	 
	}
	
 
	public function fetch_all_byissue($qishu,$nowPage=1,$limit=20){
			$nowPage= $nowPage-1;
			return DB::fetch_all('select * from %t where issue=%d order by %i desc limit '. ($nowPage*$limit).','.$limit, array($this->_table,$qishu,$this->_pk));
		
	}
	
	public function count_by_search($condition) {
		return DB::result_first("SELECT COUNT(*) FROM %t WHERE 1 %i", array($this->_table, $condition));
	}
	
	
 

	 
}
	