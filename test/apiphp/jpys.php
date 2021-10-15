<?php
header("Content-Type: text/html; charset=UTF-8");
libxml_use_internal_errors(true);

$typeid =$_GET["t"];
$page = $_GET["pg"];
$ids = $_GET["ids"];


$web='https://www.jpysvip.net';


//===============================================影视分类相关配置开始===========================

$movietype = '{"class":[{"type_id":1,"type_name":"电 影"},{"type_id":2,"type_name":"连续 剧"},{"type_id":3,"type_name":"综艺"},{"type_id":4,"type_name":"动漫"},{"type_id":13,"type_name":"国产剧"},{"type_id":14,"type_name":"香港剧"},{"type_id":15,"type_name":"韩国剧"},{"type_id":16,"type_name":"欧美剧"},{"type_id":20,"type_name":"日本剧"},{"type_id":21,"type_name":"台湾剧"},{"type_id":6,"type_name":"动作片"},{"type_id":7,"type_name":"喜剧片"},{"type_id":8,"type_name":"爱情片"},{"type_id":9,"type_name":"科幻片"},{"type_id":10,"type_name":"恐怖片"},{"type_id":11,"type_name":"剧情片"},{"type_id":12,"type_name":"战争片"}]}';
//===============================================影视分类相关配置结束===========================




//===============================================影视列表相关配置开始===========================

//取出影片ID的文本左边
$url1='/voddetail/';

//取出影片ID的文本右边
$url2='.html';

//xpath列表
$query='//ul[@class="myui-vodlist clearfix"]/li/div/a';

//取出影片的图片
$picAttr='data-original';

//取出影片的标题
$titleAttr='title';

//取出影片的链接
$linkAttr='href';


//影视更新情况 例如：更新至*集
$query2 = '//ul[@class="myui-vodlist clearfix"]/li/div/a/span[@class="pic-text text-right"]';

//影视列表链接 page=页码  typeid=类目ID
$liebiao='https://www.jpysvip.net/vodtype/typeid-page.html';

//每页多少个影片
$num=48;
//===============================================影视列表相关配置结束===========================







//===============================================影视详情相关配置开始===========================
//影片链接 vodid=影片ID
$detail='https://www.jpysvip.net/voddetail/vodid.html';

//影片名称
$vodtitle='//div[@class="myui-content__detail"]/h1';

//影片类型
$vodtype='//div[@class="myui-content__detail"]/p[1]/a[1]';

//播放地址名称
$playname='//ul[@class="nav nav-tabs active"]/li/a';

//播放地址
$playurl='//ul[@class="myui-content__list scrollbar sort-list clearfix"]';

//取出影片的全部播放链接
$linkAttr2='//*[@id="playlist数字"]/ul/li/a';

//取出影片图片
$vodimg='//a[@class="myui-vodlist__thumb picture"]/img/@data-original';


//取出影片简介
$vodtext='//*[@id="desc"]/div/div[2]/div/span[1]/text()';

//取出影片年份
$vodyear='//div[@class="myui-content__detail"]/p[1]/a[3]';


//===============================================影视详情相关配置结束===========================






if ($typeid<> null && $page<>null){
$liebiao=str_replace("typeid",$typeid,$liebiao);
$liebiao=str_replace("page",$page,$liebiao);
//读取影视列表
$html = curl_get($liebiao);
$dom = new DOMDocument();
$dom->loadHTML($html);
$dom->normalize();
$xpath = new DOMXPath($dom);
$texts = $xpath->query($query2);
$events = $xpath->query($query);

$length=$events->length;
if ($length<$num)
{
$page2=$page;
}else{
$length=$length+1;
$page2=$page + 1;
}
$result='{"code":1,"page":'.$page.',"pagecount":'. $page2 .',"total":'. $length.',"list":[';
for ($i = 0; $i < $events->length; $i++) {
    $event = $events->item($i);
    $text = $texts->item($i)->nodeValue;
    $link = $event->getAttribute($linkAttr);
    $title = $event->getAttribute($titleAttr);
    $pic = $event->getAttribute($picAttr);
    $link2 =getSubstr($link,$url1,$url2);

	if (substr($pic,0,4)<>'http'){
	$pic = $web.$pic;
	}

    $result=$result.'{"vod_id":'.$link2.',"vod_name":"'.$title.'","vod_pic":"'.$pic.'","type_id":'.$typeid.',"vod_remarks":"'.$text.'"},';
}

$result=substr($result, 0, strlen($result)-1).']}';
echo $result;
}else if ($ids<> null){
$detail=str_replace("vodid",$ids,$detail);
$html = curl_get($detail);
$dom = new DOMDocument();
$dom->loadHTML($html);
$dom->normalize();
$xpath = new DOMXPath($dom);
$texts = $xpath->query($vodtitle);
$text = $texts->item(0)->nodeValue;
$texts = $xpath->query($vodtype);
$type = $texts->item(0)->nodeValue;
$texts = $xpath->query($vodtext);
$vodtext2 = $texts->item(0)->nodeValue;
$texts = $xpath->query($vodyear);
$year = $texts->item(0)->nodeValue;
$texts = $xpath->query($vodimg);
$img = $texts->item(0)->nodeValue;
	if (substr($img,0,4)<>'http'){
	$img = $web.$img;
	}

$result='{"list":[{"vod_id":"'.$ids.'",';

$result=$result.'"vod_name":"'.$text.'",';

$result=$result.'"vod_pic":"'.$img.'",';

$result=$result.'"type_name":"'.$type.'",';

$result=$result.'"vod_year":"'.$year.'",';

$result=$result.'"vod_content":"'.$vodtext2.'",';

$yuan = '';
$dizhi = '';

$text1 = $xpath->query($playname);
$text2 = $xpath->query($playurl);
for ($i = 0; $i < $text2->length; $i++) {
    $event2 = $text2->item($i);
    $event1 = $text1->item($i);
    $bfyuan = $event1->nodeValue;

$yuan = $yuan.$bfyuan.'$$$';

$i2= $i+1;
$linkAttr3=str_replace("数字",$i2,$linkAttr2);

$link = $xpath->query($linkAttr3);

$dizhi2 = '';
for ($z = 0; $z < $link->length; $z++) {
    $text3 = $link->item($z);
    $text4 = $text3->nodeValue;
    $link4 = $text3->getAttribute($linkAttr);

	if (substr($link4,0,4)<>'http'){
	$link4 = $web.$link4;
	}
$dizhi2 = $dizhi2.$text4.'$'.$link4.'#';
}
$dizhi=$dizhi.substr($dizhi2, 0, strlen($dizhi2)-1).'$$$';
}

$result= $result.'"vod_play_from":"'.substr($yuan, 0, strlen($yuan)-3).'",';
$result= $result.'"vod_play_url":"'.substr($dizhi, 0, strlen($dizhi)-3).'"}]}';

echo $result;

}else{
echo $movietype;
}




function curl_get($url){
   $header = array(
       'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36',
    );
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_TIMEOUT, 15);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    $data = curl_exec($curl);
    if (curl_error($curl)) {
        return "Error: ".curl_error($curl);
    } else {
	curl_close($curl);
	return $data;
    }
}

function getSubstr($str, $leftStr, $rightStr) 
{
$left = strpos($str, $leftStr);
$right = strpos($str, $rightStr,$left);
if($left < 0 or $right < $left){
return '';
}
return substr($str, $left + strlen($leftStr),$right-$left-strlen($leftStr));

} 
?>
