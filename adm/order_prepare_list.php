<?php
include_once("./_dmshop.php");
if ($date1) { $date1 = trim($date1); $date1 = preg_match("/^[0-9\-]+$/", $date1) ? $date1 : ""; }
if ($date2) { $date2 = trim($date2); $date2 = preg_match("/^[0-9\-]+$/", $date2) ? $date2 : ""; }
if ($order_type) { $order_type = preg_match("/^[0-9]+$/", $order_type) ? $order_type : ""; }
if ($order_pay_type) { $order_pay_type = preg_match("/^[0-9]+$/", $order_pay_type) ? $order_pay_type : ""; }
$top_id = "2";
$left_id = "2";
$menu_id = "201";
$shop['title'] = "배송 준비";
include_once("./_top.php");

$colspan = "21";

/*--------------------------------
    ## 날짜 ##
--------------------------------*/

// 어제
$search_date1 = date("Y-m-d", $shop['server_time'] - (1 * 86400));

// 일주일
$search_date2 = date("Y-m-d", $shop['server_time'] - (7 * 86400));

// 이번달 1일
$search_date3 = date("Y-m-d", strtotime(date("Y-m", $shop['server_time'])."-01"));

// 지난달 1일
$search_date4 = date("Y-m-d", strtotime(date("Y-m", strtotime(date("Y-m", $shop['server_time'])."-01") - (86400 * 1))."-01"));

// 지난달 마지막일
$search_date5 = date("Y-m-d", strtotime(date("Y-m", $shop['server_time'])."-01") - (86400 * 1));

// 전체 (기본)
$search_date_default1 = "2012-01-01";
$search_date_default2 = $shop['time_ymd'];

/*--------------------------------
    ## 주문 ##
--------------------------------*/

// 검색조건
$sql_search = " where order_payment != '0' and order_number = '0' ";

if ($f && $q) {

    $sql_search .= " and INSTR(".$f.", '".$q."') ";

}

if (!$date1 || !$date2) {

    $date1 = $search_date_default1;
    $date2 = $search_date_default2;

}

if ($date1 && $date2) {

    $sql_search .= " and substring(order_datetime,1,10) >= '".$date1."' and substring(order_datetime,1,10) <= '".$date2."' ";

}

if ($order_type == '101200') {

    $sql_search .= " and order_type in ('101','200') ";

} else {

    if ($order_type) {

        $sql_search .= " and order_type = '".$order_type."' ";

    }

}

if ($order_pay_type) {

    $sql_search .= " and order_pay_type = '".$order_pay_type."' ";

}

$cnt = sql_fetch(" select count(*) as cnt from $shop[order_table] $sql_search ");

$total_count = $cnt['cnt'];

if (!$rows) {

    $rows = 20;

}

$total_page  = ceil($total_count / $rows);

if (!$page) {

    $page = 1;

}

$from_record = ($page - 1) * $rows;

$shop_pages = shop_paging_v0("10", $page, $total_page, "?date1=".$date1."&date2=".$date2."&order_type=".$order_type."&order_pay_type=".$order_pay_type."&sort=".$sort."&rows=".$rows."&f=".$f."&q=".$q."&page=");

if (!$sort) {

    $sort = "order_datetime desc";

}

$result = sql_query(" select * from $shop[order_table] $sql_search order by $sort limit $from_record, $rows ");

if (!$q) {

    $keyword = "검색어";
    $q = "검색어";

}
?>
<style type="text/css">
.contents_box {min-width:1100px;}

.category1 .select {line-height: 1.5; font-size:12px; color:#000000; font-family:"돋움",Dotum,Helvetica,AppleGothic,Sans-serif;}
.category1 .selectBox-dropdown {width:100px; height:19px;}
.category1 .selectBox-dropdown .selectBox-label {padding:1px 5px 1px 5px;}

.category2 .select {line-height: 1.5; font-size:12px; color:#000000; font-family:"돋움",Dotum,Helvetica,AppleGothic,Sans-serif;}
.category2 .selectBox-dropdown {width:100px; height:19px;}
.category2 .selectBox-dropdown .selectBox-label {padding:1px 5px 1px 5px;}

.sort .select {line-height: 1.5; font-size:12px; color:#000000; font-family:"돋움",Dotum,Helvetica,AppleGothic,Sans-serif;}
.sort .selectBox-dropdown {width:110px; height:19px;}
.sort .selectBox-dropdown .selectBox-label {padding:1px 5px 1px 5px;}

.limit .select {line-height: 1.5; font-size:12px; color:#000000; font-family:"돋움",Dotum,Helvetica,AppleGothic,Sans-serif;}
.limit .selectBox-dropdown {width:35px; height:19px;}
.limit .selectBox-dropdown .selectBox-label {padding:1px 5px 1px 5px;}

.field .select {line-height: 1.5; font-size:12px; color:#000000; font-family:"돋움",Dotum,Helvetica,AppleGothic,Sans-serif;}
.field .selectBox-dropdown {width:80px; height:19px;}
.field .selectBox-dropdown .selectBox-label {padding:1px 5px 1px 5px;}
</style>

<script type="text/javascript">
$(document).ready( function() {
    $(".category1 select").selectBox();
    $(".category2 select").selectBox();
    $(".sort select").selectBox();
    $(".limit select").selectBox();
    $(".field select").selectBox();
});
</script>

<script type="text/javascript" src="<?=$shop['path']?>/js/userview.js"></script>

<script type="text/javascript">
function checkAll(mode)
{

    $('.form_list .chk_id input').attr('checked', mode);

}

function checkConfirm(msg)
{

    var n = $('.form_list .chk_id input:checked').length;

    if (n <= '0') {

        alert(msg + "할 내역을 선택하세요.");
        return false;

    }

    return true;

}

function checkOk()
{

    var msg = "배송준비";
    if (!checkConfirm(msg)) {

        return false;

    }

    var f = document.formList;

    f.m.value = "check_ok";

    if (!confirm("선택한 내역을 배송준비로 변경하시겠습니까?\n\n결제완료 상태의 주문내역만 변경됩니다.")) {

        return false;

    }

    f.action = "./order_prepare_list_update.php";
    f.submit();

}

function checkCancel()
{

    var msg = "배송준비취소";
    if (!checkConfirm(msg)) {

        return false;

    }

    var f = document.formList;

    f.m.value = "check_cancel";

    if (!confirm("선택한 내역을 배송준비취소 하시겠습니까?\n\n배송준비 상태의 주문내역만 변경됩니다.")) {

        return false;

    }

    f.action = "./order_prepare_list_update.php";
    f.submit();

}

function checkOrderCancel()
{

    var msg = "주문취소";
    if (!checkConfirm(msg)) {

        return false;

    }

    var f = document.formList;

    f.m.value = "check_order_cancel_ok";

    if (!confirm("선택한 내역을 주문취소 하시겠습니까?\n\n주문취소한 내역은 복구가 불가합니다.")) {

        return false;

    }

    f.action = "./order_prepare_list_update.php";
    f.submit();

}

function checkExcel()
{

    var msg = "액셀생성";
    if (!checkConfirm(msg)) {

        return false;

    }

    var f = document.formList;

    f.m.value = "check_prepare";

    if (!confirm("선택한 내역을 액셀생성 하시겠습니까?")) {

        return false;

    }

    f.action = "./order_excel.php";
    f.submit();

}

function listUpdate(m, order_code)
{

    var f = document.formUpdate;

    f.m.value = m;
    f.order_code.value = order_code;

    if (m == 'order_cancel_ok') {

        var message = "해당 내역을 주문취소하시겠습니까?\n\n주문취소한 내역은 복구할 수 없습니다.";

    } else {

        var message = "배송준비 상태로 변경 하시겠습니까?\n\n결제완료 상태의 주문내역만 변경됩니다.";

    }

    if (!confirm(message)) {

        return false;

    }

    f.action = "./order_prepare_list_update.php";
    f.submit();

}

function listSearch(mode)
{

    var f = document.formSearch;

    if (f.q.value == '<?=text($keyword)?>') {

        f.q.value = "";

    }

    if (mode == 'sort') {

        f.submit();

    } else {

        if (!f.q.value) {

            alert("검색어를 입력하십시오."); 
            f.q.focus();
            return false;

        }

    }

}

function listDate(mode)
{

    var f = document.formSearch;

    if (mode == '1') {

        f.date1.value = "<?=$shop['time_ymd']?>";
        f.date2.value = "<?=$shop['time_ymd']?>";

    }

    if (mode == '2') {

        f.date1.value = "<?=$search_date1?>";
        f.date2.value = "<?=$search_date1?>";

    }

    if (mode == '3') {

        f.date1.value = "<?=$search_date2?>";
        f.date2.value = "<?=$shop['time_ymd']?>";

    }

    if (mode == '4') {

        f.date1.value = "<?=$search_date3?>";
        f.date2.value = "<?=$shop['time_ymd']?>";

    }

    if (mode == '5') {

        f.date1.value = "<?=$search_date4?>";
        f.date2.value = "<?=$search_date5?>";

    }

    if (mode == '6') {

        f.date1.value = "<?=$search_date_default1?>";
        f.date2.value = "<?=$search_date_default2?>";

    }

    listSearch('sort');

}

function keywordOver()
{

    var f = document.formSearch;

    if (f.q.value == '<?=text($keyword)?>') {

        f.q.value = "";

    }

}

function formAdd(id1, id2, val)
{

    if (document.getElementById(id1).checked == true) {

        document.getElementById(id2).value = val;

    } else {

        document.getElementById(id2).value = "";

    }

}

function formAddTime(id1, id2)
{

    var date = new Date();

    var Year = date.getFullYear();
    var Month = date.getMonth() + 1;
    var Day = date.getDate();
    var Hour = date.getHours();
    var Min = date.getMinutes();
    var Sec = date.getSeconds();

    if (Month < 10) {

        var Month = "0"+Month;

    }

    if (Day < 10) {

        var Day = "0"+Day;

    }

    if (Hour < 10) {

        var Hour = "0"+Hour;

    }

    if (Min < 10) {

        var Min = "0"+Min;

    }

    if (Sec < 10) {

        var Sec = "0"+Sec;

    }

    var time_view = Year+"-"+Month+"-"+Day+" "+Hour+":"+Min+":"+Sec;

    if (document.getElementById(id1).checked == true) {

        document.getElementById(id2).value = time_view;

    } else {

        document.getElementById(id2).value = "";

    }

}
</script>

<form method="post" name="formUpdate" autocomplete="off">
<input type="hidden" name="form_check" value="<?=$dmshop_user['datetime']?>" />
<input type="hidden" name="url" value="<?=$urlencode?>" />
<input type="hidden" name="m" value="" />
<input type="hidden" name="order_code" value="" />
</form>

<div class="contents_box">
<form method="get" name="formSearch" action="order_prepare_list.php" onSubmit="return listSearch('');" autocomplete="off">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr height="39" bgcolor="#f5f5f5">
    <td>
<table border="0" cellspacing="0" cellpadding="0">
<tr>
    <td width="20"></td>
    <td class="tx1">주문상태별 보기</td>
    <td width="10"></td>
    <td class="category1">
<select id="order_type" name="order_type" size="1" class="select" onchange="listSearch('sort');">
    <option value="101"><?=shop_order_type("101");?></option>
    <option value="200"><?=shop_order_type("200");?></option>
    <option value="101200"><?=shop_order_type("101");?>+<?=shop_order_type("200");?></option>
</select>

<script type="text/javascript">
$("#order_type").val("<?=$order_type?>");
</script>
    </td>
    <td width="4"></td>
    <td class="category2">
<select id="order_pay_type" name="order_pay_type" size="1" class="select" onchange="listSearch('sort');">
    <option value="">결제수단 전체</option>
    <option value="1"><?=shop_pay_name("1");?></option>
    <option value="2"><?=shop_pay_name("2");?></option>
    <option value="3"><?=shop_pay_name("3");?></option>
    <option value="4"><?=shop_pay_name("4");?></option>
    <option value="5"><?=shop_pay_name("5");?></option>
    <option value="6"><?=shop_pay_name("6");?></option>
</select>

<script type="text/javascript">
$("#order_pay_type").val("<?=$order_pay_type?>");
</script>
    </td>
    <td width="20"></td>
    <td class="tx1">기간별 조회 :</td>
    <td width="5"></td>
    <td><input type="text" id="date1" name="date1" value="<?=$date1?>" onFocus="shopfocusIn4(this);" onBlur="shopfocusOut4(this);" class="input4" /></td>
    <td width="2"></td>
    <td><a href="#" onclick="shopDate('date1'); return false;"><img src="<?=$shop['image_path']?>/adm/calendar.gif" border="0"></a></td>
    <td width="16" align="center" class="tx1">~</td>
    <td><input type="text" id="date2" name="date2" value="<?=$date2?>" onFocus="shopfocusIn4(this);" onBlur="shopfocusOut4(this);" class="input4" /></td>
    <td width="2"></td>
    <td><a href="#" onclick="shopDate('date2'); return false;"><img src="<?=$shop['image_path']?>/adm/calendar.gif" border="0"></a></td>
    <td width="5"></td>
    <td><a href="#" onclick="listSearch('sort'); return false;"><img src="<?=$shop['image_path']?>/adm/search3.gif" border="0"></a></td>
    <td width="5"></td>
    <td><a href="#" onclick="listDate('1'); return false;"><img src="<?=$shop['image_path']?>/adm/btn_date1.gif" border="0"></a></td>
    <td><a href="#" onclick="listDate('2'); return false;"><img src="<?=$shop['image_path']?>/adm/btn_date2.gif" border="0"></a></td>
    <td width="1"></td>
    <td><a href="#" onclick="listDate('3'); return false;"><img src="<?=$shop['image_path']?>/adm/btn_date3.gif" border="0"></a></td>
    <td width="1"></td>
    <td><a href="#" onclick="listDate('4'); return false;"><img src="<?=$shop['image_path']?>/adm/btn_date4.gif" border="0"></a></td>
    <td><a href="#" onclick="listDate('5'); return false;"><img src="<?=$shop['image_path']?>/adm/btn_date5.gif" border="0"></a></td>
    <td width="1"></td>
    <td><a href="#" onclick="listDate('6'); return false;"><img src="<?=$shop['image_path']?>/adm/btn_date6.gif" border="0"></a></td>
</tr>
</table>
    </td>
    <td width="20"></td>
</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td height="1" bgcolor="#e4e4e4" class="none">&nbsp;</td></tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="list_title_bg">
<tr>
    <td>
<table border="0" cellspacing="0" cellpadding="0">
<tr>
    <td width="20"></td>
    <td class="listname">주문내역</td>
    <td width="10"></td>
    <td><span class="tx1">총</span> <span class="totalnum" id="total_count"><?=(int)($total_count);?></span> <span class="tx1">건</span></td>
    <td width="13" class="line">|</td>
    <td class="tx1">정렬방식</td>
    <td width="10"></td>
    <td class="sort">
<select id="sort" name="sort" class="select" onchange="listSearch('sort');">
    <option value="order_datetime desc">주문일시 내림차순</option>
    <option value="order_datetime asc">주문일시 오름차순</option>
    <option value="order_name asc">주문자명 내림차순</option>
    <option value="order_name desc">주문자명 오름차순</option>
</select>
    </td>
    <td width="4"></td>
    <td class="limit">
<select id="rows" name="rows" class="select" onchange="listSearch('sort');">
    <option value="20">20개씩</option>
    <option value="40">40개씩</option>
    <option value="80">80개씩</option>
    <option value="100">100개씩</option>
</select>
    </td>
</tr>
</table>
    </td>
    <td width="400" align="right">
<table border="0" cellspacing="0" cellpadding="0">
<tr>
    <td class="field">
<select id="f" name="f" class="select">
    <option value="order_name">주문자명</option>
    <option value="order_code">주문번호</option>
    <option value="user_id">주문자ID</option>
    <option value="order_rec_name">수령자명</option>
    <option value="order_dep_name">입금자명</option>
    <option value="item_title">주문상품명</option>
    <option value="order_tel">주문자 전화</option>
    <option value="order_hp">주문자 휴대폰</option>
    <option value="order_rec_tel">수령자 전화</option>
    <option value="order_rec_hp">수령자 휴대폰</option>
</select>
    </td>
    <td width="4"></td>
    <td><input type="text" name="q" value="<?=$q?>" onmouseover="keywordOver();" onFocus="shopInfocus1_1(this); keywordOver();" onBlur="shopOutfocus1_1(this);" class="input3" /></td>
    <td width="3"></td>
    <td><input type="image" src="<?=$shop['image_path']?>/adm/search.gif" border="0"></td>
    <td width="4"></td>
    <td><a href="./order_prepare_list.php?order_type=101200"><img src="<?=$shop['image_path']?>/adm/reset.gif" border="0"></a></td>
    <td width="20"></td>
</tr>
</table>
    </td>
</tr>
</table>
</form>

<script type="text/javascript">
<? if ($sort) { ?>$("#sort").val("<?=$sort?>");<? } ?>
<? if ($rows) { ?>$("#rows").val("<?=$rows?>");<? } ?>
<? if ($f) { ?>$("#f").val("<?=$f?>");<? } ?>
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td height="1" bgcolor="#e4e4e4" class="none">&nbsp;</td></tr>
</table>

<form method="post" name="formList" autocomplete="off">
<input type="hidden" name="form_check" value="<?=$dmshop_user['datetime']?>" />
<input type="hidden" name="url" value="<?=$urlencode?>" />
<input type="hidden" name="m" value="" />
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
<colgroup>
    <col width="20">
    <col width="25">
    <col width="1">
    <col width="90">
    <col width="1">
    <col width="100">
    <col width="1">
    <col width="80">
    <col width="1">
    <col width="80">
    <col width="1">
    <col width="">
    <col width="1">
    <col width="80">
    <col width="1">
    <col width="80">
    <col width="1">
    <col width="80">
    <col width="1">
    <col width="231">
    <col width="20">
</colgroup>
<tr height="1">
    <td></td>
    <td></td>
    <td class="bc1"></td>
    <td></td>
    <td class="bc1"></td>
    <td></td>
    <td class="bc1"></td>
    <td></td>
    <td class="bc1"></td>
    <td></td>
    <td class="bc1"></td>
    <td></td>
    <td class="bc1"></td>
    <td></td>
    <td class="bc1"></td>
    <td></td>
    <td class="bc1"></td>
    <td></td>
    <td class="bc1"></td>
    <td></td>
    <td></td>
</tr>
<tr height="50" bgcolor="#f5f5f5">
    <td></td>
    <td><input type="checkbox" onclick="if (this.checked) checkAll(true); else checkAll(false);" class="checkbox" /></td>
    <td class="bc1"></td>
    <td class="boxtitle">주문일시</td>
    <td class="bc1"></td>
    <td class="boxtitle">주문번호</td>
    <td class="bc1"></td>
    <td class="boxtitle">주문자명<br>(수령자명)</td>
    <td class="bc1"></td>
    <td class="boxtitle">주문상태</td>
    <td class="bc1"></td>
    <td class="boxtitle">주문상품 (주문옵션)</td>
    <td class="bc1"></td>
    <td class="boxtitle">주문금액</td>
    <td class="bc1"></td>
    <td class="boxtitle">주문수량</td>
    <td class="bc1"></td>
    <td class="boxtitle">재고수량</td>
    <td class="bc1"></td>
    <td class="boxtitle">개별설정</td>
    <td></td>
</tr>
<tr><td colspan="<?=$colspan?>" height="1" class="bc1"></td></tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff" class="form_list">
<colgroup>
    <col width="20">
    <col width="25">
    <col width="1">
    <col width="90">
    <col width="1">
    <col width="100">
    <col width="1">
    <col width="80">
    <col width="1">
    <col width="80">
    <col width="1">
    <col width="">
    <col width="1">
    <col width="80">
    <col width="1">
    <col width="80">
    <col width="1">
    <col width="80">
    <col width="1">
    <col width="231">
    <col width="20">
</colgroup>
<?
for ($i=0; $list=sql_fetch_array($result); $i++) {

    // user
    $user = shop_user($list['user_id']);

    if ($user['id']) {

        $userview = shop_userview($user['user_id'], $user['user_name'], $user['user_email'], $user['user_homepage'], $list['order_name']);

    } else {

        $userview = shop_userview("", $dmshop_order['order_name'], $dmshop_order['order_email'], "", $list['order_name']);

    }
?>
<input type="hidden" name="order_code[<?=$i?>]" value="<?=$list['order_code']?>" />
<tr height="50" class="list_bg<?=$i%2?>">
    <td></td>
    <td class="chk_id"><input type="checkbox" name="chk_id[]" value="<?=$i?>" class="checkbox" /></td>
    <td class="bc1"></td>
    <td align="center"><p class="datetime1"><?=date("Y-m-d", strtotime($list['order_datetime']));?></p><p class="datetime2"><?=date("H시 : i분", strtotime($list['order_datetime']));?></p></td>
    <td class="bc1"></td>
    <td align="center"><a href="#" onclick="orderPopupDetail('<?=$list['order_code']?>'); return false;" class="order_code"><?=$list['order_code']?></a></td>
    <td class="bc1"></td>
    <td align="center"><? if ($list['order_name'] != $list['order_rec_name']) { echo "<p class='order_name'>".$userview."</p><p class='order_rec_name'>(".text($list['order_rec_name']).")</p>"; } else { echo "<p class='order_name'>".$userview."</p>"; } ?></td>
    <td class="bc1"></td>
    <td align="center"><span class="order_type_<?=$list['order_type']?>"><?=shop_order_type($list['order_type']);?></span></td>
    <td class="bc1"></td>
    <td colspan="7">
<?
if ($list['order_count'] == '1') {

    // 상품
    $dmshop_item = shop_item($list['item_id']);

    // 옵션 초기화
    unset($dmshop_item_option);

    // 옵션
    if ($list['option_id']) {

        // 상품 옵션
        $dmshop_item_option = shop_item_option($list['option_id']);

        // 수량
        $list['item_limit'] = $dmshop_item_option['option_limit'];

    } else {
    // 일반

        // 상품정보가 있을 때
        if ($dmshop_item['id']) {

            // 수량
            $list['item_limit'] = $dmshop_item['item_limit'];

        } else {
        // 없다면

            // 수량 제로
            $list['item_limit'] = "0";

        }

    }
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr height="50">
    <td onclick="orderManage('', '<?=$list['order_code']?>'); return false;" class="pointer"><p style="margin-left:15px;"><a href="<?=$shop['path']?>/item.php?id=<?=$list['item_code']?>" target="_blank"><span class="item_title"><?=text($list['item_title'])?></span><? if ($list['option_name']) { ?><span class="option_name"> (옵션 : <?=text($list['option_name'])?>)</span><? } ?></a></p></td>
    <td width="1" class="bc1"></td>
    <td width="80" align="center" class="order_limit"><?=number_format($list['order_item_money']);?></td>
    <td width="1" class="bc1"></td>
    <td width="80" align="center" class="order_limit"><?=number_format($list['order_limit']);?></td>
    <td width="1" class="bc1"></td>
    <td width="80" align="center" class="item_limit"><?=number_format($list['item_limit']);?></td>
</tr>
</table>
<?
} else {

$result2 = sql_query(" select * from $shop[order_table] where order_code = '".$list['order_code']."' order by order_number asc ");
for ($k=0; $row=sql_fetch_array($result2); $k++) {

    // 상품
    $dmshop_item = shop_item($row['item_id']);

    // 옵션 초기화
    unset($dmshop_item_option);

    // 옵션
    if ($row['option_id']) {

        // 상품 옵션
        $dmshop_item_option = shop_item_option($row['option_id']);

        // 수량
        $row['item_limit'] = $dmshop_item_option['option_limit'];

    } else {
    // 일반

        // 상품정보가 있을 때
        if ($dmshop_item['id']) {

            // 수량
            $row['item_limit'] = $dmshop_item['item_limit'];

        } else {
        // 없다면

            // 수량 제로
            $row['item_limit'] = "0";

        }

    }
?>
<? if ($k > '0') { ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td class="line2" height="1"></td></tr>
</table>
<? } ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr height="50">
    <td onclick="orderManage('', '<?=$list['order_code']?>'); return false;" class="pointer"><p style="margin-left:15px;"><a href="<?=$shop['path']?>/item.php?id=<?=$row['item_code']?>" target="_blank"><span class="item_title"><?=text($row['item_title'])?></span><? if ($row['option_name']) { ?><span class="option_name"> (옵션 : <?=text($row['option_name'])?>)</span><? } ?></a></p></td>
    <td width="1" class="bc1"></td>
    <td width="80" align="center" class="order_limit"><?=number_format($row['order_item_money']);?></td>
    <td width="1" class="bc1"></td>
    <td width="80" align="center" class="order_limit"><?=number_format($row['order_limit']);?></td>
    <td width="1" class="bc1"></td>
    <td width="80" align="center" class="item_limit"><?=number_format($row['item_limit']);?></td>
</tr>
</table>
<?
}

}
?>
    </td>
    <td class="bc1"></td>
    <td align="left" class="none">
<table border="0" cellspacing="0" cellpadding="0">
<tr>
    <td width="10"></td>
<?
echo "<td><a href='#' onclick=\"orderManage('', '".$list['order_code']."'); return false;\"><img src='".$shop['image_path']."/adm/list_option.gif' border='0'></a></td>";

// 취소, 교환, 환불이 완료된 내역이 아닐 때
if ($list['order_cancel'] != '2' && $list['order_exchange'] != '2' && $list['order_refund'] != '2') {

    // 결제완료, 배송준비단계가 아닌 것
    if ($list['order_payment'] == '2' && $list['order_delivery'] == '0') {

        echo "<td width='4'></td><td><a href='#' onclick=\"listUpdate('ok', '".$list['order_code']."'); return false;\"><img src='".$shop['image_path']."/adm/list_prepare.gif' border='0'></a></td>";
        echo "<td width='4'></td><td><a href='#' onclick=\"listUpdate('order_cancel_ok', '".$list['order_code']."'); return false;\"><img src='".$shop['image_path']."/adm/list_cancel_ok.gif' border='0'></a></td>";

    }

}
?>
</tr>
</table>
    </td>
    <td></td>
</tr>
<tr><td colspan="<?=$colspan?>" height="1" class="bc1"></td></tr>
<? } ?>
</table>

<? if ($i && $total_count > $rows) { ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
<tr height="90">
    <td><?=$shop_pages?></td>
</tr>
</table>
<? } ?>

<? if (!$i) { ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr height="200">
    <td class="not">데이터가 없습니다.</td>
</tr>
</table>
<? } ?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td height="1" bgcolor="#c9c9c9" class="none">&nbsp;</td></tr>
<tr><td height="1" bgcolor="#ffffff" class="none">&nbsp;</td></tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" style="margin:20px auto 0 auto;">
<tr>
    <td><a href="#" onclick="checkOk(); return false;"><img src="<?=$shop['image_path']?>/adm/all_order_prepare_ok.gif" border="0" /></a></td>
    <td width="5"></td>
    <td><a href="#" onclick="checkCancel(); return false;"><img src="<?=$shop['image_path']?>/adm/all_order_prepare_cancel.gif" border="0" /></a></td>
    <td width="5"></td>
    <td><a href="#" onclick="checkOrderCancel(); return false;"><img src="<?=$shop['image_path']?>/adm/all_order_cancel_ok_admin.gif" border="0" /></a></td>
    <td width="5"></td>
    <td><a href="#" onclick="checkExcel(); return false;"><img src="<?=$shop['image_path']?>/adm/all_excel.gif" border="0" /></a></td>
    <td width="5"></td>
    <td><a href="./order_excel.php?m=prepare"><img src="<?=$shop['image_path']?>/adm/excel.gif" border="0"></a></td>
</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr height="15"><td></td></tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" class="auto">
<tr>
    <td class="msg2">선택하신 상품을 위 조건으로 일괄 처리 합니다.</td>
</tr>
</table>
</form>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr height="50"><td></td></tr>
</table>

<table cellpadding="0" cellspacing="0" border="0" class="auto">
<tr>
    <td><img src="<?=$shop['image_path']?>/adm/guide_order_prepare.gif" border="0" usemap="#guide_map"></td>
</tr>
</table>

<map name="guide_map"><area shape="rect" coords="433,8,514,31" href="#"></map>

<div class="page_bottom"></div>
</div>

<?
include_once("./_bottom.php");
?>