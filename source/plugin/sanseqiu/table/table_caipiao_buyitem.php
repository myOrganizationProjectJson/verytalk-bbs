<?php
if(!defined('IN_DISCUZ')){
	exit('Access Denied');
}

class table_caipiao_buyitem extends discuz_table
{
	public function __construct(){
		$this->_table	= 'caipiao_buyitem';
		$this->_pk		= 'bid';

		parent::__construct();
	}
	
	
	
	public function fetch_all_byissue($qishu,$nowPage=1,$limit=20){
			$nowPage= $nowPage-1;
			return DB::fetch_all('select * from %t where issue=%d order by %i desc limit '. ($nowPage*$limit).','.$limit, array($this->_table,$qishu,$this->_pk));
		
	}
	
		public function fetch_all($nowPage=1,$limit=20){
	 		$nowPage= $nowPage-1;
			return DB::fetch_all('select * from %t  order by %i desc  limit '. ($nowPage*$limit).','.$limit, array($this->_table,$this->_pk ));
 
	}
	
	
	
 
	public function delete_by_bid($bid){
		DB::query("DELETE FROM %t WHERE %i = %d ", array($this->_table,$this->_pk, $bid ));
	}
	
	public function getMultiSizeByQiShu($qishu){
		$query= DB::query('select sum(multiple)  from %t where issue=%d', array($this->_table,$qishu));
		$re=mysql_fetch_array($query);
		return $re[0];
	}
	
	public function inertCaiPiao($style,$caiStr,$multiple,$issue,$uid,$uname){
	 
 	$arr = array( 'bid' => null, 'uid' => $uid, 'uname' => $uname, 'date' => time(),

                                'issue' => $issue, 'multiple' => $multiple, 'buystr' => $caiStr, );

		return $id = DB::insert($this->_table, $arr, 1);
	}
	
	
	public function count_by_search($condition) {
		return DB::result_first("SELECT COUNT(*) FROM %t WHERE 1 %i", array($this->_table, $condition));
	}

	public function fetch_all_by_search($condition, $start, $ppp) {
		return DB::fetch_all("SELECT * FROM %t WHERE 1 %i ORDER BY uid LIMIT %d, %d", array($this->_table, $condition, $start, $ppp));
	}
}