<?php


class Jko_Model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->db=$this->load->database('jko',true);



    }


    //获取所有价格表数据
    public function get_All_Price($pages,$rows,$wheredata,$likedata){

        $offset=($pages-1)*$rows;//计算偏移量
        $data = array();


        $this->db->select('c_priceid');
        if(count($wheredata)>0){
            $this->db->where($wheredata);//判断需不需要查询
        }
        if(count($likedata)>0){
            $this->db->like($likedata);//判断需不需要查询
        }
        $this->db->from('tbl_price_control');
        $total=$this->db->count_all_results();//查询总条数


        $this->db->select('*');
        if(count($wheredata)>0){
            $this->db->where($wheredata);//判断需不需要查询
        }
        if(count($likedata)>0){
            $this->db->like($likedata);//判断需不需要查询
        }
        $this->db->limit($rows,$offset);
        $this->db->order_by('c_control_overdate','desc');
        $query = $this->db->get('tbl_price_control');

        $ss=$this->db->last_query();

        $row_arr=$query->result_array();

        foreach ($row_arr as $row)
        {

            array_push($data, $row);
        }

        $result['count']=$total;//获取总行数
        $result["data"] = $data;

        return $result;

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

        return $result;

    }

    //插入记录
    public function table_addRow($taname,$values){

        $this->db->insert($taname,$values);
        $result = $this->db->affected_rows();

        return $result;

    }

}