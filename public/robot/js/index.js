const API="http://47.91.251.232:8892"
window.onload=getinfo()
var areaid=''
var tjnr=[]
function getinfo(){
	$.post(API+"/api/getArea",
	{
	}, function(response){
		var res=$.parseJSON( response )
		$.each(res.data,function(index,value){
			$("#area").append("<option value='"+value.id+"' name='area'>"+value.name+"</option>"); 
	   }); 
      });
}
function getcity(e){
	console.log(e)
	$.post(API+"/api/getArea",
	{
		prov_id:e
	}, function(response){
		var res=$.parseJSON( response )
		$.each(res.data,function(index,value){
			$("#city").append("<option value='"+value.id+"'>"+value.name+"</option>"); 
	   });
	   $('#city').muler({
		selectableOptgroup:true,
		afterSelect:function(val){
			let obj={'areaid':e,'cityid':val[0]}
			tjnr.push(obj)
			console.log(tjnr)
		},
		afterDeselect:function(val){
			console.log(val)
			tjnr.forEach((item,index)=>{
				if(item.cityid==val[0]){
					console.log(index)
					tjnr.splice(index,1)
					// delete arry[index]
				}
			})
			console.log(tjnr)
		}
	   });
	   $('#city').muler('refresh')
	   return false
      });
}
function goprint(){
	console.log(tjnr)
}
function cleararry(){
	tjnr=[];
	$('#city').muler('deselect_all');
	return false
}
// function selectall(){
// 	$('#city').muler('select_all');
// 	let obj={'areaid':e,'cityid':val[0]}
// 		tjnr.push(obj)
// 		return false
// }
$(function(){
	$("#area").change(function(){
		var id=$("#area").val()
		areaid=id
		$("#city").empty();
		getcity(id)
		
	})
})