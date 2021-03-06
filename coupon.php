<?
include_once("./_dmshop.php");

// 로그인
if (!$shop_user_login) {

    shop_url("$shop[path]/signin.php?url={$urlencode}");

}

// 스킨이 없다.
if (!$dmshop_skin['skin_mypage']) {

    message("<p class='title'>알림</p><p class='text'>마이페이지 스킨이 설정되지 않았습니다.</p>", "b");

}

// 스킨이 없다.
if (!$dmshop_skin['skin_coupon']) {

    message("<p class='title'>알림</p><p class='text'>쿠폰 스킨이 설정되지 않았습니다.</p>", "b");

}

// 검색조건 (발급된 쿠폰)
$sql_search = "";
$sql_search = " where user_id = '".$dmshop_user['user_id']."' and coupon_date2 >= '".$shop['time_ymd']."' and coupon_mode in (0,1) ";

$cnt = sql_fetch(" select count(*) as cnt from $shop[coupon_list_table] $sql_search ");

$total_count = $cnt['cnt'];

$rows = 10;

$total_page  = ceil($total_count / $rows);

if (!$page) {

    $page = 1;

}

$from_record = ($page - 1) * $rows;

$shop_pages = shop_paging_v1("10", $page, $total_page, "?page=");

$list = array();
$result = sql_query(" select * from $shop[coupon_list_table] $sql_search order by datetime desc limit $from_record, $rows ");
for ($i=0; $row=sql_fetch_array($result); $i++) {

    $list[$i] = $row;

    // 기간이 남았다.
    if ($shop['time_ymd'] <= $row['coupon_date2']) {

        // 종료일자에서 현재기간을 뺀다.
        $coupon_dday =  strtotime($row['coupon_date2']) - strtotime($shop['time_ymd']);

        // d-day
        $list[$i]['coupon_dday'] = (int)($coupon_dday / 86400);

    } else {

        $list[$i]['coupon_dday'] = 0;

    }

}

// 스킨 경로
$dmshop_mypage_path = "";
$dmshop_mypage_path = $shop['path']."/skin/mypage/".$dmshop_skin['skin_mypage'];

// 스킨 경로
$dmshop_coupon_path = "";
$dmshop_coupon_path = $shop['path']."/skin/coupon/".$dmshop_skin['skin_coupon'];

// 타이틀 제목
$shop['title'] = $dmshop['shop_name']." - 마이페이지 > 쿠폰 보관함";

// 페이지 아이디
$page_id = "coupon";

include_once("./_top.php");
include_once("$dmshop_coupon_path/coupon.php");
include_once("./_bottom.php");
?>