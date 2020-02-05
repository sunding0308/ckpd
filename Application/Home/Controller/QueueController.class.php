<?php
namespace Home\Controller;
use Think\Controller;
use Home\Service\SqlsrvService;
class QueueController extends Controller {

    public function GetQueueList(){
        $queue = M("Queue");
        $queues = $queue->where('states <> 2')->order('states desc, priority desc, expedited_at asc, queued_at asc')->select();

        $this->ajaxReturn([
            'Result' => "1",
            'Data' => [
                'queues' => $queues
            ],
        ]);
    }

    public function list(){ 
        $queue = M('Queue'); // 实例化Queue对象
        $states = I('states');
        $carNo = I('car_no');
        if ($states == 2) {
            $sql = 'states = 2';
            if ($carNo) {
                $sql = 'car_no = "'.$carNo.'"';
            }
            // 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
            $queues = $queue->where($sql)->order('transmited_at desc')->page($_GET['p'].',10')->select();
        } else {
            $sql = 'states <> 2';
            if ($carNo) {
                $sql = 'car_no = "'.$carNo.'"';
            }
            // 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
            $queues = $queue->where($sql)->order('states desc, priority desc, expedited_at asc, queued_at asc')->page($_GET['p'].',10')->select();
        }
        $this->assign('states',$states);
        $this->assign('queues', $queues);// 赋值数据集
        $count      = $queue->where($sql)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }

    public function form(){
        $timestamp = I('timestamp');
        
        $timediff = time()-$timestamp;   //时间差的秒数
        $mins = intval($timediff/60);

        if ($mins > 12) {
            print('二维码已失效');
            return;
        }
        $this->display('form');
    }

    public function submit(){
        $carNo = strtoupper(I('carNo'));
        $phoneNo = I('phoneNo');
        $clientName = I('clientName');
        $goodsType = I('goodsType');
        $carStates = I('carStates');
        $queue = M("Queue");
        $data = $queue->where('car_no='."'$carNo'".' AND states <> 2')->find();
        
        if ($data) {
            $this->ajaxReturn([
                'Result' => "0",
                'Data' => [
                    'Message' => $data['car_no'].' 已在排队中！'
                ],
            ]);
        } else {
            $queue->car_no = $carNo;
            $queue->phone_no = $phoneNo;
            $queue->client_name = $clientName;
            $queue->goods_type = $goodsType;
            $queue->car_states = $carStates;
            $queue->queued_at = date("Y-m-d H:i:s");
            $queue->states = 0;
            $queue->add();

            $this->ajaxReturn([
                'Result' => "1",
                'Data' => [
                    'Message' => '提交成功！'
                ],
            ]);
        }
    }

    public function ChangeStates(){
        $states = I('states');
        $id = I('id');
        $queue = M("Queue");
        $data = $queue->find($id);
        if ($data) {
            $queue->states = $states;
            if ($states == 1) {
                $queue->loaded_at = date("Y-m-d H:i:s");
            } else {
                $queue->transmited_at = date("Y-m-d H:i:s");
            }
            $queue->save();
    
            $this->ajaxReturn([
                'Result' => "1"
            ]);
        } else {
            $this->ajaxReturn([
                'Result' => "0"
            ]);
        }
    }

    public function priority(){
        $id = I('id');
        $queue = M("Queue");
        $data = $queue->find($id);
        if ($data) {
            $queue->priority = 1;
            $queue->expedited_at = date("Y-m-d H:i:s");
            $queue->save();
    
            $this->ajaxReturn([
                'Result' => "1"
            ]);
        } else {
            $this->ajaxReturn([
                'Result' => "0"
            ]);
        }

    }

    public function checkCar(){
        $carNo = I('carNo');
        $re = SqlsrvService::checkCarNo($carNo);
        if ($re) {
            $this->ajaxReturn([
                'Result' => "1"
            ]);
        } else {
            $this->ajaxReturn([
                'Result' => "0",
                'Data' => [
                    'Message' => '车辆未过磅无法排队!'
                ],
            ]);
        }
    }

    public function qrcode(){
        Vendor('phpqrcode.phpqrcode');
        //生成二维码图片
        $object = new \QRcode();
        $url=C('domain').'/home/queue/form?timestamp='.time();//网址或者是文本内容
        $level=3;
        $size=8;
        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
    }
}