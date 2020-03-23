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

    public function list(){
        $wait = M('Wait');
        $carNo = I('car_no');
        $ck = I('ck');
        $cks = C('cks');
        $p = $_GET['p'] == NULL ? 1:$_GET['p'];
        if ($carNo) {
            $sql = 'car_no = "'.$carNo.'"';
        }
        if ($ck) {
            $sql = 'ck = '.$ck;
        }
        if ($carNo && $ck) {
            $sql = 'car_no = "'.$carNo.'" and ck = '.$ck;
        }
        // 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
        if (isset($sql)) {
            $waits = $wait->where($sql)->order('waited_at desc')->page($p.',10')->select();
            $count = $wait->where($sql)->count();
        } else {
            $waits = $wait->order('waited_at desc')->page($p.',10')->select();
            $count = $wait->count();
        }
        $this->assign('waits', $waits);// 赋值数据集
        $this->assign('ck', $ck);
        $Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }

    public function notify(){
        $CarNoUid = M("CarNoUid");
        $car_no = I('car_no');
        $res = $CarNoUid->where('car_no="'.$car_no.'"')->find();
        if($res){
            $content = urlencode("您好！ 【".$car_no."】 车主，您现在可以进场，请尽快前往目标仓库排队。");
            $url="http://wxpusher.zjiecode.com/api/send/message/?appToken=AT_X3zrNKfXRW8ctWQXvRe36F4FlsEAZWWn&uid=".$res['uid']."&content=".$content;
            getUrl($url);
        } else {
            $this->ajaxReturn([
                'Result' => "0",
                'Data' => [
                    'Message' => '该车辆的用户未关注公众号并绑定车牌号，请电话通知。'
                ],
            ]);
        }
        
        $this->ajaxReturn([
            'Result' => "1",
            'Data' => [
                'Message' => '通知成功'
            ],
        ]);
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