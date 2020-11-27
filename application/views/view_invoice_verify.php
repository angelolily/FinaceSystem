<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>发票审核管理</title>
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
                            <option value="申请审核中">申请审核中</option>
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
				<button class="layui-btn layuiadmin-btn-admin layui-btn-normal" style="background-color: rgb(64,116,52)"  data-type="pass">审核通过</button>
				<button class="layui-btn layuiadmin-btn-admin layui-btn-normal" style="background-color: rgb(130,57,53)" data-type="back">审核驳回</button>
                <button class="layui-btn layuiadmin-btn-admin layui-btn-normal"  data-type="express">物流信息</button>
			</div>
			<!--数据表格-->
			<table id="finace_data_table" lay-filter="finace_data_table"></table>

            <!--表格按钮-->
            <script type="text/html" id="table-useradmin-webuser">
                <a class="layui-btn layui-btn-dange layui-btn-xs" lay-event="cancel_app"><i class="layui-icon layui-icon-file"></i>查看报告</a>
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

		</div>
	</div>
</div>

<from class="layui-form" lay-filter="express_add" id="express_add" style="display:none;padding: 20px;">
    <div class="layui-form-item">
        <label class="layui-form-label">快递公司</label>
        <div class="layui-input-inline" style="width: 30%;">
            <select id="fdata_express_company" name="fdata_express_company" lay-verify="required" lay-filter='fdata_express_company' lay-search >
                <option value="">请选择...</option>
                <option value="自取">自取</option>
                <option value="顺丰">顺丰</option>
                <option value="京东">京东</option>
                <option value="EMS">EMS</option>
                <option value="圆通">圆通</option>
                <option value="中通">中通</option>
                <option value="申通">申通</option>
                <option value="百世汇通">百世汇通</option>
                <option value="韵达">韵达</option>
            </select>
        </div>
        <label class="layui-form-label layui-required">快递单号</label>
        <div class="layui-input-inline" style="width: 45%;">
            <input type="text" name="fdata_express_num" id="fdata_express_num"  placeholder=""  class="layui-input" >
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label layui-required">领用人</label>
        <div class="layui-input-inline" style="width: 30%;">
            <input type="text" name="fdata_invoice_emp" id="fdata_invoice_emp" lay-verify="required"   class="layui-input" >
        </div>
        <label class="layui-form-label layui-required ">快递日期</label>
        <div class="layui-input-inline" style="width: 45%;">
            <input type="text" name="fdata_express_date" id="fdata_express_date" lay-verify="required"   class="layui-input" >
        </div>
    </div>
    <div class="layui-form-item layui-hide">
        <input type="button" lay-submit lay-filter="LAY-invoice-front-submit" id="LAY-invoice-front-submit" value="确认提交">
    </div>

</from>

</body>



<script src="../public/layui/layui.all.js"></script>


<script>
	var $ = layui.$;
	var g_proj=<?php echo json_encode($rpoid); ?>;//项目信息
    var g_jg=<?php echo json_encode($jgID); ?>;//项目信息
	var table = layui.table;
	var form = layui.form;
    var g_baegindata;
    var gb_total_count=0;
	var gb_index=5;//当前打开的dialog
    var g_bdate,g_edate,g_kbdate,g_kedate="";
    var checkedSet = new Set();
	var gb_seledata=[];//选中的记录
	var laydate = layui.laydate;
	var finace_data_table =table.render({
		elem: '#finace_data_table',　　　//html中table窗口的id
		height: 'full-220',
        id:'finace_data',
		url:'./Control_InvoiceVerify/get_invoiceApp_data',
		loading: true,
		toolbar:true,
		text: {
			none: '空空如也'
		},
		title: '审核发票',
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
			{id:'check_1',type: 'checkbox',fixed: 'left'}
			,{field: 'fdata_num',align:'center',title: '单号',width:150}
			,{field: 'fdata_jg_id',align:'center', title: '业务机构',width:150,templet:'#fdata_jg-Tpl'}
            ,{field: 'fdata_emp',align:'center', title: '申请人',width:150}
            ,{field: 'fdata_statue',align:'center', title: '状态',width:150}
            ,{field: 'fdata_total_flag',align:'center',title: '合计标识',width:150,sort:true}
            ,{field: 'fdata_total_money',align:'center',title: '合计开票金额',width:200}
			,{field: 'fdata_repoid',align:'center', title: '报告编号',width:150}
			,{field: 'fdata_proj_name', align:'center',title: '项目名称',width:300}
			,{field: 'fdata_invoice_name',align:'center',title: '开票名称',width:200}
			,{field: 'fdata_invoice_money',align:'center',title: '开票金额',width:150}
            ,{field: 'fdata_invoice_type',align:'center',title: '发票类型',width:150,templet:'#fdata_invoice_type-Tpl'}
			,{field: 'fdata_express_company',align:'center', title: '快递公司',width:150}
			,{field: 'fdata_express_date',align:'center', title: '快递日期',width:150}
			,{field: 'fdata_express_num',align:'center', title: '快递号码',width:150}
			,{field: 'fdata_cjrpotdate', align:'center',title: '出具报告日期',width:150}
			,{field: 'fdata_report_type',align:'center',title: '报告类型',width:150}
			,{field: 'fdata_entrust',align:'center',title: '委托方',width:150}
			,{field: 'fdata_amount',align:'center',title: '评估额',width:150}
			,{field: 'fdata_evaluation',align:'center',title: '评估费',width:150}
			,{field: 'fdata_tax_num',align:'center',title: '税号',width:150}
			,{field: 'fdata_bank_address',align:'center',title: '开户行地址',width:150}
            ,{field: 'fdata_bank_phone',align:'center',title: '开户行电话',width:150}
            ,{field: 'fdata_invoice_emp',align:'center',title: '发票领用人',width:150}
            ,{field: 'fdata_invoice_data',align:'center',title: '开票日期',width:150}
            ,{field: 'fdata_money_data',align:'center',title: '收款日期',width:150}
            ,{field: 'fdata_refund_data',align:'center',title: '退票日期',width:150}
            ,{field: 'fdata_rete',align:'center',title: '备注',width:250}
            ,{title: '操作', align:'center', fixed: 'right', toolbar: '#table-useradmin-webuser',width:100}
		]]


	});//表格

    console.log(g_jg);

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
        pass: function(){

            var checkStatus = table.checkStatus('finace_data'),data =checkStatus.data;
            console.log(checkStatus);
            if (checkStatus.data.length<1){
                layer.alert('请勾选要审核信息！');
                return false;
            }

            layer.confirm('请确认审核的申请开票项目数：'+checkStatus.data.length, function(index){
                $.ajax({
                    type:'post',
                    url:'./Control_InvoiceVerify/verify_invoice_app',
                    data:{val:checkStatus,type:2,back_reason:""},
                    dataType:'json',
                    async : false,
                    success:function (result) {
                        if(result.code){
                            layer.msg(result.msg, {icon: 6});
                            finace_data_table.reload();
                        }
                        else{
                            layer.msg(result.msg, {icon: 5});

                        }
                    }
                });
            });
        }
        ,back: function(){

            var checkStatus = table.checkStatus('finace_data'),data =checkStatus.data;
            console.log(checkStatus);
            if (checkStatus.data.length<1){
                layer.alert('请勾选要驳回审核信息！');
                return false;
            }

            layer.prompt({
                formType: 2,
                value: '',
                title: '驳回理由',
                area: ['300px', '250px'] //自定义文本域宽高
            }, function(value, index, elem){
                alert(value); //得到value
                layer.close(index);
                layer.confirm('请确认驳回审核的申请开票项目数：'+checkStatus.data.length, function(index){
                    $.ajax({
                        type:'post',
                        url:'./Control_InvoiceVerify/verify_invoice_app',
                        data:{val:checkStatus,type:1,back_reason:value},
                        dataType:'json',
                        async : false,
                        success:function (result) {
                            if(result.code){
                                layer.msg(result.msg, {icon: 6});
                                finace_data_table.reload();
                            }
                            else{
                                layer.msg(result.msg, {icon: 5});

                            }
                        }
                    });
                });
            });




        }
        ,express: function(){

            var checkStatus = table.checkStatus('finace_data'),data =checkStatus.data;
            console.log(checkStatus);
            if (checkStatus.data.length<1){
                layer.alert('请勾选要填写物流的信息！');
                return false;
            }
            var flag=0;
            checkStatus.data.forEach(function (item){

                if(item.fdata_statue!="已开票")
                {

                    flag=1;

                }

            });

            if(flag==1)
            {
                layer.alert('选择的项目中有未开票！');
                return false;
            }

            $("#express_add").find('input[type=text],select,input[type=hidden]').each(function() {
                $(this).val('');
            });

            layer.confirm('请确认要填写物流信息项目数：'+checkStatus.data.length, function(index){
                layer.open({
                    type: 1
                    ,title: '物流信息更新'
                    ,content: $("#express_add")
                    ,maxmin: true
                    ,area: ['1000px', '400px']
                    ,btn: ['更新物流']
                    ,anim: 3
                    ,end:function () {
                        $("#express_add").find('input[type=text],select,input[type=hidden]').each(function() {
                            $(this).val('');
                        });

                    }
                    ,yes: function(indexs, layero){
                        var submit = $("#LAY-invoice-front-submit");
                        //监听提交
                        form.on('submit(LAY-invoice-front-submit)', function(data){

                            var field = data.field; //获取提交的字段
                            //提交 Ajax 成功后，静态更新表格中的数据
                            $.ajax({
                                type:'post',
                                url:'./Control_InvoiceVerify/verify_invoice_app',
                                data:{val:checkStatus,type:3,back_reason:"",express:field},
                                dataType:'json',
                                async : false,
                                success:function (result) {
                                    if(result.code){
                                        layer.msg(result.msg, {icon: 6});
                                        finace_data_table.reload();//数据刷新
                                        layer.close(indexs);//关闭弹层
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
    });

    laydate.render({
        elem: '#fdata_express_date'
        ,type: 'datetime'
        ,format: 'yyyy年MM月dd日'
        ,done: function(value, date, endDate){

            g_baegindata=date.year+'-'+date.month+'-'+date.date; //得到日期时间对象：{year: 2017, month: 8, date: 18, hours: 0, minutes: 0, seconds: 0}
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




