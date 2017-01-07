<?php
class newdb{
   
    private  $dbhost;
    private  $dbuser;
    private  $dbpw;
    private  $dbname;
    private  $tablepre;
    private  $r;
    public  $sql;
    /**
     * 构造函数 初始化配置项
     */
    public function __construct(){
        require './config/config_global.php';
        $configs=$_config['db']['1'];
        $dbhost=$configs['dbhost'];
        $dbuser=$configs['dbuser'];
        $dbpw=$configs['dbpw'];
        $dbname=$configs['dbname'];
        $tablepre=$configs['tablepre'];
        $this->dbhost=$dbhost;
        $this->dbuser=$dbuser;
        $this->dbpw=$dbpw;
        $this->dbname=$dbname;
        $this->tablepre=$tablepre;
     }
    /**
     * 连接数据库
     */
    public function connects(){
        	$con = mysql_connect("$this->dbhost","$this->dbuser","$this->dbpw");
        	if (!$con)
        	  {
        	     die('Could not connect: ' . mysql_error());
        	  }
        	if(!mysql_select_db("$this->dbname", $con)){
        	   echo "连接失败";
        	}
            	mysql_query('set names utf8');
    }
    /**
     * 查询
     * @param unknown $sql
     * @return multitype:
     */
    public function selects($name,$where='',$file=''){
        $this->connects();
        if($file==''){
            $file='*';
        }
        if(!empty($file)){
            $sql="select $file from {$this->tablepre}{$name}";
            if(!empty($where)){
             $sql.=" where $where";
            }
        }
        if($this->sql==true){
            echo $sql;
            echo '<br/>';
        }
        $result=mysql_query($sql);
        $i=0;
        while($row = mysql_fetch_array($result)){
            for($z=0;$z<count($row)/2+1;$z++){
                unset($row[$z]);
            }
            $rows[$i]=$row;
            $i++;
        }
        return $rows;
    }
    /**
     * 查询
     * @param unknown $sql
     * @return multitype:
     */
    public function inserts($name,$files='',$data=''){
        $this->connects();
        if(!empty($name)){
            $sql="INSERT INTO {$this->tablepre}$name ($files) VALUES ($data)";
        }
        if($this->sql==true){
            echo $sql;
            echo '<br/>';
        }
        $result=mysql_query($sql);
        return $result;
    }
    /**
     * 查询
     * @param unknown $sql
     * @return multitype:
     */
    public function querys($sql=''){
        $this->connects();
        if(empty($sql)){
            $sql="SELECT COUNT(*) TABLES, table_schema FROM information_schema.TABLES   WHERE table_schema = '$this->dbname' GROUP BY table_schema";
        }
        if($this->sql==true){
            echo $sql;
            echo '<br/>';
        }
        $result=mysql_query($sql);
        $row = mysql_fetch_array($result);
        return $row;
    }
    /**
     * 查询
     * @param unknown $sql
     * @return multitype:
     */
    public function deletes($name,$where=''){
        $this->connects();
        if(empty($where)){
            echo "delete all ???";
            return false;
        }
        
        if(!empty($name)){
            $sql= "DELETE FROM {$this->tablepre}$name WHERE $where ";
           
        }
        if($this->sql==true){
            echo $sql;
            echo '<br/>';
        }
        $result=mysql_query($sql);
        return $result;
    }
    /**
     * 关闭连接
     */
    function __destruct(){
        mysql_close();
    }
  }	
