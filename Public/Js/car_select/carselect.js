$(document).ready(function(){
		var brand_id = $("#default_brand").val();
		var series_id = $("#default_series").val();
		var model_id = $("#default_model").val();
		var brand_name = $("#default_brand").attr('brand_name');
		var series_name = $("#default_series").attr('series_name');
		var model_name = $("#default_model").attr('model_name');
		if(brand_id && series_id && model_id && brand_name && series_name && model_name){
			$("#get_brand").append("<option selected='selected' value='"+brand_id+"'>"+brand_name+"</option>");
			$("#get_series").append("<option selected='selected' value='"+series_id+"'>"+series_name+"</option>");
			$("#get_model").append("<option selected='selected' value='"+model_id+"'>"+model_name+"</option>");
		}
	});






	$("#get_brand").change(function(){
		$("#get_series").html("");
		if($(this).val() != '0'){
			$.ajax({
				type: "POST",
				url: "__APP__/carselect/ajax_get_carinfo",
				cache: false,
				data: "brand_id="+$(this).val(),
				success: function(data){
					if (data!='null'){
						var param = eval(data);
						//alert(data);
						for (i=0; i<param.length; i++ ){
							$("#get_series").append("<option value='"+param[i]['series_id']+"'>"+param[i]['series_name']+"</option>");
						}
					}
				}
			});
		}else{
			
		}
	});


		$("#get_series").change(function(){
		$("#get_model").html("");
		if($(this).val() != '0'){
			$.ajax({
				type: "POST",
				url: "__APP__/carselect/ajax_get_carinfo",
				cache: false,
				data: "series_id="+$(this).val(),
				success: function(data){
					if (data!='null'){
						var param = eval(data);
						//alert(data);
						for (i=0; i<param.length; i++ ){
							$("#get_model").append("<option value='"+param[i]['model_id']+"'>"+param[i]['model_name']+"</option>");
						}
					}
				}
			});
		}else{
			
		}
	});