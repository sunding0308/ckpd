<?php
namespace Home\Controller;
use Think\Controller;
use Home\Service\SqlsrvService;
// use Home\Service\SmsService;

class QueueController extends Controller {

    public function Index(){
        $ck = I('ck');
        $cks = C('cks');
        $this->assign('ck',$ck);
        $this->assign('cks',$cks);
        $this->display();
    }

    public function GetQueueList(){
        $queue = M("Queue");
        $ck = I('ck');
        $today = date("Y-m-d 00:00:00");
        $queues = $queue->where('states <> 2 and ck = '.$ck.' and queued_at >= "'.$today.'"')->order('states desc, priority desc, expedited_at asc, queued_at asc')->select();
        $map['queued_at'] = ['egt', $today];
        $map['ck'] = $ck;
        $todayTotal = $queue->where($map)->count();
        $finished = $queue->where($map)->where('states = 2')->count();
        
        $this->ajaxReturn([
            'Result' => "1",
            'Data' => [
                'queues' => $queues,
                'todayTotal' => $todayTotal,
                'finished' => $finished,
            ],
        ]);
    }

    public function list(){ 
        $queue = M('Queue'); // 实例化Queue对象
        $states = I('states');
        $carNo = I('car_no');
        $ck = I('ck');
        $cks = C('cks');
        $p = $_GET['p'] == NULL ? 1:$_GET['p'];
        if ($states == 2) {
            $sql = 'states = 2 and ck = '.$ck;
            if ($carNo) {
                $sql = 'car_no = "'.$carNo.'" and ck = '.$ck;
            }
            // 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
            $queues = $queue->where($sql)->order('transmited_at desc')->page($p.',10')->select();
        } else {
            $sql = 'states <> 2 and ck = '.$ck;
            if ($carNo) {
                $sql = 'car_no = "'.$carNo.'" and ck = '.$ck;
            }
            // 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
            $queues = $queue->where($sql)->order('states desc, priority desc, expedited_at asc, queued_at asc')->page($p.',10')->select();
        }
        $this->assign('states',$states);
        $this->assign('queues', $queues);// 赋值数据集
        $this->assign('ck', $ck);
        $this->assign('cks', $cks);
        $count      = $queue->where($sql)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->display(); // 输出模板
    }

    public function allList(){
        $queue = M('Queue');
        $states = I('states');
        $carNo = I('car_no');
        $p = $_GET['p'] == NULL ? 1:$_GET['p'];
        $today = date("Y-m-d 00:00:00");
        if ($states == 2) {
            $sql = 'states = 2 and queued_at >= "'.$today.'"';
            if ($carNo) {
                $sql = 'car_no = "'.$carNo.'" and queued_at >= "'.$today.'"';
            }
        } elseif($states == 01) {
            $sql = 'states <> 2 and queued_at >= "'.$today.'"';
            if ($carNo) {
                $sql = 'car_no = "'.$carNo.'" and queued_at >= "'.$today.'"';
            }
        } else {
            $sql = 'queued_at >= "'.$today.'"';
            if ($carNo) {
                $sql = 'car_no = "'.$carNo.'" and queued_at >= "'.$today.'"';
            }
        }
        $queues = $queue->where($sql)->order('states asc')->page($p.',10')->select();
        $todayTotal = $queue->where('queued_at >= "'.$today.'"')->count();
        $this->assign('queues', $queues);
        $count      = $queue->where($sql)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('todayTotal',$todayTotal);
        $this->display();
    }

    public function form(){
        $timestamp = I('timestamp');
        $ck = I('ck');
        $zt = I('zt');
        $cks = C('cks');
        
        $timediff = time()-$timestamp;   //时间差的秒数
        $mins = intval($timediff/60);

        if ($mins > 12) {
            print('二维码已失效');
            return;
        }
        $this->assign('ck',$ck);
        $this->assign('zt',$zt);
        $this->assign('cks',$cks);
        $this->display('form');
    }

    public function submit(){
        $carNo = strtoupper(I('carNo'));
        $phoneNo = I('phoneNo');
        $clientName = I('clientName');
        $goodsWeight = I('goodsWeight');
        $type = I('type');
        $carStates = I('carStates');
        $ck = I('ck');
        $zt = I('zt') == 'true' ? 1 : 0;
        $carNo = str_replace('O', '0', $carNo);
        $carNo = str_replace('I', '1', $carNo);
        $goodsType = implode(',', $type);
        $queue = M("Queue");
        $data = $queue->where('car_no='."'$carNo'".' AND states <> 2')->find();
        
        if ($data) {
            $this->ajaxReturn([
                'Result' => "0",
                'Data' => [
                    'Message' => $data['car_no'].' 已在'.C('cks')[$data['ck']].'仓库排队中！'
                ],
            ]);
        } else {
            $queue->car_no = $carNo;
            $queue->phone_no = $phoneNo;
            $queue->client_name = $clientName;
            $queue->goods_weight = $goodsWeight;
            $queue->goods_type = $goodsType;
            $queue->car_states = $carStates;
            $queue->ck = $ck;
            $queue->zt = $zt;
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
                //点击装车发送短信给司机
                // SmsService::sendYzxSms($data['phone_no'],$data['car_no'],C('cks')[$data['ck']]);
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

    public function retract(){
        $id = I('id');
        $queue = M("Queue");
        $data = $queue->find($id);
        if ($data) {
            $queue->states = 0;
            $queue->loaded_at = null;
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

    public function checkCar(){
        $carNo = strtoupper(I('carNo'));
        $carNo = str_replace('O', '0', $carNo);
        $carNo = str_replace('I', '1', $carNo);
        $re = SqlsrvService::checkCarNo($carNo);
        if ($re['status']) {
            $this->ajaxReturn([
                'Result' => "1"
            ]);
        } else {
            $this->ajaxReturn([
                'Result' => "0",
                'Data' => [
                    'Message' => $re['msg']
                ],
            ]);
        }
    }

    public function qrcode(){
        Vendor('phpqrcode.phpqrcode');
        $ck = I('ck');
        //生成二维码图片
        $object = new \QRcode();
        $url=C('domain').'/home/queue/form?timestamp='.time().'&ck='.$ck.'&zt=false';//网址或者是文本内容
        $level=3;
        $size=8;
        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
    }

    public function getQueueStatus(){
        $queue = M('Queue');
        $ck1Queue = $queue->where('ck = "01" and states = 0')->count();
        $ck1Load = $queue->where('ck = "01" and states = 1')->count();
        $ck2Queue = $queue->where('ck = "02" and states = 0')->count();
        $ck2Load = $queue->where('ck = "02" and states = 1')->count();
        $ck3Queue = $queue->where('ck = "03" and states = 0')->count();
        $ck3Load = $queue->where('ck = "03" and states = 1')->count();
        $ck4Queue = $queue->where('ck = "04" and states = 0')->count();
        $ck4Load = $queue->where('ck = "04" and states = 1')->count();
        $ck5Queue = $queue->where('ck = "05" and states = 0')->count();
        $ck5Load = $queue->where('ck = "05" and states = 1')->count();

        $this->ajaxReturn([
            'Result' => "1",
            'Data' => [
                'ck1Queue' => $ck1Queue,
                'ck1Load' => $ck1Load,
                'ck2Queue' => $ck2Queue,
                'ck2Load' => $ck2Load,
                'ck3Queue' => $ck3Queue,
                'ck3Load' => $ck3Load,
                'ck4Queue' => $ck4Queue,
                'ck4Load' => $ck4Load,
                'ck5Queue' => $ck5Queue,
                'ck5Load' => $ck5Load
            ],
        ]);
    }
}