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
				<button class="layui-btn layuiadmin-btn-admin layui-btn-normal" style="background-color: rgb(130,57,53)" data-type="add">发票申请</button>
				<button class="layui-btn layuiadmin-btn-admin layui-btn-normal" style="background-color: rgb(64,116,52)" data-type="merge">合并开票</button>
			</div>
			<!--数据表格-->
			<table id="finace_data_table" lay-filter="finace_data_table"></table>

			<!--表格按钮-->
			<script type="text/html" id="table-useradmin-webuser">
                {{#  if(d. fdata_statue== '发票申请中'){ }}
                <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="cancel_app"><i class="layui-icon layui-icon-delete"></i>撤回申请</a>
                {{#  } else if(d.fdata_statue== '已开票' || d.fdata_statue== '发票已寄出') { }}
                <a class="layui-btn layui-btn-xs" lay-event="cancel_back"><i class="layui-icon layui-icon-delete"></i>退票/改开</a>
                {{#  } }}
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



<from class="layui-form" lay-filter="invoice_add" id="invoice_add" style="display:none;padding: 20px;">
	<div class="layui-form-item">
        <label class="layui-form-label">报告编号</label>
        <div class="layui-input-inline" style="width: 30%;">
            <select id="fdata_repoid" name="fdata_repoid" lay-verify="required" lay-filter='fdata_repoid' lay-search >
                <option value="">请选择...</option>

            </select>
        </div>
        <label class="layui-form-label layui-required">出具日期</label>
        <div class="layui-input-inline" style="width: 45%;">
            <input type="text" name="fdata_cjrpotdate" id="fdata_cjrpotdate"  placeholder=""  class="layui-input layui-disabled" >
        </div>
	</div>

    <div class="layui-form-item">
        <label class="layui-form-label layui-required">项目类型</label>
        <div class="layui-input-inline" style="width: 30%;">
            <select id="fdata_report_type" name="fdata_report_type" lay-verify="required"  >
                <option value="">请选择...</option>
                <option value="住宅">住宅</option>
                <option value="土地">土地</option>
                <option value="资产">资产</option>
                <option value="快贷">快贷</option>
                <option value="工业">工业</option>
                <option value="在建工程">在建工程</option>
                <option value="商业">商业</option>
                <option value="划拨土地">划拨土地</option>
            </select>
        </div>
        <label class="layui-form-label layui-required">项目地址</label>
        <div class="layui-input-inline" style="width: 45%;">
            <input type="text" name="fdata_proj_name" id="fdata_proj_name"   placeholder="输入房产证上详细地址信息" lay-verify="required"  autocomplete="off" class="layui-input layui-disabled" >
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label layui-required">委托方</label>
        <div class="layui-input-inline" style="width: 30%;">
            <input type="text" name="fdata_entrust" id="fdata_entrust" lay-verify="required"   class="layui-input layui-disabled" >
        </div>
        <label class="layui-form-label layui-required ">评估额(万元)</label>
        <div class="layui-input-inline" style="width: 45%;">
            <input type="text" name="fdata_amount" id="fdata_amount" placeholder="万元"   lay-verify="required"   class="layui-input layui-disabled" >
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label layui-required" >应收评估费(元)</label>
        <div class="layui-input-inline" style="width: 30%;">
            <input type="text" name="fdata_evaluation" id="fdata_evaluation" placeholder="元"   lay-verify="required"   class="layui-input" >
        </div>
        <label class="layui-form-label layui-required">开票金额(元)</label>
        <div class="layui-input-inline" style="width: 45%;">
            <input type="text" name="fdata_invoice_money" placeholder="元"   lay-verify="required"   class="layui-input" >
        </div>
    </div>

    <div class="layui-form-item">
        <label class="layui-form-label layui-required">发票类型</label>
        <div class="layui-input-inline" style="width: 30%;">
            <input type="radio" name="fdata_invoice_type" lay-filter='fdata_invoice_type' value="1" title="普票">
            <input type="radio" name="fdata_invoice_type" lay-filter='fdata_invoice_type' value="2" title="专票" checked>
        </div>
        <label class="layui-form-label layui-required">开票名称</label>
        <div class="layui-input-inline" style="width: 45%;">
            <input type="text" name="fdata_invoice_name" id="fdata_invoice_name" class="layui-input" style="position:absolute;z-index:2;width:93%;" lay-verify="required"  autocomplete="off">
            <select id="invoice_name" name="invoice_name" lay-verify="required" lay-filter='invoice_name' lay-search>
                <option value="no">请选择...</option>
                <?php
                if ($account) {
                    foreach ($account as $row) {
                        ?>
                        <option
                                value="<?php echo $row['account_id']; ?>"><?php echo $row['account_invoice_name']; ?></option>
                        <?php
                    }
                } else {
                    ?>
                    <option value="no">请先至后台增加项目类型</option>
                    <?php
                }
                ?>
            </select>
        </div>
    </div>


	<div class="layui-form-item">
		<label class="layui-form-label layui-required">税号</label>
		<div class="layui-input-inline" style="width: 30%;">
            <input type="text" name="fdata_tax_num" id="fdata_tax_num"  class="layui-input" >
		</div>
		<label class="layui-form-label layui-required">开户行</label>
		<div class="layui-input-inline" style="width: 45%;">
            <input type="text" name="fdata_bank" id="fdata_bank"  class="layui-input" >
		</div>
	</div>
	<div class="layui-form-item">
		<label class="layui-form-label ">开户行电话</label>
		<div class="layui-input-inline" style="width: 30%;">
			<input type="text" name="fdata_bank_phone" id="fdata_bank_phone"  class="layui-input" >
		</div>
		<label class="layui-form-label layui-required">开户行地址</label>
		<div class="layui-input-inline" style="width: 45%;">
			<input type="text" name="fdata_bank_address" id="fdata_bank_address"   class="layui-input" >
		</div>
	</div>

	<div class="layui-form-item layui-form-text">
		<label class="layui-form-label">备注</label>
		<div class="layui-input-block">
			<textarea name="fdata_rete" placeholder="" class="layui-textarea"></textarea>
		</div>
	</div>
	<div class="layui-form-item layui-hide">
		<input type="button" lay-submit lay-filter="LAY-invoice-front-submit" id="LAY-invoice-front-submit" value="确认提交">
	</div>

</from>


<!--填写退票/改开信息-->
<from class="layui-form" lay-filter="invoice_cancel_update" id="invoice_cancel_update" style="display:none;padding: 20px;">
    <div class="layui-form-item">
        <label class="layui-form-label ">开票金额</label>
        <div class="layui-input-inline">
            <input type="text" name="fdata_invoice_money" placeholder="元" lay-verify="required" class="layui-input" >
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label ">开票名称</label>
        <div class="layui-input-inline">
            <input type="text" name="fdata_invoice_name" id="fdata_invoice_name" class="layui-input" lay-verify="required"  autocomplete="off">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label layui-required">税号</label>
        <div class="layui-input-inline">
            <input type="text" name="fdata_tax_num" id="fdata_tax_num" lay-verify="required"  class="layui-input" >
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label layui-required">开户行</label>
        <div class="layui-input-inline" style="width: 45%;">
            <input type="text" name="fdata_bank" id="fdata_bank" lay-verify="required"  class="layui-input" >
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label ">开户行电话</label>
        <div class="layui-input-inline" >
            <input type="text" name="fdata_bank_phone" id="fdata_bank_phone" lay-verify="required"  class="layui-input" >
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label layui-required">开户行地址</label>
        <div class="layui-input-inline" >
            <input type="text" name="fdata_bank_address" id="fdata_bank_address" lay-verify="required"  class="layui-input" >
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">退改理由</label>
        <div class="layui-input-block">
            <textarea name="fdata_refund_reason" lay-verify="required" class="layui-textarea"></textarea>
        </div>
    </div>
    <div class="layui-form-item layui-hide">
        <input type="button" lay-submit lay-filter="LAY-invoice-back-submit" id="LAY-invoice-back-submit" value="确认提交">
    </div>

</from>




</body>



<script src="../public/layui/layui.all.js"></script>

<script>
	var $ = layui.$;
	var g_account=<?php echo json_encode($account); ?>; //开票信息
	var g_proj;//项目信息
	var table = layui.table;
	var form = layui.form;
	var gb_index=5;//当前打开的dialog
    var checkedSet = new Set();
	var gb_seledata=[];//选中的记录
	var gb_total_count=0;
	var g_bdate,g_edate,g_kbdate,g_kedate="";
	var laydate = layui.laydate;
	var finace_data_table =table.render({
		elem: '#finace_data_table',　　　//html中table窗口的id
		height: 'full-220',
        id:'finace_data',
		url:'./Control_InvoiceApp/get_invoiceApp_data',
		loading: true,
		toolbar:true,
		text: {
			none: '空空如也'
		},
		title: '我申请的发票',
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
			,{field: 'fdata_statue',align:'center', title: '状态',width:150}
            ,{field: 'fdata_total_flag',align:'center',title: '合计标识',width:150,sort:true}
            ,{field: 'fdata_total_money',align:'center',title: '合计开票金额',width:200}
			,{field: 'fdata_repoid',align:'center', title: '报告编号',width:150}
			,{field: 'fdata_proj_name', align:'center',title: '项目名称',width:300}
			,{field: 'fdata_invoice_name',align:'center',title: '开票名称',width:200}
			,{field: 'fdata_invoice_money',align:'center',title: '开票金额',width:150}
            ,{field: 'fdata_invoice_type',align:'center',title: '发票类型',width:150,templet:'#fdata_invoice_type-Tpl'}
			,{field: 'fdata_express_company',align:'center', title: '快递公司',width:150}
			,{field: 'fdata_express_data',align:'center', title: '快递日期',width:150}
			,{field: 'fdata_express_num',align:'center', title: '快递号码',width:150}
			,{field: 'fdata_cjrpotdate', align:'center',title: '出具报告日期',width:150}
			,{field: 'fdata_report_type',align:'center',title: '报告类型',width:150}
			,{field: 'fdata_entrust',align:'center',title: '委托方',width:150}
			,{field: 'fdata_amount',align:'center',title: '评估额',width:150}
			,{field: 'fdata_evaluation',align:'center',title: '评估费',width:150}
			,{field: 'fdata_tax_num',align:'center',title: '税号',width:150}
			,{field: 'fdata_bank_address',align:'center',title: '开户行地址',width:150}
            ,{field: 'fdata_bank_phone',align:'center',title: '开户行电话',width:150}
            ,{field: 'fdata_bank',align:'center',title: '开户行',width:150}
            ,{field: 'fdata_invoice_emp',align:'center',title: '发票领用人',width:150}
            ,{field: 'fdata_invoice_data',align:'center',title: '开票日期',width:150}
            ,{field: 'fdata_money_data',align:'center',title: '收款日期',width:150}
            ,{field: 'fdata_refund_data',align:'center',title: '退票日期',width:150}
            ,{field: 'fdata_refund_reason',align:'center',title: '退改理由',width:250}
            ,{field: 'fdata_rete',align:'center',title: '备注',width:250}
            ,{title: '操作', align:'center', fixed: 'right', toolbar: '#table-useradmin-webuser',width:100}
		]]


	});//表格



    //绑定按钮事件
    $('.layui-btn.layui-btn-normal').on('click', function(){
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });

    //普票无需填写开户行地址等信息信息
    form.on('radio(fdata_invoice_type)', function(data){
            console.log(data.value); //被点击的radio的value值
            if(data.value==1)//被点击的radio的value值
            {
                $("#fdata_bank").hide();
                $("#fdata_bank_address").hide();
                $("#fdata_bank_phone").hide();

            }
            else
            {
                $("#fdata_bank").show();
                $("#fdata_bank_address").show();
                $("#fdata_bank_phone").show();
            }


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

    //开票名称选择后，同步更新到input，并且给开票其他信息赋值
    form.on('select(invoice_name)',function (obj){
        $("#fdata_invoice_name").val(obj.elem[obj.elem.selectedIndex].text);
        $("#invoice_name").next().find("dl").css({"display":"note"});

        if(obj.value=="no")
        {
            //清空开票其他信息
            $("#fdata_tax_num").val('');
            $("#fdata_bank").val('');
            $("#fdata_bank_phone").val('');
            $("#fdata_bank_address").val('');

        }
        else
        {
            if(g_account)
            {
                if(Array.isArray(g_account))
                {
                    g_account.forEach(function(val){
                        if(obj.value==val.account_id)
                        {
                            //赋值开票其他信息
                            $("#fdata_tax_num").val(val.account_tax_num);
                            $("#fdata_bank").val(val.account_bank_name);
                            $("#fdata_bank_phone").val(val.account_bank_phone);
                            $("#fdata_bank_address").val(val.account_bank_address);

                        }

                    });
                }
            }
        }

        form.render();

    });


    $("#fdata_invoice_name").on("input",function (){

        var value =$("#fdata_invoice_name").val();
        value=value.replace(/^\s*/,"");
        console.log(value);
        $("#invoice_name").val(value);
        form.render();
        $("#invoice_name").next().find(".layui-select-title input").click();
        var dl = $("#invoice_name").next().find("dl").children();
        var j = -1;
        for (var i =0;i<dl.length;i++) {
            if (dl[i].innerHTML.indexOf(value)<=-1){
                dl[i].style.display="none";
                j++
                if (j ==dl.length-1){
                    $("#mdd_select").next().find("dl").css({"display":"note"});
                }

            }
        }
    });


    //报告编号选择后
    form.on('select(fdata_repoid)',function (obj){
        console.log(obj.value);
        form.render();
        if(g_proj)
        {
            if(Array.isArray(g_proj))
            {
                g_proj.forEach(function(val){
                    if(obj.value==val.c_rpoid)
                    {
                        //赋值报告其他信息
                        $("#fdata_cjrpotdate").val(val.C_CJRPOTDATE);
                        $("#fdata_evaluation").val(val.c_evaluation);
                        $("#fdata_proj_name").val(val.C_PROJNAME);
                        $("#fdata_entrust").val(val.C_ENTRUST);
                        $("#fdata_amount").val(val.C_AMOUNT);

                    }

                });

            }
        }
        $.ajax({
            type:'post',
            url:'./Control_invoiceApp/count_total_price',
            data:{amount: $("#fdata_amount").val()},
            dataType:'json',
            async : false,
            success:function (result) {
                if(result){

                    $("#fdata_evaluation").val(result.charge);

                    //form.render();
                }

            }

        });



    });


    //监听搜索
    form.on('submit(LAY-invoice-search)', function(data){
        var field = data.field;
        field['bdd1']=g_bdate;
        field['edd1']=g_edate;
        field['kbdd1']=g_kbdate;
        field['kedd1']=g_kedate;
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
            $("#invoice_add").find('input[type=text],select,input[type=hidden]').each(function() {
                $(this).val('');
            });

            $.ajax({
                type:'post',
                url:'./Control_InvoiceApp/get_rpoid',
                dataType:'json',
                async : false,
                success:function (result) {
                    console.log(result);

                    if(Array.isArray(result))
                    {
                        g_proj=result;
                        $("#fdata_repoid").empty();
                        $('#fdata_repoid').append(new Option("", "请选择...."));
                        result.forEach(function (item){
                            $('#fdata_repoid').append(new Option(item.c_rpoid, item.c_rpoid));// 下拉菜单里添加元素
                        });
                        layui.form.render("select");

                    }


                }

            });

            layer.open({
                type: 1
                ,title: '申请发票'
                ,content: $("#invoice_add")
                ,maxmin: true
                ,area: ['1000px', '600px']
                ,btn: ['提交申请']
                ,anim: 3
                ,end:function () {
                    $("#invoice_add").find('input[type=text],select,input[type=hidden]').each(function() {
                        $(this).val('');
                    });

                }
                ,yes: function(index, layero){
                    var submit = $("#LAY-invoice-front-submit");
                    //监听提交
                    form.on('submit(LAY-invoice-front-submit)', function(data){

                        var field = data.field; //获取提交的字段
                        //提交 Ajax 成功后，静态更新表格中的数据
                        $.ajax({
                            type:'post',
                            url:'./Control_InvoiceApp/add_invoice_App',
                            data:{val:field},
                            dataType:'json',
                            async : false,
                            success:function (result) {
                                console.log(result);
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
        ,merge:function(){

            var checkStatus = table.checkStatus('finace_data'),data =checkStatus.data;
            console.log(checkStatus);
            if (checkStatus.data.length<1){
                layer.alert('请勾选要合并开票信息！');
                return false;
            }
            layer.msg("您要合并开票的项目数："+checkStatus.data.length+",合计开票金额："+gb_total_count+"元");
            $.ajax({
                type:'post',
                url:'./Control_InvoiceApp/merge_invoice_info',
                data:{val:checkStatus,money:gb_total_count},
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
        else if(obj.event === 'cancel_back'){
            var checkData=obj.data;
            if(checkData.length === 0){
                return layer.msg('请选择数据');
            }
            $("#invoice_cancel_update").find('input[type=text],select,input[type=hidden]').each(function() {
                $(this).val('');
            });
            form.val('invoice_cancel_update',checkData);
            layer.open({
                type: 1
                ,title: '退票/改开'
                ,content: $("#invoice_cancel_update")
                ,maxmin: true
                ,area: ['800px', '750px']
                ,btn: ['确定', '取消']
                ,yes: function(index, layero){
                    var submit = $("#LAY-invoice-back-submit");
                    //监听提交
                    form.on('submit(LAY-invoice-back-submit)', function(data){
                        var field = data.field; //获取提交的字段
                        var selectrow =Array();
                        selectrow[0]=obj.data;
                        console.log(selectrow);
                        //提交 Ajax 成功后，静态更新表格中的数据
                        $.ajax({
                            type:'post',
                            url:'./Control_Invoice/invoice_info_update',
                            data:{val:field,fdata:selectrow,type:4},
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
                ,success: function(layero, index){

                }
            });
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




