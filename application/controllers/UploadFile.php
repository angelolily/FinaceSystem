<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UploadFile extends CI_Controller
{


    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Sys_Model');
        $this->load->model('Jko_Model');
        $this->load->helper('url');
        $this->load->helper('tool');

    }

    public function index()
    {


    }
    //计算应收评估费
    public function count_total_price_fc($h_Total)
    {
        $charge = 0;
        $h_Total =intval($h_Total) * 10000;
        if ($h_Total <= 1000000) {
            if ($h_Total * 0.004 < 2000) {//如果收费低于2000元/条 那么就按两千算
                $charge = 2000;
            } else {
                $charge = $h_Total * 0.004;//如果高于就按高的算
            }

        }
        if ($h_Total > 1000000 && $h_Total <= 10000000) {//高于100万，小于1000
            $charge = 4000 + ($h_Total - 1000000) * 0.002;

        }
        if ($h_Total > 10000000 && $h_Total <= 20000000) {//高于1000万小于2000万
            $charge = 22000 + ($h_Total - 10000000) * 0.0014;

        }
        if ($h_Total > 20000000 && $h_Total <= 50000000) {
            $charge = 36000 + ($h_Total - 20000000) * 0.0007;

        }
        if ($h_Total > 50000000 && $h_Total <= 80000000) {
            $charge = 57000 + ($h_Total - 50000000) * 0.0004;

        }
        if ($h_Total > 80000000 && $h_Total <= 100000000) {
            $charge = 69000 + ($h_Total - 80000000) * 0.0002;

        }
        if ($h_Total > 100000000) {
            $charge = 73000 + ($h_Total - 100000000) * 0.0001;

        }

        $result = array('charge' => $charge);
        return $result;

    }


    public function upload_file()
    {
        $echo_result=[];
        $rpoid=[];//记录要报告编号,用于同步更新到OA中
        $jgID = $this->session->userdata('c_jgID');
        //获取已完成的报告编号
        if($jgID=="FJJK")
        {
            $rpoid_sql = "select distinct c_rpoid,C_CJRPOTDATE,C_PROJNAME,C_ENTRUST,C_AMOUNT,c_projtype from jko_projinfotb where c_projstate='报告已完成' and C_PZZT is null";
        }
        else
        {
            $rpoid_sql = "select distinct c_rpoid,C_CJRPOTDATE,C_PROJNAME,C_ENTRUST,C_AMOUNT,c_projtype from jko_projinfotb where c_jgID='$jgID' and c_projstate='报告已完成' and C_PZZT is null";
        }

        $result_poid = $this->Jko_Model->execute_sql($rpoid_sql);
        $DbInsResult=array();//最终插入数据库结构
        $file = $_FILES['file']; // 获取上传的文件
        if ($file == null) {
            exit(json_encode(array('code' => 1, 'msg' => '未上传文件')));
        }
        // 获取文件后缀
        $temp = explode(".", $file["name"]);
        $extension = end($temp);
        $file_size = $file['size'];
        $file_name=date('Ymdhis').rand(111,999);//文件名去中文
        $file_path = "D:\\importExcel\\" .$file_name . "." . $extension;
        $file_tmp = $file['tmp_name'];
        $move_result = move_uploaded_file($file_tmp, $file_path);
        if ($move_result) {

            $Mismatch=array();//不匹配报错
            $Match=array();//录入的报告编号
            $excel_inData=batch_import_excel($file_path);
            $excel_num=count($excel_inData);//记录Excel中记录总数
            foreach ($excel_inData as $row)
            {
                if($row[0])
                {
                    array_push($Mismatch,$row[0]);//获取所有报告编号
                    foreach ($result_poid as $result_row)
                    {
                        if($row[0]==$result_row['c_rpoid'])
                        {
                            array_push($Match,$row[0]);
                            //按照excel列顺序赋值字段名称
                            $temp_row['fdata_repoid']=$row[0];
                            $temp_row['fdata_invoice_name']=$row[1];
                            $temp_row['fdata_invoice_type']=$row[2];
                            $temp_row['fdata_tax_num']=$row[3];
                            $temp_row['fdata_bank']=$row[4];
                            $temp_row['fdata_bank_address']=$row[5];
                            $temp_row['fdata_bank_phone']=$row[6];
                            $temp_row['fdata_invoice_emp']=$row[7];
                            $temp_row['fdata_total_money']=$row[8];
                            $temp_row['fdata_total_flag']=$row[9];//标识
                            $temp_row['fdata_emp']=$row[10];
                            $temp_row['fdata_bank_num']=$row[11];
                            $temp_row['fdata_invoice_money']=$row[12];
                            $temp_row['fdata_cjrpotdate']=$result_row["C_CJRPOTDATE"];
                            $temp_row['fdata_proj_name']=$result_row["C_PROJNAME"];
                            $temp_row['fdata_entrust']=$result_row["C_ENTRUST"];
                            $temp_row['fdata_amount']=$result_row["C_AMOUNT"];
                            $temp_row['fdata_report_type']=$result_row["c_projtype"];
                            $temp_row['fdata_jg_id']=$jgID;
                            $temp_row['fdata_statue']="发票申请中";
                            $temp_row['fdata_num']=date('Ymdhis').rand(111,999);
                            $temp_row['fdata_create_time']=date('Y-m-d h:i:s', time());
                            //获取应收评估费
                            $result_price=$this->count_total_price_fc($result_row["C_AMOUNT"]);
                            if(count($result_price)>0)
                            {
                                $temp_row['fdata_evaluation']=$result_price['charge'];
                            }
                            array_push($DbInsResult,$temp_row);
                            array_push($rpoid,array('c_rpoid'=>$temp_row['fdata_repoid'],'c_pzzt'=>1));
                        }
                    }
                }
            }
            $Mismatch=array_diff($Mismatch,$Match);
            unset($Mismatch[0]);

            $Mismatch=join("、",$Mismatch);

            $FinalResults=array();//最终插入数据库的值
            $tem_plz=array();//标签
            $tem_plz_bp=array();//改变后标识

            if(count($DbInsResult)>0){
                //统计合并信息
                foreach ($DbInsResult as $tem){
                    if($tem['fdata_total_flag']!=""){
                        if(count($tem_plz_bp)<=0){
                            $is=true;
                        }else{
                            $is=false;
                            $isinoi=in_array($tem['fdata_total_flag'],$tem_plz_bp);//如果合并标识已存在，说明已被标识过
                        }
                        if($is||!$isinoi){
                            $tem_plz=array();
                            array_push($tem_plz, $tem['fdata_total_flag']);
                            array_push($tem_plz_bp, $tem['fdata_total_flag']);
                            $tem['fdata_total_flag'] = time().rand(111,999);//自定义合并标识
                            foreach ($DbInsResult as $tem_sec){
                                if($tem_sec['fdata_total_flag']!="") {
                                    $isino=in_array($tem_sec['fdata_total_flag'],$tem_plz);
                                    if ($isino && $tem_sec['fdata_repoid'] != $tem['fdata_repoid']) {
                                        $tem_sec['fdata_total_flag'] = $tem['fdata_total_flag'];
                                        array_push($FinalResults, $tem_sec);
                                    }
                                }
                            }
                            array_push($tem_plz_bp, $tem['fdata_total_flag']);//将已使用的合并标识记录
                            array_push($FinalResults,$tem);
                        }

                    }
                    else{
                        array_push($FinalResults,$tem);
                    }

                }
                //插入财务发票信息表
                $result_rows=$this->Sys_Model->table_addRow('finance_data',$FinalResults,2);
                //同步将报告编号更新为已开票状态
                $result_rpoid=$this->Jko_Model->table_updateBatchRow("jko_projinfotb",$rpoid,"c_rpoid");

                if ($result_rows>0 && $result_rpoid>0){
                    $echo_result['code']=true;
                    if(count($Mismatch)>0){
                        $echo_result['msg']="成功插入数据：".$result_rows."条,"."失败的数据是：".$Mismatch;
                    } else{
                        $echo_result['msg']="成功插入数据：".$result_rows."条";
                    }
                }
                else{
                    $echo_result['code']=false;
                    $echo_result['msg']="插入数据失败";
                }

            } else {
                $echo_result['code']=false;
                $echo_result['msg']="全部插入失败,失败的原因可能是：报告编号已开票，请核实后修改";

            }

        }
        else{
            $echo_result['code']=false;
            $echo_result['msg']="文件获取失败";
        }


        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");

        echo json_encode($echo_result);



    }



}
