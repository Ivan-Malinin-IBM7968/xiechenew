//输入价格的时候计算
function FillQuantity()
{
	var quantity = $("#quantity").val();
	var unit_price = $("#unit_price").val();
	var total_cost = $("#total_cost").val();

	if(quantity && unit_price && !total_cost)
	{
		var result=parseFloat(quantity) * parseFloat(unit_price);
		$("#total_cost").val(result.toFixed(2));
	}
	else if(quantity && !unit_price && total_cost)
	{
		if(quantity!=0)
		{
			var result=parseFloat(total_cost) / parseFloat(quantity);
			$("#unit_price").val(result.toFixed(2));
		}
	}
	else if(quantity && unit_price && total_cost)
	{
		var result=parseFloat(quantity) * parseFloat(unit_price);
		$("#total_cost").val(result.toFixed(2));

	}
}


function FillPrice()
{
	var quantity = $("#quantity").val();
	var unit_price = $("#unit_price").val();
	var total_cost = $("#total_cost").val();

	if(unit_price && quantity && !total_cost)
	{
		var result=parseFloat(quantity) * parseFloat(unit_price);
		$("#total_cost").val(result.toFixed(2));

	}
	else if(unit_price && !quantity && total_cost)
	{
		if(unit_price!=0)
		{
			var result=parseFloat(total_cost) / parseFloat(unit_price);
			$("#quantity").val(result.toFixed(2));

		}
	}
	else if(unit_price && quantity && total_cost)
	{
		if(quantity!=0){
			if(unit_price!=0){
				var result=parseFloat(quantity) * parseFloat(unit_price);
				$("#total_cost").val(result.toFixed(2));
			}else{
				var result=parseFloat(total_cost) / parseFloat(quantity);
				$("#unit_price").val(result.toFixed(2));
			}
		}else{
			var result=parseFloat(quantity) * parseFloat(unit_price);
			$("#total_cost").val(result.toFixed(2));
		}


	}
}

function FillTotal()
{
	var quantity = $("#quantity").val();
	var unit_price = $("#unit_price").val();
	var total_cost = $("#total_cost").val();

	if(total_cost && quantity && !unit_price)
	{
		if(quantity != 0)
		var result=parseFloat(total_cost) / parseFloat(quantity);
		$("#unit_price").val(result.toFixed(2));
	}
	else if(total_cost && !quantity && unit_price)
	{
		if(unit_price!=0)
		{
			var result=parseFloat(total_cost) / parseFloat(unit_price);
			$("#quantity").val(result.toFixed(2));
		}
	}
	else if(total_cost && quantity && unit_price)
	{
		if(unit_price != 0){
			var result=parseFloat(total_cost) / parseFloat(unit_price);
			$("#quantity").val(result.toFixed(2));
		}else if(quantity!=0){
			var result=parseFloat(total_cost) / parseFloat(quantity);
			$("#unit_price").val(result.toFixed(2));
		}

	}
}





//费用  显示保留两位小数
function checkMoneys(money,num){
	if(num==1){
		if (money.value == "0.00")
		{
			money.style.cssText = "text-align:right;color:black;";
			money.value = '';
		}
	}else if(num=2){
		if (!money.value)
		{
			money.style.cssText = "text-align:right;color:#CCCCCC";
			money.value = "0.00";
		}
	}
}
//检测费用格式
function CheckMoney(aMoney,aStyle){ 
 var money = aMoney.value;

  var myReg = /^[\.]{1,}$/;
  if(myReg.test(money))
  {
	  alert("输入格式错误!");
	  return ;
  }
 var oNumberObject = new Number(money);
 aMoney.value = oNumberObject.toFixed(2);
}



function createXMLHttpRequest(){
	if (window.ActiveXObject){
		xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");	
	}
	else if (window.XMLHttpRequest){
		xmlHttp = new XMLHttpRequest();	
	}
 }

 function handleStateChange(midClassDivId){
 
	if (xmlHttp.readyState == 4){
	//	alert(xmlHttp.readyState);
	  // alert(xmlHttp.status);
		if (xmlHttp.status == 200 || xmlHttp.status==0){
		
			var a = document.getElementById(midClassDivId);
			//alert(xmlHttp.responseText);
			a.innerHTML = xmlHttp.responseText;	
		}	
	}
 }

 




 function accountTotalAcceoryFee(){
	 //如果是手工输入了总费用就退出涵数
	  var input = document.getElementById("isManInput").value;
	  if (input)
	 	return;
	 
	 
	 var totalFee = 0;
	 var   TXT_AcceoryFee =  document.getElementById("TXT_AcceoryFee");
	 
	 var  TXT_ManFee =  document.getElementById("TXT_ManFee"); 
	
	 if (TXT_AcceoryFee.value && TXT_ManFee.value == "")
	 	 totalFee +=   parseFloat(TXT_AcceoryFee.value);
	 else if (TXT_ManFee.value && TXT_AcceoryFee.value == "")
		 totalFee +=   parseFloat(TXT_ManFee.value);
	 else
	 	totalFee =  parseFloat(TXT_AcceoryFee.value) + parseFloat(TXT_ManFee.value);
	 if (totalFee != "" && totalFee > 0)
	 {
	  	var oNumberObject = new Number(totalFee);
	   	document.getElementById("TXT_Total").value = oNumberObject.toFixed(2);
		document.getElementById("TXT_Total").style.cssText = "color:black;text-align:right; width:130px";
	 }
 }



 function accountTotalFee(){
	 //如果是手工输入了配件费就退出涵数
	 var inputTwo = document.getElementById("isAccesoryInput").value;
	 if (inputTwo)
	 	return;
	 
	 var totalFee = 0;//总费
	 var subCostFee = 0; //配件总费
	 var  TXT_ManFee =  document.getElementById("TXT_ManFee");
	 var  TXT_SubCosts =  document.getElementsByName("TXT_SubCost");
	 
	  for(var i=0;i<TXT_SubCosts.length;i++){
		    if (TXT_SubCosts[i].value)
			{
	  			subCostFee +=   parseFloat(TXT_SubCosts[i].value);
			}
	 }
	 
	 if (TXT_ManFee.value > 0 && TXT_ManFee.value != "" && subCostFee <= 0)
	 {
	 	  totalFee += parseFloat(TXT_ManFee.value); 
	 }
	 else if (TXT_ManFee.value == "" && subCostFee > 0)
	 {
		   totalFee +=  subCostFee;
	 }
	 else
	 {
	 	 totalFee = subCostFee + parseFloat(TXT_ManFee.value);
	 }
	 
	  
	 if (totalFee != "" && totalFee > 0)
	 {
		//如果没有手工输入总费用则自动更新总费用
		if (!document.getElementById("isManInput").value)
		{
			var oNumberObject = new Number(totalFee);
			document.getElementById("TXT_Total").value = oNumberObject.toFixed(2);
			document.getElementById("TXT_Total").style.cssText = "color:black;text-align:right;  width:130px";
		}
		var oNumberObject2 = new Number(subCostFee);
		document.getElementById("TXT_AcceoryFee").value =  oNumberObject2.toFixed(2);
		document.getElementById("TXT_AcceoryFee").style.cssText = "color:black;text-align:right;  width:130px";
	 }
 }

   function intelligenceTwo(){
		document.getElementById("isAccesoryInput").value = "fuck";
 }


function checkTotalFees(money,num){
	if(num==1){
		if (money.value == "0.00")
		{
			money.style.cssText = "text-align:right;color:black;width:100px;";
			money.value = '';
		}
	}else if(num=2){
		if (!money.value)
		{
			money.style.cssText = "text-align:right;color:#CCCCCC;width:100px;";
			money.value = "0.00";
		}
	}
}


