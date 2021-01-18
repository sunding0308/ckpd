<?php
namespace Home\Controller;
use Think\Controller;
use Home\Service\SqlsrvService;

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

    public function locationNavi(){
        $this->display('location_navi');
    }

    public function locationForm(){
        $ck = I('ck');
        $zt = I('zt');
        $cks = C('cks');
        
        $this->assign('ck',$ck);
        $this->assign('zt',$zt);
        $this->assign('cks',$cks);
        $this->display('location_form');
    }

    public function locationIndex(){
        $ck = I('ck');
        $cks = C('cks');
        $this->assign('ck',$ck);
        $this->assign('cks',$cks);
        $this->display('location_index');
    }

    public function GetLocationList(){
        $queue = M("Queue");
        $ck = I('ck');
        $today = date("Y-m-d 00:00:00");
        $queue = $queue->where('queued_at >= "'.$today.'"')->where('ck = '.$ck)->order('queued_at asc')->select();
        
        $this->ajaxReturn([
            'Result' => "1",
            'Data' => [
                'queue' => $queue
            ],
        ]);
    }

    public function notify(){
        $CarNoUid = M("CarNoUid");
        $queue = M("Queue");
        $id = I('id');
        $queue = $queue->where('id = '.$id)->find();
        $res = $CarNoUid->where('car_no="'.$queue['car_no'].'"')->find();
        if($res){
            $content = urlencode("您好！ 【".$queue['car_no']."】 车主，您现在可以进厂，请尽快前往目标仓库装车。");
            $url="http://wxpusher.zjiecode.com/api/send/message/?appToken=AT_riDOZCE8NwYJqnytEgsdi2b0QXG6UIIg&uid=".$res['uid']."&content=".$content;
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

    public function bindUidForm(){
        $uid = $_REQUEST['uid'];
        $this->assign('uid',$uid);

        $this->display('bind_uid_form');
    }

    public function bindUid(){
        $uid = I('uid');
        $carNo = strtoupper(I('carNo'));
        $carNo = str_replace('O', '0', $carNo);
        $carNo = str_replace('I', '1', $carNo);

        $CarNoUid = M("CarNoUid");
        $res = $CarNoUid->where('uid="'.$uid.'"')->find();
        if($res){
            $CarNoUid->where('uid="'.$uid.'"')->setField('car_no',$carNo);

            $this->ajaxReturn([
                'Result' => "1",
                'Data' => [
                    'Message' => '更改绑定车牌成功！'
                ],
            ]);
        }else{
            $data['uid'] = $uid;
            $data['car_no'] = $carNo;
            $CarNoUid->add($data);

            $this->ajaxReturn([
                'Result' => "1",
                'Data' => [
                    'Message' => '绑定车牌成功！'
                ],
            ]);
        }
    }

    public function submit(){
        $carNo = strtoupper(I('carNo'));
        $phoneNo = I('phoneNo');
        $transportCompany = I('transportCompany');
        $clientName = I('clientName');
        $goodsWeight = I('goodsWeight');
        $type = I('type');
        $carStates = I('carStates');
        $ck = I('ck');
        $zt = I('zt') == 'true' ? 1 : 0;
        $carNo = str_replace('O', '0', $carNo);
        $carNo = str_replace('I', '1', $carNo);
        $goodsType = implode(',', $type);

        if($ck != '06') {
            $reserveInfo = postUrl('http://192.168.60.160:8080/jtvms/restzvms043/getReserveInfo.do',json_encode([
                "RESERVATION_NO"=>'',
                "WECHATID"=>'',
                "CAR_LICENSE"=>$carNo
            ]));
            if($reserveInfo->code != '90001'){
                $this->ajaxReturn([
                    'Result' => "0",
                    'Data' => [
                        'Message' => $reserveInfo->message
                    ]
                ]);
            }
            if($reserveInfo->code == '90001' && $reserveInfo->data->BUSINESS_STATUS != '30' && $reserveInfo->data->BUSINESS_STATUS != '40' && $reserveInfo->data->BUSINESS_STATUS != '50'){
                $this->ajaxReturn([
                    'Result' => "0",
                    'Data' => [
                        'Message' => '您的预约状态不正确'
                    ]
                ]);
            }
        }
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
            $queue->transport_company = $transportCompany;
            $queue->client_name = $clientName;
            $queue->goods_weight = $goodsWeight;
            $queue->goods_type = $goodsType;
            $queue->car_states = $carStates;
            $queue->ck = $ck;
            $queue->zt = $zt;
            $queue->queued_at = date("Y-m-d H:i:s");
            $queue->states = 0;
            $queue->add();

            $wait = M("Wait");
            $wait->where('car_no='."'$carNo'")->delete();

            if($ck != '06') {
                $this->updateReserveStatus($carNo,40);
            }

            $this->ajaxReturn([
                'Result' => "1",
                'Data' => [
                    'Message' => '提交成功！'
                ],
            ]);
        }
    }

    public function submit4Vms(){
        $post = json_decode(file_get_contents('php://input'), true);
        $carNo = substr(strtoupper($post['carNo']),-5);
        $phoneNo = $post['phoneNo'];
        $transportCompany = $post['transportCompany'];
        $clientName = $post['clientName'];
        $goodsWeight = $post['goodsWeight'];
        $goodsType = $post['type'];
        $ck = $post['ck'];
        switch ($ck) {
            case '智能仓库（铜管、铜带、铜板）':
                $ck = '01';
                break;
            case '铜棒仓库':
                $ck = '02';
                break;
            case '电材仓库':
                $ck = '03';
                break;
            case '棒线仓库':
                $ck = '04';
                break;
            case '铜排仓库':
                $ck = '05';
                break;
            default:
                break;
        }
        $zt = $post['zt'] == 'true' ? 1 : 0;
        $carNo = str_replace('O', '0', $carNo);
        $carNo = str_replace('I', '1', $carNo);
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
            $queue->transport_company = $transportCompany;
            $queue->client_name = $clientName;
            $queue->goods_weight = $goodsWeight;
            $queue->goods_type = $goodsType;
            $queue->car_states = '空车';
            $queue->ck = $ck;
            $queue->zt = $zt;
            $queue->queued_at = date("Y-m-d H:i:s");
            $queue->states = 0;
            $queue->add();

            $wait = M("Wait");
            $wait->where('car_no='."'$carNo'")->delete();

            $this->ajaxReturn([
                'Result' => "1",
                'Data' => [
                    'Message' => '提交成功！'
                ],
            ]);
        }
    }

    public function submit4Scan(){
        header('Access-Control-Allow-Origin:*');
        $post = json_decode(file_get_contents('php://input'), true);
        $carNo = substr(strtoupper($post['carNo']),-5);
        $phoneNo = $post['phoneNo'];
        $transportCompany = $post['transportCompany'];
        $clientName = $post['clientName'];
        $goodsWeight = $post['goodsWeight'];
        $goodsType = $post['type'];
        $ck = $post['ck'];
        switch ($ck) {
            case '智能仓库（铜管、铜带、铜板）':
                $ck = '01';
                break;
            case '铜棒仓库':
                $ck = '02';
                break;
            case '电材仓库':
                $ck = '03';
                break;
            case '棒线仓库':
                $ck = '04';
                break;
            case '铜排仓库':
                $ck = '05';
                break;
            default:
                break;
        }
        $zt = $post['zt'] == 'true' ? 1 : 0;
        $carNo = str_replace('O', '0', $carNo);
        $carNo = str_replace('I', '1', $carNo);

        $reserveInfo = postUrl('http://192.168.60.160:8080/jtvms/restzvms043/getReserveInfo.do',json_encode([
            "RESERVATION_NO"=>'',
            "WECHATID"=>'',
            "CAR_LICENSE"=>$carNo
        ]));
        if($reserveInfo->code != '90001'){
            $this->ajaxReturn([
                'Result' => "0",
                'Data' => [
                    'Message' => $reserveInfo->message
                ]
            ]);
        }
        if($reserveInfo->code == '90001' && $reserveInfo->data->BUSINESS_STATUS != '30' && $reserveInfo->data->BUSINESS_STATUS != '40' && $reserveInfo->data->BUSINESS_STATUS != '50'){
            $this->ajaxReturn([
                'Result' => "0",
                'Data' => [
                    'Message' => '您的预约状态不正确'
                ]
            ]);
        }
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
            $res = $this->checkCar4Scan($carNo);
            if (!$res['status']) {
                $this->ajaxReturn([
                    'Result' => "0",
                    'Data' => [
                        'Message' => $res['msg']
                    ],
                ]);
            }
            $queue->car_no = $carNo;
            $queue->phone_no = $phoneNo;
            $queue->transport_company = $transportCompany;
            $queue->client_name = $clientName;
            $queue->goods_weight = $goodsWeight;
            $queue->goods_type = $goodsType;
            $queue->car_states = '空车';
            $queue->ck = $ck;
            $queue->zt = $zt;
            $queue->queued_at = date("Y-m-d H:i:s");
            $queue->states = 0;
            $queue->add();

            $this->updateReserveStatus($carNo,40);

            $this->ajaxReturn([
                'Result' => "1",
                'Data' => [
                    'Message' => C('cks')[$data['ck']].'仓库排队成功！'
                ],
            ]);
        }
    }

    public function changeStates4Oa(){
        $post = json_decode(file_get_contents('php://input'), true);
        $carNo = $post['carNo'];
        $queue = M("Queue");
        $data = $queue->where('car_no="'.$carNo.'"')->find();
        if ($data) {
            $queue->states = 2;
            $queue->transmited_at = date("Y-m-d H:i:s");
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

    public function ChangeStates(){
        $states = I('states');
        $id = I('id');
        $queue = M("Queue");
        $data = $queue->find($id);
        if ($data) {
            $queue->states = $states;
            if ($states == 1) {
                $CarNoUid = M("CarNoUid");
                $res = $CarNoUid->where('car_no="'.$data['car_no'].'"')->find();
                $queue->loaded_at = date("Y-m-d H:i:s");
                if($res){
                    $content = urlencode("您好！ 【".$data['car_no']."】 车主，您的货物即将准备装车，请尽快将车辆开往发货台。 — ".C('cks')[$data['ck']]."仓库管理中心");
                    $url="http://wxpusher.zjiecode.com/api/send/message/?appToken=AT_riDOZCE8NwYJqnytEgsdi2b0QXG6UIIg&uid=".$res['uid']."&content=".$content;
                    getUrl($url);
                }
            } else {
                $queue->transmited_at = date("Y-m-d H:i:s");
                $reserveInfo = postUrl('http://192.168.60.160:8080/jtvms/restzvms043/getReserveInfo.do',json_encode([
                    "RESERVATION_NO"=>'',
                    "WECHATID"=>'',
                    "CAR_LICENSE"=>$data['car_no']
                ]));
                if($reserveInfo->code == '90001' && $reserveInfo->data->BUSINESS_STATUS == '40'){
                    $this->updateReserveStatus($data['car_no'],50);
                }
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

    public function checkCar4Scan($carNo){
        $carNo = str_replace('O', '0', $carNo);
        $carNo = str_replace('I', '1', $carNo);
        $re = SqlsrvService::checkCarNo($carNo);
        if ($re['status']) {
            return [
                'status' => "1"
            ];
        } else {
            return [
                'status' => "0",
                'msg' => $re['msg']
            ];
        }
    }

    public function qrcode(){
        Vendor('phpqrcode.phpqrcode');
        $ck = I('ck');
        //生成二维码图片
        $object = new \QRcode();
        if($ck == '06'){
            $url=C('domain').'/home/queue/form?timestamp='.time().'&ck='.$ck.'&zt=true';
        }else{
            $url=C('domain').'/home/queue/form?timestamp='.time().'&ck='.$ck.'&zt=false';//网址或者是文本内容
        }
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
        $ck6Queue = $queue->where('ck = "06" and states = 0')->count();
        $ck6Load = $queue->where('ck = "06" and states = 1')->count();
        $wait = M('Wait');
        $waitCk1 = $wait->where('ck = "01"')->count();
        $waitCk2 = $wait->where('ck = "02"')->count();
        $waitCk3 = $wait->where('ck = "03"')->count();
        $waitCk4 = $wait->where('ck = "04"')->count();
        $waitCk5 = $wait->where('ck = "05"')->count();
        $waitCk6 = $wait->where('ck = "06"')->count();

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
                'ck5Load' => $ck5Load,
                'ck6Queue' => $ck6Queue,
                'ck6Load' => $ck6Load,
                'waitCk1' => $waitCk1,
                'waitCk2' => $waitCk2,
                'waitCk3' => $waitCk3,
                'waitCk4' => $waitCk4,
                'waitCk5' => $waitCk5
            ],
        ]);
    }

    public function getWaitInfo(){
        $carNo = I('car_no');
        $wait = M("Wait");
        $res = $wait->where('car_no='."'$carNo'")->find();

        if ($res) {
            $this->ajaxReturn([
                'Result' => "1",
                'Data' => [
                    'phoneNo' => $res['phone_no'],
                    'transportCompany' => $res['transport_company']
                ],
            ]);
        } else {
            $this->ajaxReturn([
                'Result' => "0",
                'Data' => [
                    'Message' => '获取等待区排队信息失败！'
                ],
            ]);
        }
    }

    public function download(){
        $in=  fopen('http://wxpusher.zjiecode.com/api/qrcode/mWPNEfMxu8W4phi0ctXvkKPWjVqWjtGwZNQDb2z2upPWgyJvvoLIQV3OiakVaTZB.jpg', "rb");
        $out=  fopen('Public/img/showqrcode.jpg', "wb");
        while ($chunk = fread($in,8192))
        {
            fwrite($out, $chunk, 8192);
        }
        fclose($in);
        fclose($out);

        echo '下载成功';
    }

    public function updateReserveStatus($carNo='BTEST',$status=40){
        $data = posturl('http://192.168.60.160:8080/jtvms/restzvms043/getReserveInfo.do',json_encode([
            "RESERVATION_NO"=> "",
            "WECHATID"=> "",
            "CAR_LICENSE"=> $carNo
        ]));
        if($data->code == '90001'){
            $reservationNo = $data->data->RESERVATION_NO;
            $data = posturl('http://192.168.60.160:8080/jtvms/restzvms043/updateReserveStatus.do',json_encode([
                "RESERVATION_NO"=> $reservationNo,
                "BUSINESS_STATUS"=> $status
            ]));
        }else{
            echo $data->message;
        }
    }
}