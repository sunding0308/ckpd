<?php
namespace Home\Service;
use PDO;

class SqlsrvService {

    private $conn;
    private $dbms;
    private $host;
    private $user;
    private $password;
    private $dbname;
    private $dsn;

    public function __construct(){
        $this->dbms = 'sqlsrv'; 
        $this->host = '192.168.7.202';
        $this->user = 'esb';
        $this->password = 'esb2019';
        $this->dbname = 'WEIGHT70';
        $this->dsn = "$this->dbms:Server=$this->host;Database=$this->dbname";
        $conn = new PDO($this->dsn, $this->user, $this->password);
        $this->conn = $conn;
    }

    static public function checkCarNo($carNo){
        $re = new SqlsrvService();
        if (!$re->conn) {
            echo "连接地磅系统失败！";
            exit;
        }else {
            $sql = "select 车号,一次过磅时间,二次过磅时间 from dbo.称重信息 where 车号 = '".$carNo."' and 更新时间 > convert(varchar(10),getdate(),120)";
            $result = $re->conn->query($sql);
            if ($result->fetch()) {
                return true;
            } else {
                return false;
            }
        }
    }
}