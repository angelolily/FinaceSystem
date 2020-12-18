<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

//关联数组删除key

function bykey_reitem($arr, $key){
    if(!array_key_exists($key, $arr)){
        return $arr;
    }
    $keys = array_keys($arr);
    $index = array_search($key, $keys);
    if($index !== FALSE){
        array_splice($arr, $index, 1);
    }
    return $arr;

}

function batch_import_excel($file){
    $spreadsheet = PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    $sheetData = $spreadsheet->getActiveSheet()->toArray();
    return $sheetData;

}

/**
 * Notes:
 * User: Administrator
 * DateTime: 2020/12/7 12:17
 * @param int $fill_num
 * @return array
 */
function test_Data_Fill($fill_num=1)
{
    $faker = Faker\Factory::create('zh_CN');
    $data=[];
    $fill_data=[];
    for($i=0;$i<=$fill_num;$i++)
    {
        $data=[];
        $data['fdata_num']=time().rand(1111,9999);
        $data['fdata_create_time']=date('Y-m-d H:i:s');
        $data['fdata_jg_id']="FJJK";
        $data['fdata_emp']="15259191562";
        $data['fdata_cjrpotdate']=$faker->date($format="Y-m-d",$max = 'now');
        $data['fdata_repoid']=rand(1000,9999);
        $data['fdata_report_type']="住宅";
        $data['fdata_proj_name']=$faker->address();
        $data['fdata_entrust']=$faker->name();
        $data['fdata_invoice_money']=rand(10000,99999);
        $data['fdata_invoice_name']=$faker->company();
        $data['fdata_amount']=rand(100,600);
        $data['fdata_evaluation']=rand(100,600);
        $data['fdata_invoice_type']=2;
        $data['fdata_tax_num']=rand(100000,999999);
        $data['fdata_bank']=$faker->creditCardType;
        $data['fdata_bank_address']=$faker->address();
        $data['fdata_bank_phone']=$faker->phoneNumber();
        $data['fdata_statue']="发票申请中";


        array_push($fill_data,$data);


    }

    return $fill_data;



}
