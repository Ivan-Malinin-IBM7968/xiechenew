$(document).ready(function(){
	var brand_id = $("#brand_id").val();
	var series_id = $("#series_id").val();
	var model_id = $("#model_id").val();
	if (brand_id){
		$(".own_car").removeAttr("checked");
		$("#other_car").attr({ checked: "checked"});
		//$("#get_model").attr("disabled",true);
		$.ajax({
			type: "POST",
			url: "/index.php/carselect/ajax_get_carinfo",
			cache: false,
			data: "",
			success: function(data){
				if (data!='null'){
					var param = eval(data);
					$("#get_brand").html("");
					$("#get_brand").append("<option value='0'>请选择品牌</option>");
					for (i=0; i<param.length; i++ ){
						var selected = "";
						if(brand_id && param[i]['brand_id']==brand_id){
							selected = "selected";
						}
						$("#get_brand").append("<option value='"+param[i]['brand_id']+"' "+selected+">"+param[i]['brand_name']+"</option>");
					}
				}
			}
		})
	}
	var brand_id = $("#brand_id").val();
	var series_id = $("#series_id").val();
	var model_id = $("#model_id").val();
	if (brand_id){
		$(".own_car").removeAttr("checked");
		$("#other_car").attr({ checked: "checked"});
		$("#get_series").html("");
		//$("#get_series").attr("disabled",false);
		$("#get_model").html("");
		$("#get_model").append("<option value='0'>请选择车型</option>");
		//$("#get_model").attr("disabled",true);
		if(brand_id != '0'){
			$.ajax({
				type: "POST",
				url: "/index.php/carselect/ajax_get_carinfo",
				cache: false,
				data: "brand_id="+brand_id,
				success: function(data){
					if (data!='null'){
						var param = eval(data);
						$("#get_series").append("<option value='0'>请选择车系</option>");
						for (i=0; i<param.length; i++ ){
							var selected = "";
							if(series_id && param[i]['series_id']==series_id){
								selected = "selected";
							}
							$("#get_series").append("<option value='"+param[i]['series_id']+"' "+selected+">"+param[i]['series_name']+"</option>");
						}
					}
				}
			})
		}else{
			$("#get_series").append("<option value='0'>请选择车系</option>");
		}
	}
	if(series_id){
		$(".own_car").removeAttr("checked");
		$("#other_car").attr({ checked: "checked"});
		$("#get_model").html("");
		//$("#get_model").attr("disabled",false);
		if(series_id != '0'){
			$.ajax({
				type: "POST",
				url: "/index.php/carselect/ajax_get_carinfo",
				cache: false,
				data: "series_id="+series_id,
				success: function(data){
					if (data!='null'){
						var param = eval(data);
						$("#get_model").append("<option value='0'>请选择车型</option>");
						for (i=0; i<param.length; i++ ){
							var selected = "";
							if(model_id && param[i]['model_id']==model_id){
								selected = "selected";
							}
							$("#get_model").append("<option value='"+param[i]['model_id']+"' "+selected+">"+param[i]['model_name']+"</option>");
						}
					}
				}
			})
		}else{
			$("#get_model").append("<option value='0'>请选择车型</option>");
			//$("#get_model").attr("disabled",true);
		}
	}

	$("#get_brand").change(function(){
		$(".own_car").removeAttr("checked");
		$("#other_car").attr({ checked: "checked"});
		$("#get_series").html("");
		//$("#get_series").attr("disabled",false);
		$("#get_model").html("");
		$("#get_model").append("<option value='0'>请选择车型</option>");
		if($(this).val() != '0'){
			$.ajax({
				type: "POST",
				url: "/index.php/carselect/ajax_get_carinfo",
				cache: false,
				data: "brand_id="+$(this).val(),
				success: function(data){
					if (data!='null'){
						var param = eval(data);
						$("#get_series").append("<option value='0'>请选择车系</option>");
						for (i=0; i<param.length; i++ ){
							$("#get_series").append("<option value='"+param[i]['series_id']+"'>"+param[i]['series_name']+"</option>");
						}
					}
				}
			})
		}else{
			$("#get_series").html("");
			$("#get_series").append("<option value='0'>请选择车系</option>");
			//$("#get_series").attr("disabled",true);

			$("#get_model").html("");
			$("#get_model").append("<option value='0'>请选择车型</option>");
			//$("#get_model").attr("disabled",true);
		}
	})
	$("#get_series").change(function(){
		$(".own_car").removeAttr("checked");
		$("#other_car").attr({ checked: "checked"});
		$("#get_model").html("");
		//$("#get_model").attr("disabled",false);
		if($(this).val() != '0'){
			$.ajax({
				type: "POST",
				url: "/index.php/carselect/ajax_get_carinfo",
				cache: false,
				data: "series_id="+$(this).val(),
				success: function(data){
					if (data!='null'){
						var param = eval(data);
						$("#get_model").append("<option value='0'>请选择车型</option>");
						for (i=0; i<param.length; i++ ){
							$("#get_model").append("<option value='"+param[i]['model_id']+"'>"+param[i]['model_name']+"</option>");
						}
					}
				}
			})
		}else{
			$("#get_model").html("");
			$("#get_model").append("<option value='0'>请选择车系</option>");
			//$("#get_model").attr("disabled",true);
		}
	})
	$(".own_car").click(function(){
			$("#other_car").removeAttr("checked");
	})


	$("#other_car").click(function(){
		$(".own_car").removeAttr("checked");
	})


	$("#get_brand1").change(function(){
		$(".own_car").removeAttr("checked");
		$("#other_car").attr({ checked: "checked"});
		$("#get_series1").html("");
		$("#get_model1").html("");
		if($(this).val() != '0'){
			$.ajax({
				type: "POST",
				url: "/index.php/carselect/ajax_get_carinfo",
				cache: false,
				data: "brand_id="+$(this).val(),
				success: function(data){
					if (data!='null'){
						var param = eval(data);
						$("#get_series1").append("<option value='0'>请选择车系</option>");
						for (i=0; i<param.length; i++ ){
							$("#get_series1").append("<option value='"+param[i]['series_id']+"'>"+param[i]['series_name']+"</option>");
						}
					}
				}
			})
		}else{
			
		}
	})
	$("#get_series1").change(function(){
		$(".own_car").removeAttr("checked");
		$("#other_car").attr({ checked: "checked"});
		$("#get_model1").html("");
		if($(this).val() != '0'){
			$.ajax({
				type: "POST",
				url: "/index.php/carselect/ajax_get_carinfo",
				cache: false,
				data: "series_id="+$(this).val(),
				success: function(data){
					if (data!='null'){
						var param = eval(data);
						$("#get_model1").append("<option value='0'>请选择车型</option>");
						for (i=0; i<param.length; i++ ){
							$("#get_model1").append("<option value='"+param[i]['model_id']+"'>"+param[i]['model_name']+"</option>");
						}
					}
				}
			})
		}else{
			
		}
	})

})