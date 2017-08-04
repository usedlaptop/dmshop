<?php
include_once("./_dmshop.php");
echo "<meta http-equiv='content-type' content='text/html; charset=$shop[charset]'>";

$message = "";

if ($q) { $q = preg_match("/^[A-Za-z0-9_가-힣\x20]+$/", $q) ? $q : ""; }

if ($q) {

    // 도로명
    if ($dmshop['zipcode'] == 1) {

        $url = "http://zipcode.teraboard.net/?q=".urlencode($q);
        $url = shop_text($url);

        $data = array('id' => $id, 'q' => $q);

        while(list($n,$v) = each($data)){
            $send_data[] = "$n=$v";
        }
        $send_data = implode('&', $send_data);

        $url = parse_url($url);

        $host = $url['host'];
        $path = $url['path'];

        $fp = fsockopen($host, 80, $errno, $errstr, 5);
        if (!is_resource($fp)) {
            return false;
        }

        fputs($fp, "POST $path HTTP/1.1\r\n");
        fputs($fp, "Host: $host\r\n");
        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($fp, "Content-length: " . strlen($send_data) . "\r\n");
        fputs($fp, "Connection:close" . "\r\n\r\n");
        fputs($fp, $send_data);

        $result = '';
        while(!feof($fp)) {
            $result .= fgets($fp, 128);
        }
        fclose($fp);

        $result = explode("\r\n\r\n", $result, 2);

        $header = isset($result[0]) ? $result[0] : '';
        $content = isset($result[1]) ? $result[1] : '';

        //결과 출력
        //echo $header;
        //echo $content;

        preg_match_all("/<item>(.*)<\/item>/Uis", $content, $matches);

        $message = preg_match("/<message><!\[CDATA\[(.*)\]\]><\/message>/Uis", $content, $match);
        $message = trim($match[1]);

        unset($content);

        $n = 0;
        $list = array();
        for ($i=0; $i<count($matches[1]); $i++) {

            $zipcode = preg_match("/<zipcode><!\[CDATA\[(.*)\]\]><\/zipcode>/Uis", $matches[1][$i], $match);
            $zipcode = trim($match[1]);

            $addr = preg_match("/<addr><!\[CDATA\[(.*)\]\]><\/addr>/Uis", $matches[1][$i], $match);
            $addr = trim($match[1]);

            $addr2 = preg_match("/<addr2><!\[CDATA\[(.*)\]\]><\/addr2>/Uis", $matches[1][$i], $match);
            $addr2 = trim($match[1]);

            $building = preg_match("/<building><!\[CDATA\[(.*)\]\]><\/building>/Uis", $matches[1][$i], $match);
            $building = trim($match[1]);

            $list[$n]['zip1'] = shop_split("-", filter1($zipcode), 0);
            $list[$n]['zip2'] = shop_split("-", filter1($zipcode), 1);

            $list[$n]['addr'] = filter1($addr);
            $list[$n]['addr2'] = filter1($addr2);

            if ($building) {

                $list[$n]['addr'] .= " (".filter1($building).")";
                $list[$n]['addr2'] .= " (".filter1($building).")";

            }

            $list[$n]['building'] = filter1($building);


            $list[$n]['zipcode'] = $list[$n]['zip1'].$list[$n]['zip2'];
            $list[$n]['full_addr'] = $list[$n]['addr2'];
            $list[$n]['addr'] = $list[$n]['addr2'];

            $n++;

        }

    } else {
    // 지번

        $array = array();
        $list = array();
        $fp = fopen($shop['path']."/zip.db", "r");
        while(!feof($fp)) {
            $array[] = fgets($fp, 4096);
        }
        fclose($fp);

        $zip_count = 0;
        foreach($array as $data) {

            if (strstr($data,$q)) {

                $list[$zip_count]['zip1'] = substr($data,0,3);
                $list[$zip_count]['zip2'] = substr($data,3,3);
                $list[$zip_count]['zipcode'] = $list[$zip_count]['zip1'].$list[$zip_count]['zip2'];
    
                $addr_array = explode(' ', substr($data,7));
                if (substr($addr_array[sizeof($addr_array)-1],0,1) == '(' || intval(substr($addr_array[count($addr_array)-1],0,1))) {

                    $addr = trim(str_replace($addr_array[count($addr_array)-1], '', substr($data,7)));	

                } else {

                    $addr = trim(substr($data,7));

                }

                $list[$zip_count]['full_addr'] = trim(substr($data,7));
                $list[$zip_count]['addr'] = $addr;

                $zip_count++;

            }

        }

    }

}

// 타이틀 제목
$shop['title'] = $dmshop['shop_name']." - 우편번호찾기";
?>
<style type="text/css">
.zipbg {height:40px; background:url('<?=$shop['mobile_url']?>/img/bg.gif') repeat-x;}
.zipline {height:1px; background:url('<?=$shop['mobile_url']?>/img/line.gif') repeat-x;}

.t1 {font-weight:bold; line-height:37px; font-size:14px; color:#ffffff; font-family:gulim,굴림;}
.t2 {font-weight:bold; line-height:15px; font-size:13px; color:#808080; font-family:gulim,굴림;}
.t3 {line-height:16px; font-size:12px; color:#7d7d7d; font-family:gulim,굴림;}
.t4 {font-weight:bold; line-height:15px; font-size:13px; color:#333333; font-family:gulim,굴림;}
.t5 {text-align:center; line-height:33px; font-size:11px; color:#959595; font-family:gulim,굴림;}
.t6 {text-align:center; font-weight:bold; line-height:90px; font-size:13px; color:#808080; font-family:gulim,굴림;}
.zip {text-align:center; line-height:14px; font-size:11px; color:#7d7d7d; font-family:gulim,굴림;}
.addr {text-align:center; line-height:14px; font-size:12px; color:#000000; font-family:gulim,굴림;}
.addr:hover {color:#409dae;}

.not {text-align:center;}
.not {font-weight:bold; line-height:21px; font-size:13px; color:#808080; font-family:gulim,굴림;}

.input {height:20px; border:1px solid #bebebe; padding:0px 3px 0px 3px;}
.input {line-height:20px; font-size:12px; color:#000000; font-family:dotum,돋움;}
</style>
<div style="padding:0 15px;">
<div style="background-color:#ffffff; border:2px solid #1f1f22;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr class="zipbg">
    <td width="15"></td>
    <td width="6"><img src="<?=$shop['mobile_url']?>/img/arrow.gif" class="up2"></td>
    <td width="10"></td>
    <td class="t1">우편번호 찾기</td>
    <td width="60" align="center" class="t1" onclick="zipClose();">[닫기]</td>
</tr>
</table>

<div style="padding:10px; background-color:#ffffff;">
<table border="0" cellspacing="0" cellpadding="0">
<tr>
    <td><p class="t2">검색할 동 또는 읍/면/리를 입력하세요.</p><p class="t3">예:) 논현동, 교하읍, 영암리 (두글자 이상)</p></td>
</tr>
</table>

<table border="0" cellspacing="0" cellpadding="0">
<tr height="10"><td></td></tr>
</table>

<div style="padding:10px 10px; border:1px solid #e5e5e5; background-color:#f4f4f4; margin:0 auto;">
<table border="0" cellspacing="0" cellpadding="0" class="auto">
<tr>
    <td><input type="text" id="q" name="q" value="<?=$q?>" class="input" /></td>
    <td width="3"></td>
    <td><input type="button" value="검색" onclick="zipSearch('<?=$m?>');" /></td>
</tr>
</table>
</div>

<table border="0" cellspacing="0" cellpadding="0">
<tr height="50"><td></td></tr>
</table>

<table border="0" cellspacing="0" cellpadding="0">
<tr>
    <td class="t2">검색결과</td>
</tr>
</table>

<table border="0" cellspacing="0" cellpadding="0">
<tr height="10"><td></td></tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td height="2" bgcolor="#bbbbbb" class="none">&nbsp;</td></tr>
</table>

<? if ($q) { ?>
<? if (count($list)) { ?>
<table border="0" cellspacing="0" cellpadding="0">
<tr height="20"><td></td></tr>
</table>

<table border="0" cellspacing="0" cellpadding="0">
<? for ($i=0; $i<count($list); $i++) { ?>
<tr height="50">
    <td width="50" class="zip" valign="top"><?=$list[$i]['zipcode']?></td>
    <td valign="top"><a href="#" onclick="zipAdd('<?=$m?>', '<?=$list[$i]['zip1']?>', '<?=$list[$i]['zip2']?>', '<?=$list[$i]['addr']?>'); return false;" class="addr"><?=$list[$i]['full_addr']?></a></td>
</tr>
<? } ?>
</table>

<table border="0" cellspacing="0" cellpadding="0">
<tr height="20"><td></td></tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td class="zipline"></td></tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
    <td class="t5">검색결과에서 원하는 주소를 클릭하세요.</td>
</tr>
</table>
<? } else { ?>
<table border="0" cellspacing="0" cellpadding="0">
<tr height="30"><td></td></tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
    <td class="not">주소를 찾을 수 없습니다.<br>다시 한번 정확히 입력하세요.</td>
</tr>
</table>

<table border="0" cellspacing="0" cellpadding="0">
<tr height="30"><td></td></tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td class="line"></td></tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
    <td class="t5">검색결과에서 원하는 주소를 클릭하세요.</td>
</tr>
</table>
<? } ?>
<? } else { ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
    <td class="t6">검색하여주세요.</td>
</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td class="line"></td></tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td class="t5">검색결과에서 주소를 클릭하세요.</td></tr>
</table>
<? } ?>

<table border="0" cellspacing="0" cellpadding="0">
<tr height="20"><td></td></tr>
</table>

</div>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td height="1" bgcolor="#efefef" class="none">&nbsp;</td></tr>
<tr><td height="1" bgcolor="#e0e0e0" class="none">&nbsp;</td></tr>
<tr><td height="1" bgcolor="#ffffff" class="none">&nbsp;</td></tr>
</table>
</div>
</div>