<?php

if(!defined('IN_DISCUZ')){
	exit('Access Denied');
}

class table_caipiao_pub extends discuz_table
{
	public function __construct(){
		$this->_table	= 'caipiao_pub';
		$this->_pk		= 'pid';

		parent::__construct();
	}
	
	
	public function fetch_all_byissue($qishu,$nowPage=1,$limit=20){
			$nowPage= $nowPage-1;
			return DB::fetch_all('select * from %t where issue=%d ORDER BY %i DESC  LIMIT '. ($nowPage*$limit).','.$limit, array($this->_table,$qishu,$this->_pk));
		
	}
	
	public function fetch_all($nowPage=1,$limit=20){
	 		$nowPage= $nowPage-1;
			return DB::fetch_all('select * from %t  ORDER BY %i DESC  LIMIT '. ($nowPage*$limit).','.$limit, array($this->_table ,$this->_pk));
 
	}
		
	public function fetch_byid($id){
			return DB::fetch_first('SELECT * FROM %t WHERE %i = %d', array($this->_table, $this->_pk,$id));
		
	}
	
	public function fetch_byissue($issue){
			return DB::fetch_first('SELECT * FROM  %t  WHERE  %i = %d', array($this->_table, $this->_pk,$issue) );
	}

	
	public function decrease_byissue($issue,$num){
			 DB::query('UPDATE %t SET total_multiple=total_multiple-%d  WHERE issue = %d', array($this->_table,$num,$issue));
		
	}
	
 	public function delete_by_id($id){
		  DB::query("DELETE FROM %t WHERE %i = %d ", array($this->_table, $this->_pk,$id ));
	}
 
	public function inertCaiPiaoPub( $pubstr, $issue,$totalMul ){
	 
 		$arr = array( 'pid' => null, 'pubstr' => $pubstr, 'issue' => $issue, 'date' => time() ,'total_multiple'=> $totalMul    );

		return $id = DB::insert($this->_table, $arr, 1);
	}
	
	public function getMaxQiShu(){
		$arr =DB::fetch_first('SELECT MAX(issue) FROM  %t', array($this->_table));
    	 return $arr['MAX(issue)'];
	}
 
	
	public function getMultiSizeByQiShu($qishu){
		$query= DB::query('select sum(multiple)  from %t where issue=%d', array($this->_table,$qishu));
		$re=mysql_fetch_array($query);
		return $re[0];
	}
	
	public function count_by_search($condition) {
		return DB::result_first("SELECT COUNT(*) FROM  %t WHERE 1 %i ", array($this->_table, $condition));
	}
	
	 
	public function fetch_all_by_search($condition, $start, $ppp) {
		return DB::fetch_all("SELECT * FROM %t WHERE 1 %i ORDER BY uid LIMIT %d, %d ", array($this->_table, $condition, $start, $ppp));
	}
	 
}