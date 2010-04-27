<?php
// RSSサンプル

$rss = new UniversalFeedCreator();

$rss->useCached();

//システム情報の取得
$system_config_keyword_value = $request->getAttribute('system_config_keyword_value');
$rss->title = $system_config_keyword_value['SYSTEM_NAME'];
$rss->description = $system_config_keyword_value['SYSTEM_OUTLINE'];
$rss->link = $system_config_keyword_value['SYSTEM_BASE_URL'];
$rss->syndicationURL = $request->getAttribute('rss_syndicationURL');

$image = new FeedImage();
$image->title = $system_config_keyword_value['SYSTEM_IMAGE']['title'];
$image->url = $system_config_keyword_value['SYSTEM_IMAGE']['url'];
$image->link = $system_config_keyword_value['SYSTEM_IMAGE']['link'];
$image->description = $system_config_keyword_value['SYSTEM_IMAGE']['description'];
$rss->image = $image;

// get news items from somewhere: 
$bbs_rss_array = $request->getAttribute('bbs_rss_array');
$system_top_address = $request->getAttribute('system_top_address');

$rss_display_max_count = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D06'), 'RSS_DISPLAY_MAX_COUNT');
$rss_count = 0;
foreach ($bbs_rss_array as $index => $data) {
	// CRLF → LF
	$body = preg_replace('/\r\n/', "\n", $data['body']);

    $item = new FeedItem(); 
    $item->post_date = $data['post_date']; 
    $item->title = $data['community_id_name'] . "::" . $data['subject']; 
	$item->link = $system_top_address . $data['bbs_url'];
    $item->description = $body;
    $item->image_link = $data['file_url'];
    $item->description2 = $body; 		//第２の本文
		
    $rss->addItem($item); 

	$rss_count++;
	if ($rss_count == $rss_display_max_count) {
		// 最大出力表示件数の場合、終了
		break;
	}
}

// http-header
mb_http_output('pass');
header('Content-type: application/xml; charset=UTF-8');
echo mb_convert_encoding($rss->createFeed("RSS1.0"), 'UTF-8', mb_internal_encoding());

?>
