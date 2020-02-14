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
            $sql = "select top 1 净重,皮重 from dbo.称重信息 where 车号 = '".$carNo."' and 更新时间 > convert(varchar(10),getdate(),120) order by 一次过磅时间 desc";
            $result = $re->conn->query($sql)->fetch();
            //未过磅不能排队
            if ($result) {
                //皮重和净重都有值，不能排队
                if ($result['皮重'] != '.000' && $result['净重'] != '.000') {
                    return [
                        'status' => false,
                        'msg' => '车辆已过磅无法排队!'
                    ];
                } else {
                    return [
                        'status' => true
                    ];
                }
            } else {
                return [
                    'status' => false,
                    'msg' => '车辆未过磅无法排队!'
                ];
            }
        }
    }
}