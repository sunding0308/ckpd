<?php
namespace Home\Controller;
use Think\Controller;

class WaitController extends Controller {

    public function index(){
        $this->display();
    }

    public function navigation(){
        $this->display();
    }

    public function GetWaitList(){
        $wait = M("Wait");
        $wait = $wait->select();
        
        $this->ajaxReturn([
            'Result' => "1",
            'Data' => [
                'wait' => $wait
            ],
        ]);
    }

    public function form(){
        $this->display();
    }

    public function submit(){
        $phoneNo = I('phoneNo');
        $carNo = strtoupper(I('carNo'));
        $transportCompany = I('transportCompany');
        $ck = I('ck');
        $carNo = str_replace('O', '0', $carNo);
        $carNo = str_replace('I', '1', $carNo);
        $wait = M("Wait");
        $data = $wait->where('car_no='."'$carNo'")->find();
        
        if ($data) {
            $this->ajaxReturn([
                'Result' => "0",
                'Data' => [
                    'Message' => $data['car_no'].' 已在等待区排队！'
                ],
            ]);
        } else {
            $driver = M("Driver");
            $res = $driver->where('phone_no='."'$phoneNo'")->find();
            if (!$res) {
                $driver->car_no = $carNo;
                $driver->phone_no = $phoneNo;
                $driver->transport_company = $transportCompany;
                $driver->created_at = date("Y-m-d H:i:s");
                $driver->updated_at = date("Y-m-d H:i:s");
                $driver->add();
            } else {
                $driver->car_no = $carNo;
                $driver->transport_company = $transportCompany;
                $driver->updated_at = date("Y-m-d H:i:s");
                $driver->save();
            }
            $wait = M("Wait");
            $wait->car_no = $carNo;
            $wait->phone_no = $phoneNo;
            $wait->transport_company = $transportCompany;
            $wait->ck = $ck;
            $wait->waited_at = date("Y-m-d H:i:s");
            $wait->add();

            $this->ajaxReturn([
                'Result' => "1",
                'Data' => [
                    'Message' => '提交成功！'
                ],
            ]);
        }
    }

    public function getDriverInfo(){
        $phoneNo = I('phone_no');
        $driver = M("Driver");
        $res = $driver->where('phone_no='."'$phoneNo'")->find();

        if ($res) {
            $this->ajaxReturn([
                'Result' => "1",
                'Data' => [
                    'carNo' => $res['car_no'],
                    'transportCompany' => $res['transport_company']
                ],
            ]);
        } else {
            $this->ajaxReturn([
                'Result' => "0",
                'Data' => [
                    'Message' => '获取司机信息失败！'
                ],
            ]);
        }
    }

    public function delete(){
        $id = I('id');
        $queue = M("Queue");
        $data = $queue->delete($id);
        if ($data) {
            $this->ajaxReturn([
                'Result' => "1"
            ]);
        } else {
            $this->ajaxReturn([
                'Result' => "0"
            ]);
        }
    }
}