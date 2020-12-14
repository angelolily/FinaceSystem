<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>发票管理</title>
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<link rel="stylesheet" href="../public/layuiadmin/layui/css/layui.css" media="all">
	<link rel="stylesheet" href="../public/layuiadmin/style/admin.css" media="all">
</head>

<style>
	.layui-form-label.layui-required:after{
		content:"*";
		color:red;
		position: absolute;
		top:14px;
		right:5px;
	}
</style>

<body>
<!--顶部区域-->
<div class="layui-fluid">
	<div class="layui-card" >
        <!--查询条件区域-->
        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-inline">
                <div class="layui-inline">
                    <label class="layui-form-label">报告编号</label>
                    <div class="layui-input-block" >
                        <select id="s_repoid" name="s_repoid"  style="width: 300px"   lay-search >
                            <option value="">请选择...</option>
                            <?php
                            if ($rpoid) {
                                foreach ($rpoid as $row) {
                                    ?>
                                    <option
                                            value="<?php echo $row['c_rpoid']; ?>"><?php echo $row['c_rpoid']; ?></option>
                                    <?php
                                }
                            } else {
                                ?>
                                <option value="no">无任何报告编号</option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">业务机构</label>
                    <div class="layui-input-block" >
                        <select id="fdata_jg_id" name="fdata_jg_id"  style="width: 300px"   lay-search >
                            <option value="">请选择...</option>
                            <?php
                            if ($jgID) {
                                foreach ($jgID as $row) {
                                    ?>
                                    <option
                                            value="<?php echo $row['c_jgID']; ?>"><?php echo $row['c_jgName']; ?></option>
                                    <?php
                                }
                            } else {
                                ?>
                                <option value="no">无任何报告编号</option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">开票名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="s_invoice_name" id="s_invoice_name"  style="width: 200px" placeholder="请输入"  class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label" style="width: 100px">开票金额</label>
                    <div class="layui-input-inline" >
                        <input type="text" id="invoice_money1" name="invoice_money1"  class="layui-input">
                    </div>
                    <label class="layui-input-inline">-</label>
                    <div class="layui-input-inline">
                        <input type="text" id="invoice_money2" name="invoice_money2"  class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label" style="width: 100px">报告出具时间</label>
                    <div class="layui-input-inline" >
                        <input type="text" id="bdd" name="bdd"  class="layui-input">
                    </div>
                    <label class="layui-input-inline">-</label>
                    <div class="layui-input-inline">
                        <input type="text" id="edd" name="edd"  class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label" style="width: 100px">开票时间</label>
                    <div class="layui-input-inline" >
                        <input type="text" id="kbdd" name="kbdd"  class="layui-input">
                    </div>
                    <label class="layui-input-inline">-</label>
                    <div class="layui-input-inline">
                        <input type="text" id="kedd" name="kedd"  class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">发票状态</label>
                    <div class="layui-input-inline">
                        <select id="fdata_statue" name="fdata_statue"   >
                            <option value="">请选择...</option>
                            <option value="申请审核中">发票申请中</option>
                            <option value="开票中">开票中</option>
                            <option value="发票已寄出">发票已寄出</option>
                            <option value="已开票">已开票</option>
                            <option value="已退票">已退票</option>
                            <option value="改开票">改开票</option>
                            <option value="申请退改">申请退改</option>
                            <option value="申请驳回">申请驳回</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn layui-btn-normal" lay-submit lay-filter="LAY-invoice-search">
                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                    </button>
                </div>
            </div>
        </div>


		<div class="layui-card-body">
			<!--按钮功能区-->
			<div style="padding-bottom: 10px;">
				<button class="layui-btn layuiadmin-btn-admin layui-btn-normal" style="background-color: rgb(130,57,53)" data-type="add">开票信息填写</button>
				<button class="layui-btn layuiadmin-btn-admin layui-btn-normal" style="background-color: rgb(64,116,52)" data-type="cancel">退票信息填写</button>
                <button class="layui-btn layuiadmin-btn-admin layui-btn-normal"  data-type="update">改开票信息填写</button>
                <button class="layui-btn layuiadmin-btn-admin layui-btn-normal" style="background-color: rgb(20,68,106)"  data-type="excel">导出Excel</button>
			</div>
			<!--数据表格-->
			<table id="finace_data_table" lay-filter="finace_data_table"></table>

			<!--表格按钮-->
			<script type="text/html" id="table-useradmin-webuser">
                {{#  if(d.fdata_total_flag!=null){}}
                <a class="layui-btn layui-btn-xs" lay-event="detail_app"><i class="layui-icon layui-icon-template-1"></i>查看明细</a>
                {{#  } else { }}
                {{#  } }}
			</script>

            <script type="text/html" id="fdata_jg-Tpl">
                {{#
                var ss;
                g_jg.forEach(function(item){
                    if(d.fdata_jg_id==item.c_jgID)
                    {
                        ss=item.c_jgName;
                    }
                });

                }}
                {{= ss }}
            </script>

			<!--字段模版-->
			<script type="text/html" id="fdata_invoice_type-Tpl">
				{{#  if(d.fdata_invoice_type == 1){ }}
				普票
				{{#  } else { }}
				专票
				{{#  } }}
			</script>

            <!--字段模版-->
            <script type="text/html" id="fdata_refund_verify-Tpl">
                {{#  if(d.fdata_refund_verify == 'on'){ }}
                同意
                {{#  } else if(d.fdata_invoice_type == 'off') { }}
                不同意
                {{#  } }}
            </script>

		</div>
	</div>
</div>



<!--填写新开票信息-->
<from class="layui-form" lay-filter="invoice_add" id="invoice_info_add" style="display:none;padding: 20px;">
    <div class="layui-form-item">
        <label class="layui-form-label  layui-required">税率</label>
        <div class="layui-input-inline" >
            <input type="text" name="fdata_tax_rate" id="fdata_tax_rate" lay-verify="required" class="layui-input" >
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label  layui-required">税额</label>
        <div class="layui-input-inline">
            <input type="text" name="fdata_tax_amount" id="fdata_tax_amount"   lay-verify="required" class="layui-input" >
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">开票金额</label>
        <div class="layui-input-inline">
            <input type="text"  name="fdata_invoice_money" id="fdata_invoice_money" readonly="true" class="layui-input" >
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label layui-required">发票号</label>
        <div class="layui-input-inline">
            <input type="text"  name="fdata_invoice_num" id="fdata_invoice_num" lay-verify="required" class="layui-input" >
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label layui-required ">收款日期</label>
        <div class="layui-input-inline" >
            <input type="text" name="fdata_money_date" id="fdata_money_date" class="layui-input" >
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label layui-required ">开票日期</label>
        <div class="layui-input-inline" >
            <input type="text" name="fdata_invoice_date" id="fdata_invoice_date"   lay-verify="required"  class="layui-input" >
        </div>
    </div>

	<div class="layui-form-item layui-hide">
		<input type="button" lay-submit lay-filter="LAY-invoice-front-submit" id="LAY-invoice-front-submit" value="确认提交">
	</div>

</from>

<!--填写退票信息-->
<from class="layui-form" lay-filter="invoice_cancel" id="invoice_cancel" style="display:none;padding: 20px;">
    <div class="layui-form-item">
        <label class="layui-form-label " >同意退票</label>
        <div class="layui-input-inline" >
            <input type="checkbox" name="fdata_refund_verify" id="fdata_refund_verify" lay-skin="switch" lay-filter="fdata_refund_verify" lay-text="同意|不同意">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label ">退票单号(红)</label>
        <div class="layui-input-inline">
            <input type="text" name="fdata_rad_num" id="fdata_rad_num" placeholder="次月退票填写" class="layui-input" >
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label ">退票金额(红)</label>
        <div class="layui-input-inline">
            <input type="text" name="fdata_rad_money" id="fdata_rad_money" placeholder="填写开票金额负数" class="layui-input" >
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label layui-required ">退票日期</label>
        <div class="layui-input-inline" >
            <input type="text" name="fdata_refund_date" id="fdata_refund_date" lay-verify="required" class="layui-input" >
        </div>
    </div>

    <div class="layui-form-item layui-hide">
        <input type="button" lay-submit lay-filter="LAY-invoice-back-submit" id="LAY-invoice-back-submit" value="确认提交">
    </div>

</from>

<!--填写改票信息-->
<from class="layui-form" lay-filter="invoice_update" id="invoice_update" style="display:none;padding: 20px;">
    <div class="layui-form-item">
        <label class="layui-form-label ">改开发票号</label>
        <div class="layui-input-inline">
            <input type="text" name="fdata_alter_invoice_num" id="fdata_alter_invoice_num"  class="layui-input" >
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label ">改开税率</label>
        <div class="layui-input-inline">
            <input type="text" name="fdata_alter_rate" id="fdata_alter_rate"  class="layui-input" >
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label ">改开税额</label>
        <div class="layui-input-inline">
            <input type="text" name="fdata_alter_amount" id="fdata_alter_amount"  class="layui-input" >
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label ">改开金额</label>
        <div class="layui-input-inline">
            <input type="text" name="fdata_alter_money" id="fdata_alter_money"  class="layui-input" >
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label layui-required ">改开日期</label>
        <div class="layui-input-inline" >
            <input type="text" name="fdata_alter_date" id="fdata_alter_date" lay-verify="required" class="layui-input" >
        </div>
    </div>

    <div class="layui-form-item layui-hide">
        <input type="button" lay-submit lay-filter="LAY-invoice-update-submit" id="LAY-invoice-update-submit" value="确认提交">
    </div>

</from>





</body>
<div style="display: none;" id="work_detail" name="work_detail">
    <table id="example" lay-filter="example"></table>
</div>


<script src="../public/layui/layui.all.js"></script>
<script src="https://www.layuicdn.com/extend/excel/1.6.5/layui_exts/excel.min.js"></script>
<script>
	var $ = layui.$;
	var g_proj=<?php echo json_encode($rpoid); ?>;//项目信息
    var g_jg=<?php echo json_encode($jgID); ?>;//项目信息
	var table = layui.table;
	var form = layui.form;
	var gb_index=5;//当前打开的dialog
    var checkedSet = new Set();
	var gb_seledata=[];//选中的记录
	var gb_total_count=0;
	var g_searchval;
    var g_bdate,g_edate,g_kbdate,g_kedate="";
	var laydate = layui.laydate;
	var finace_data_table =table.render({
		elem: '#finace_data_table',　　　//html中table窗口的id
		height: 'full-220',
        id:'finace_data',
		url:'./Control_Invoice/get_invoice',
		loading: true,
		toolbar:true,
		text: {
			none: '空空如也'
		},
		title: '发票管理',
		page: {
			layout: ['count', 'prev', 'page', 'next', 'limit', 'refresh', 'skip'],
			limit: 20

		},
        parseData:function (res)
        {
            console.log(checkedSet);
            for(var i in res.data){

                if(checkedSet.has(res.data[i].fdata_num)){
                    //如果set集合中有的话，给rows添加check属性选中
                    res.data[i]["LAY_CHECKED"] = true;
                }
            }
            return {
                "code": res.code, //解析接口状态
                "count": res.count, //解析数据长度
                "data": res.data //解析数据列表
            };

        },
		cols: [[
			{id:'radio',type: 'radio', rowspan: 2,fixed: 'left'}
			,{field: 'fdata_num',align:'center',rowspan: 2,title: '单号',width:150}
			,{field: 'fdata_statue',align:'center', rowspan: 2,title: '状态',width:150}
            ,{field: 'fdata_jg_id',align:'center',rowspan: 2, title: '业务机构',width:150,templet:'#fdata_jg-Tpl'}
            ,{field: 'fdata_emp',align:'center', rowspan: 2,title: '申请人',width:150}
            ,{field: 'fdata_proj_name', align:'center',rowspan: 2,title: '项目名称',width:300}
            ,{field: 'fdata_total_flag',align:'center',rowspan: 2,title: '合计标识',width:150,sort:true}
            ,{field: 'fdata_total_money',align:'center',rowspan: 2,title: '合计开票金额',width:200}
            ,{field: 'fdata_amount',align:'center',rowspan: 2,title: '评估额',width:150}
            ,{field: 'fdata_evaluation',align:'center',rowspan: 2,title: '应收评估费',width:150}
			,{field: 'fdata_repoid',align:'center', rowspan: 2,title: '报告编号',width:150}
            ,{field: '', colspan: 8,align:'center',title: '开票信息',width:250}
            ,{field: '', colspan: 3,align:'center',title: '发票信息',width:350}
            ,{field: '', colspan: 4,align:'center',title: '日期信息',width:350}
            ,{field: '', colspan: 4,align:'center',title: '退票信息',width:350}
            ,{field: '', colspan: 6,align:'center',title: '改票信息',width:350}

           ,{field: 'fdata_rete',align:'center',rowspan: 2,title: '备注',width:250}
            ,{title: '操作', align:'center', rowspan: 2,fixed: 'right', toolbar: '#table-useradmin-webuser',width:100}
		],
            [{field: 'fdata_invoice_name',align:'center',title: '开票名称',width:200}
            ,{field: 'fdata_bank',align:'center',title: '开户行名称',width:150}
            ,{field: 'fdata_invoice_money',align:'center',title: '开票金额',width:150}
            ,{field: 'fdata_tax_num',align:'center',title: '税号',width:150}
            ,{field: 'fdata_bank_address',align:'center',title: '开户行地址',width:150}
            ,{field: 'fdata_bank_phone',align:'center',title: '开户行电话',width:150}
            ,{field: 'fdata_bank_num',align:'center',title: '开户行账号',width:150}
            ,{field: 'fdata_invoice_type',align:'center',title: '发票类型',width:150,templet:'#fdata_invoice_type-Tpl'}
                ,{field: 'fdata_tax_rate',align:'center',rowspan: 2,title: '税率',width:150}
                ,{field: 'fdata_tax_amount',align:'center',rowspan: 2,title: '税额',width:150}
                ,{field: 'fdata_invoice_num',align:'center',rowspan: 2,title: '发票号',width:150}
                ,{field: 'fdata_invoice_date',align:'center',title: '开票日期',width:150}
                ,{field: 'fdata_money_date',align:'center',title: '收款日期',width:150}
                ,{field: 'fdata_refund_date',align:'center',title: '退票日期',width:150}
                ,{field: 'fdata_alter_date',align:'center',title: '改开日期',width:150}
                ,{field: 'fdata_refund_reason',align:'center',rowspan: 2,title: '退改理由',width:250}
                ,{field: 'fdata_rad_num',align:'center',rowspan: 2,title: '退票红单号',width:150}
                ,{field: 'fdata_rad_money',align:'center',rowspan: 2,title: '退票红金额',width:150}
                ,{field: 'fdata_refund_verify',align:'center',rowspan: 2,title: '退改审核',width:150,templet:'#fdata_refund_verify-Tpl'}
                ,{field: 'fdata_alter_money',align:'center',rowspan: 2,title: '改开金额',width:150}
                ,{field: 'fdata_alter_rate',align:'center',rowspan: 2,title: '改开税率',width:150}

                ,{field: 'fdata_alter_amount',align:'center',rowspan: 2,title: '改开税额',width:150}
                ,{field: 'fdata_alter_invoice_num',align:'center',rowspan: 2,title: '改开发票号',width:150}
                ,{field: 'fdata_alter_amount',align:'center',rowspan: 2,title: '改开税额',width:150}
                ,{field: 'fdata_alter_invoice_num',align:'center',rowspan: 2,title: '改开发票号',width:150}

                ]

        ]


	});//表格



    //绑定按钮事件
    $('.layui-btn.layui-btn-normal').on('click', function(){
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });

    //跨页check后，记忆存储
    table.on('checkbox(finace_data_table)', function(obj){
        //  console.log(obj.checked); //当前是否选中状态
        //  console.log(obj.data); //选中行的相关数据
        // console.log(obj.type); //如果触发的是全选，则为：all，如果触发的是单选，则为：one

        if(obj.type=='all'){
            if(obj.checked)
            {
                var checkStatus = table.checkStatus('finace_data');
                console.log(checkStatus.data);
                checkStatus.data.forEach(function (val){
                    checkedSet.add(val.fdata_num);
                    gb_total_count=gb_total_count+Number(val.fdata_invoice_money);
                });
            }
            else{

                layer.confirm('取消全选，会将所有选择全部取消，请确认', {icon: 3, title:'提示'}, function(index){
                    layer.close(index);
                });
                checkedSet.clear();
                gb_total_count=0;

            }

        }
        else
        {
            //选中时加入set 否则移除
            if(obj.checked){
                checkedSet.add(obj.data.fdata_num);
                gb_total_count=gb_total_count+Number(obj.data.fdata_invoice_money);
            }else{
                checkedSet.delete(obj.data.fdata_num)
                gb_total_count=gb_total_count-Number(obj.data.fdata_invoice_money);
            }
        }

        //console.log(checkedSet);
    });


    //监听搜索
    form.on('submit(LAY-invoice-search)', function(data){
        var field = data.field;
        g_searchval= data.field;
        //执行重载
        finace_data_table.reload({
            where: { //设定异步数据接口的额外参数，任意设
                val: field
            }
            ,page: {
                curr: 1 //重新从第 1 页开始
            }
        });
    });

    //事件
    var active = {
        add: function(){
            $("#invoice_info_add").find('input[type=text],select,input[type=hidden]').each(function() {
                $(this).val('');
            });

            var checkStatus = table.checkStatus('finace_data'),cdata =checkStatus.data;
            console.log(cdata);
            if (checkStatus.data.length<1){
                layer.alert('请勾选要填写的开票信息！');
                return false;
            }

            if(checkStatus.data[0].fdata_statue!='开票中' && checkStatus.data[0].fdata_statue!='已开票' && checkStatus.data[0].fdata_statue!='发票已寄出')
            {
                layer.alert('必须是已审核的项目，或未收款，才能开具发票！');
                return false;

            }
            else
            {
                form.val('invoice_add',checkStatus.data[0]);

            }



            layer.open({
                type: 1
                ,title: '发票信息填写'
                ,content: $("#invoice_info_add")
                ,maxmin: true
                ,area: ['600px', '450px']
                ,btn: ['提交申请']
                ,anim: 3
                ,end:function () {
                    $("#invoice_info_add").find('input[type=text],select,input[type=hidden]').each(function() {
                        $(this).val('');
                    });

                }
                ,success: function(layero, index){
                   $('#fdata_invoice_money').val(checkStatus.data[0].fdata_invoice_money);
                }
                ,yes: function(index, layero){
                    var submit = $("#LAY-invoice-front-submit");
                    //监听提交
                    form.on('submit(LAY-invoice-front-submit)', function(data){

                        var field = data.field; //获取提交的字段
                        //提交 Ajax 成功后，静态更新表格中的数据
                        $.ajax({
                            type:'post',
                            url:'./Control_Invoice/invoice_info_update',
                            data:{val:field,fdata:checkStatus.data,type:1},
                            dataType:'json',
                            async : false,
                            success:function (result) {
                                if(result.code){
                                    layer.msg(result.msg, {icon: 6});
                                    finace_data_table.reload();//数据刷新
                                    layer.close(index);//关闭弹层
                                }
                                else{
                                    layer.msg(result.msg, {icon: 5});
                                }
                            }

                        });
                    });

                    submit.trigger('click');
                }
            });
        }
        ,cancel: function(){
            $("#invoice_cancel").find('input[type=text],select,input[type=hidden]').each(function() {
                $(this).val('');
            });

            var checkStatus = table.checkStatus('finace_data'),cdata =checkStatus.data;
            console.log(cdata);
            if (checkStatus.data.length<1){
                layer.alert('请勾选要填写的退票信息！');
                return false;
            }
            console.log(checkStatus.data[0].fdata_statue);
            if(checkStatus.data[0].fdata_statue!='申请退改')
            {
                layer.alert('必须是已申请退改的项目，才能退票！');
                return false;

            }

            layer.open({
                type: 1
                ,title: '退票信息填写'
                ,content: $("#invoice_cancel")
                ,maxmin: true
                ,area: ['600px', '450px']
                ,btn: ['提交申请']
                ,anim: 3
                ,end:function () {
                    $("#invoice_cancel").find('input[type=text],select,input[type=hidden]').each(function() {
                        $(this).val('');
                    });

                }
                ,yes: function(index, layero){
                    var submit = $("#LAY-invoice-back-submit");
                    //监听提交
                    form.on('submit(LAY-invoice-back-submit)', function(data){

                        var field = data.field; //获取提交的字段
                        //提交 Ajax 成功后，静态更新表格中的数据
                        $.ajax({
                            type:'post',
                            url:'./Control_Invoice/invoice_info_update',
                            data:{val:field,fdata:checkStatus.data,type:2},
                            dataType:'json',
                            async : false,
                            success:function (result) {
                                if(result.code){
                                    layer.msg(result.msg, {icon: 6});
                                    finace_data_table.reload();//数据刷新
                                    layer.close(index);//关闭弹层
                                }
                                else{
                                    layer.msg(result.msg, {icon: 5});
                                }
                            }

                        });
                    });

                    submit.trigger('click');
                }
            });
        }
        ,update: function(){
            $("#invoice_update").find('input[type=text],select,input[type=hidden]').each(function() {
                $(this).val('');
            });

            var checkStatus = table.checkStatus('finace_data'),cdata =checkStatus.data;
            console.log(cdata);
            if (checkStatus.data.length<1){
                layer.alert('请勾选要填写的退票信息！');
                return false;
            }
            console.log(checkStatus.data[0].fdata_statue);
            if(checkStatus.data[0].fdata_statue!='已退票')
            {
                layer.alert('必须是已退票的项目，才能改开！');
                return false;

            }

            layer.open({
                type: 1
                ,title: '改开信息填写'
                ,content: $("#invoice_update")
                ,maxmin: true
                ,area: ['600px', '450px']
                ,btn: ['提交申请']
                ,anim: 3
                ,end:function () {
                    $("#invoice_update").find('input[type=text],select,input[type=hidden]').each(function() {
                        $(this).val('');
                    });

                }
                ,yes: function(index, layero){
                    var submit = $("#LAY-invoice-update-submit");
                    //监听提交
                    form.on('submit(LAY-invoice-update-submit)', function(data){

                        var field = data.field; //获取提交的字段
                        //提交 Ajax 成功后，静态更新表格中的数据
                        $.ajax({
                            type:'post',
                            url:'./Control_Invoice/invoice_info_update',
                            data:{val:field,fdata:checkStatus.data,type:3},
                            dataType:'json',
                            async : false,
                            success:function (result) {
                                if(result.code){
                                    layer.msg(result.msg, {icon: 6});
                                    finace_data_table.reload();//数据刷新
                                    layer.close(index);//关闭弹层
                                }
                                else{
                                    layer.msg(result.msg, {icon: 5});
                                }
                            }

                        });
                    });

                    submit.trigger('click');
                }
            });
        }
        ,excel: function(){

            $.ajax({
                type:'post',
                url:'./Control_Invoice/outexcel',
                data:{val:g_searchval},
                dataType:'json',
                async : false,
                success:function (result) {
                    console.log(result);
                    result.unshift({fdata_repoid: '报告编号',fdata_proj_name: '项目名称'
                                    ,fdata_invoice_name: '开票名称',fdata_amount: '评估额'
                                    ,fdata_evaluation: '应收评估费',fdata_invoice_type: '发票类型'
                                    ,fdata_invoice_date: '开票日期',fdata_invoice_money: '开票金额'
                                    ,fdata_invoice_num: '发票号',fdata_money_date: '收款日期'
                                    ,fdata_refund_verify: '退改审核',fdata_refund_date: '退票日期'
                                    ,fdata_alter_date: '改开日期',fdata_alter_money: '改开金额'
                                    ,fdata_alter_rate: '改开税率',fdata_alter_amount: '改开税额',fdata_alter_invoice_num: '改开发票号'});
                    LAY_EXCEL.exportExcel(result, '表格导出.xlsx', 'xlsx');
                }

            });


        }
    };

    //监听工具条
    table.on('tool(finace_data_table)', function(obj){
        var selectrow =obj.data;
        if(obj.event === 'cancel_app'){
            if(selectrow.fdata_statue=="发票申请中")
            {
                layer.confirm('确定撤回该笔发票申请对吗', function(index){
                    //提交 Ajax 成功后，删除更新表格中的数据

                    console.log(obj);
                    $.ajax({
                        type:'post',
                        url:'./Control_InvoiceApp/cancel_invoice_app',
                        data:{val:selectrow},
                        dataType:'json',
                        async : false,
                        success:function (result) {
                            if(result.code){
                                layer.msg(result.msg, {icon: 6});
                                finace_data_table.reload();//数据刷新
                                layer.close(index);//关闭弹层
                            }
                            else{
                                layer.msg(result.msg, {icon: 5});
                                layer.close(index);//关闭弹层
                            }
                        }

                    });
                });
            }
            else
            {
                layer.msg("状态必须是申请中，才可以撤回申请", {icon: 5});
            }


        }
        else if(obj.event === 'detail_app')
        {
            var temps={};
            temps.fdata_total_flag=selectrow.fdata_total_flag;
            console.log(temps);
            layer.open({
                type: 1
                ,title: '明细数据'
                ,content: $("#work_detail")
                ,maxmin: true
                ,area: ['1024px', '612px']
                ,btn: ['取消']
                ,anim: 3
                ,toolbar:true
                ,success: function(layero, index){
                    table.render({
                        elem: '#example',　　　//html中table窗口的id
                        height: 'full-150',
                        url:'./Control_InvoiceVerify/get_invoiceApp_data',
                        loading: true,
                        where:{val:temps},
                        text: {
                            none: '空空如也'
                        },
                        title: '合并明细开票信息',
                        toolbar:true,
                        page: {
                            layout: ['count', 'prev', 'page', 'next', 'limit', 'refresh', 'skip'],
                            limit: 20

                        },
                        cols: [[
                             {field: 'fdata_proj_name', align:'center',title: '项目名称',width:300}
                            ,{field: 'fdata_invoice_money',align:'center',title: '开票金额(元)',width:150}
                            ,{field: 'fdata_repoid',align:'center',title: '报告编号',width:150}
                            ,{field: 'fdata_cjrpotdate', align:'center',title: '出具报告日期',width:150}
                            ,{field: 'fdata_report_type',align:'center',title: '报告类型',width:150}
                            ,{field: 'fdata_entrust',align:'center',title: '委托方',width:150}
                            ,{field: 'fdata_amount',align:'center',title: '评估额(万元)',width:150}
                            ,{field: 'fdata_evaluation',align:'center',title: '应收评估费(元)',width:150}
                        ]],

                    });//表格
                }

            });

        }
    });

    laydate.render({
        elem: '#fdata_refund_date'
        ,type: 'date'
        ,format: 'yyyy-MM-dd'
        ,done: function(value, date, endDate){

            //得到日期时间对象：{year: 2017, month: 8, date: 18, hours: 0, minutes: 0, seconds: 0}
        }
    });

    laydate.render({
        elem: '#fdata_money_date'
        ,type: 'date'
        ,format: 'yyyy-MM-dd'
        ,done: function(value, date, endDate){

            //得到日期时间对象：{year: 2017, month: 8, date: 18, hours: 0, minutes: 0, seconds: 0}
        }
    });

    laydate.render({
        elem: '#fdata_invoice_date'
        ,type: 'date'
        ,format: 'yyyy-MM-dd'
        ,done: function(value, date, endDate){

             //得到日期时间对象：{year: 2017, month: 8, date: 18, hours: 0, minutes: 0, seconds: 0}
        }
    });

    laydate.render({
        elem: '#fdata_alter_date'
        ,type: 'date'
        ,format: 'yyyy-MM-dd'
        ,done: function(value, date, endDate){

            //得到日期时间对象：{year: 2017, month: 8, date: 18, hours: 0, minutes: 0, seconds: 0}
        }
    });
    laydate.render({
        elem: '#bdd'
        ,type: 'date'
        ,format: 'yyyy-MM-dd'
        ,done: function(value, date, endDate){

            g_bdate=value;//得到日期时间对象：{year: 2017, month: 8, date: 18, hours: 0, minutes: 0, seconds: 0}
        }
    });

    laydate.render({
        elem: '#edd'
        ,type: 'date'
        ,format: 'yyyy-MM-dd'
        ,done: function(value, date, endDate){

            g_edate=value;
            var oDate1 = new Date(g_bdate);
            var oDate2 = new Date(value);

            if(oDate1.getTime() > oDate2.getTime()){
                layer.msg("开始日期不能大结束日期",{icon: 5});
            }
            //得到日期时间对象：{year: 2017, month: 8, date: 18, hours: 0, minutes: 0, seconds: 0}
        }
    });


    laydate.render({
        elem: '#kbdd'
        ,type: 'date'
        ,format: 'yyyy-MM-dd'
        ,done: function(value, date, endDate){

            g_kbdate=value;//得到日期时间对象：{year: 2017, month: 8, date: 18, hours: 0, minutes: 0, seconds: 0}
        }
    });

    laydate.render({
        elem: '#kedd'
        ,type: 'date'
        ,format: 'yyyy-MM-dd'
        ,done: function(value, date, endDate){

            g_kedate=value;
            var oDate1 = new Date(g_kbdate);
            var oDate2 = new Date(value);
            if(oDate1.getTime() > oDate2.getTime()){
                layer.msg("开始日期不能大结束日期",{icon: 5});
            }
            //得到日期时间对象：{year: 2017, month: 8, date: 18, hours: 0, minutes: 0, seconds: 0}
        }
    });





























</script>




