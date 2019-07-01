<?php
header("Content-type:text/html;charset=utf-8");
//GET关键字
$kw=isset($_GET['kw'])?$_GET['kw']:null;
if(!$kw){
	exit();
}
//读取垃圾明细文本内容
$content = file_get_contents('./trashes.txt', FILE_USE_INCLUDE_PATH);
$content = iconv("gb2312", "utf-8//IGNORE",$content);

//提前定义4个垃圾分类
$type1="可回收Recyclable";
$type2="干垃圾Residual";
$type3="湿垃圾HouseholdFood";
$type4="有害垃圾Hamfulr";

//按行分割垃圾明细文本内容
$trashRow = explode("\n",$content);

//垃圾明细文本内容按行打ID标签，对应上面4个type
//数组trashAll存放所有垃圾（格式：id|垃圾名）
$trashAll=array();
$i=1;
foreach ($trashRow as $key => $row) {
	if(trim($row)){
		$expTrash=array();
		$expTrash=explode("|", $row);
		foreach ($expTrash as $key => $value) {
			$value=trim($value);
			$newValue=$i.'|'.$value;
			$exptrash[$key] =$newValue;
			array_push($trashAll, $newValue);
		}
		$i++;
	}
}

//find（所有垃圾，关键字，默认正向查找），返回id
function find($trashAll,$kw,$isForward=true){
	foreach ($trashAll as $key => $trash) {
		$expTrash=explode("|", $trash);
		if($expTrash){
			$id=$expTrash[0];
			$trashName=$expTrash[1];
			if($id&&$trashName){
				$isMatch=preg_match("/$kw/",$trashName,$match); 
				if($isForward==false){
					$isMatch= preg_match("/$trashName/",$kw,$match); 
				}
				if($isMatch){ 
					return $id;
				}
			}
		}
	}
}

//默认正向查找，未查找到则反向再次查找一遍
$findId=-1;
$findId=find($trashAll,$kw);
if(!$findId){
	$findId=find($trashAll,$kw,false);
}
if(!$findId||$findId<0){
	echo "others";
	exit();
}

//输出row.findId的typeName类型名称
$typeName=${'type'.$findId};
echo $typeName." (id=".$findId.")";
?>