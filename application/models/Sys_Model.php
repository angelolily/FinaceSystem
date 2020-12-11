<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/23
 * Time: 20:41
 */
class Sys_Model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database('default');
    }

    //获取发票申请记录数据,用于table填充数据
    public function get_All_invoiceData($pages,$rows,$wheredata,$likedata){

        $offset=($pages-1)*$rows;//计算偏移量
        $data = array();
        $this->db->select('SQL_CALC_FOUND_ROWS fdata_bank_num,fdata_num,fdata_id,fdata_total_flag,fdata_statue,fdata_total_money,
                           fdata_repoid,fdata_proj_name,fdata_invoice_name,fdata_invoice_money,
                           fdata_invoice_type,fdata_express_company,fdata_express_date,fdata_jg_id,fdata_emp,
                           fdata_express_num,fdata_cjrpotdate,fdata_report_type,fdata_entrust,
                           fdata_amount,fdata_evaluation,fdata_tax_num,fdata_bank,fdata_bank_address,fdata_bank_phone,
                           fdata_invoice_date,fdata_money_date,fdata_refund_date,fdata_rete,fdata_invoice_emp',FALSE);
        if(count($wheredata)>0){
            $this->db->where($wheredata);//判断需不需要查询
        }
        if(count($likedata)>0){
            $this->db->like($likedata);//判断需不需要查询
        }

        $this->db->limit($rows,$offset);
        $this->db->order_by('fdata_id','desc');

        $query = $this->db->get('finance_data');
        $ss=$this->db->last_query();
        $r_total=$this->db->query("select FOUND_ROWS() as total")->row();
        $row_arr=$query->result_array();
        $result['count']=$r_total->total;//获取总行数
        $result["data"] = $row_arr;

        return $result;

    }

    //财务获取发票申请记录数据,用于table填充数据
    public function get_All_invoice($pages,$rows,$wheredata,$likedata){

        $offset=($pages-1)*$rows;//计算偏移量
        $data = array();
        $field='SQL_CALC_FOUND_ROWS fdata_bank,fdata_bank_num,fdata_proj_name,fdata_tax_rate,fdata_tax_amount,fdata_invoice_num,fdata_num,fdata_id,fdata_total_flag,fdata_statue,fdata_total_money,
                           fdata_repoid,fdata_invoice_name,fdata_invoice_money,fdata_refund_verify,fdata_emp,fdata_jg_id,
                           fdata_invoice_type,fdata_report_type,fdata_alter_date,fdata_alter_money,
                           fdata_alter_rate,fdata_alter_amount,fdata_alter_invoice_num,fdata_rad_num,
                           fdata_amount,fdata_evaluation,fdata_tax_num,fdata_bank_address,fdata_bank_phone,
                           fdata_invoice_date,fdata_money_date,fdata_refund_date,fdata_rete,fdata_rad_num,fdata_rad_money,fdata_refund_reason';
        $sql_query="Select $field from finance_data a where not exists(select 1 from finance_data where fdata_total_flag=a.fdata_total_flag and fdata_id>a.fdata_id)";
        if($wheredata!=""){
            $sql_query=$sql_query." and ".$wheredata;
        }
        if($likedata!=""){
            $sql_query=$sql_query." ".$likedata;
        }
        $sql_query_total=$sql_query;
        $sql_query=$sql_query." order by fdata_id desc limit $offset,$rows";

        $query = $this->db->query($sql_query);
        $ss=$this->db->last_query();
        $r_total=$this->db->query($sql_query_total)->result_array();
        $row_arr=$query->result_array();
        $result['count']=count($r_total);//获取总行数
        $result["data"] = $row_arr;

        return $result;

    }

    //limit orderc查询

    public function table_seleRow_limit($field,$taname,$wheredata=array(),$likedata=array(),$limit=1,$order=null,$order_type=null){


        $this->db->select($field);
        if(count($wheredata)>0){
            $this->db->where($wheredata);//判断需不需where要查询
        }
        if(count($likedata)>0){
            $this->db->like($likedata);//判断需不需要like查询
        }
        $this->db->limit($limit);
        if(!(is_null($order))){

            $this->db->order_by($order,$order_type);

        }

        $query = $this->db->get($taname);
        $ss=$this->db->last_query();

        $rows_arr=$query->result_array();

        return $rows_arr;


    }

    //插入记录
    public function table_addRow($taname,$values){

        $this->db->insert($taname,$values);
        $result = $this->db->affected_rows();
        $this->db->cache_delete_all();
        return $result;

    }

    //查询记录
    public function table_seleRow($field,$taname,$wheredata=array(),$likedata=array()){

        $this->db->select($field);
        if(count($wheredata)>0){
            $this->db->where($wheredata);//判断需不需where要查询
        }
        if(count($likedata)>0){
            $this->db->like($likedata);//判断需不需要like查询
        }
        $query = $this->db->get($taname);

        $ss=$this->db->last_query();

        $rows_arr=$query->result_array();

        return $rows_arr;

    }

    //修改记录
    public function table_updateRow($taname,$values,$wheredata){

        $this->db->where($wheredata);
        $this->db->update($taname,$values);
        $result = $this->db->affected_rows();
        $this->db->cache_delete_all();
        return $result;

    }

    //删除记录
    public function table_del($taname,$wheredata){

        $this->db->where($wheredata);
        $this->db->delete($taname);
        $result = $this->db->affected_rows();
        $this->db->cache_delete_all();

        return $result;
    }

    //事物处理
    public function table_trans($sql_array)
    {

        if(count($sql_array)>0)
        {

            try {
                $this->db->trans_begin();
                foreach ($sql_array as $sql)
                {
                    $this->db->query($sql);
                }
                if (($this->db->trans_status() === FALSE))
                {
                    $this->db->trans_rollback();
                    return false;
                }
                else {
                    $this->db->trans_commit();
                    $this->db->cache_delete_all();
                    return true;
                }
            }
            catch (Exception $ex)
            {
                $this->db->trans_rollback();
                return false;
            }

        }





    }


    //执行纯SQL语句，返回数组
    public function execute_sql($sql)
    {

        $query = $this->db->query($sql);
        if($query){
            return $query->result_array();
        }
        $ss=$this->db->last_query();
        return array();

    }

    //excel导出
    public function output_excel($wheredata,$likedata)
    {
        $field='fdata_repoid,fdata_proj_name,fdata_invoice_name,fdata_amount
                ,fdata_evaluation,fdata_invoice_type,fdata_invoice_date,fdata_invoice_money
                ,fdata_invoice_num,fdata_money_date,fdata_refund_verify,fdata_refund_date,fdata_alter_date,fdata_alter_money
                ,fdata_alter_rate,fdata_alter_amount,fdata_alter_invoice_num';
        $sql_query="Select $field from finance_data a where not exists(select 1 from finance_data where fdata_total_flag=a.fdata_total_flag and fdata_id>a.fdata_id)";
        if($wheredata!=""){
            $sql_query=$sql_query." and ".$wheredata;
        }
        if($likedata!=""){
            $sql_query=$sql_query." ".$likedata;
        }
        $query = $this->db->query($sql_query);
        $ss=$this->db->last_query();
        $row_arr=$query->result_array();
        return $row_arr;
    }






}