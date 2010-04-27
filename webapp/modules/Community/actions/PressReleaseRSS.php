<?php
// Ã±ÆÈ¤ÎCommunity·Ç¼¨ÈÄ¤òÉ½¼¨¤¹¤ëRSS

$rss = new UniversalFeedCreator();

//Community¾ðÊó
$community_row = $request->getAttribute('community_row');
$community_name = $request->getAttribute('community_name');
$system_top_address = $request->getAttribute('system_top_address');
$detail = $request->getAttribute('detail');

$community_row_url = $system_top_address .$community_row['community_profile']['top_page_url'];
//¡û¡û¸¦µæ¼¼¥³¥ß¥å¥Ë¥Æ¥£¤Î³µÍ×
$rss->useCached();
$rss->title = $community_name;
$rss->description = $community_row['community_profile']['contents_value'];
$rss->link = $community_row_url;										//¥³¥ß¥å¥Ë¥Æ¥£¤ÎTOP URL
$rss->url = $community_row_url;															//image rdf:resource
$rss->syndicationURL = $request->getAttribute('rss_syndicationURL');

//¡û¡û¸¦µæ¼¼¥í¥´²èÁü
$image = new FeedImage();
$image->title = $community_row['image_title'];
$image->link = ACSMsg::get_msg('Community', 'PressReleaseRSS.php', 'M001');
$image->url = $system_top_address .$community_row['image_url'];
$rss->image = $image;

// get news items from somewhere: 
$bbs_rss_array = $request->getAttribute('bbs_rss_array');
if($detail == 1){
	$rss_display_max_count = ACSSystemConfig::get_keyword_value(ACSMsg::get_mst('system_config_group','D06'), 'RSS_DISPLAY_MAX_COUNT');
	$rss_count = 0;
	foreach ($bbs_rss_array as $index => $data) {
		// CRLF ¢ª LF
		$body = preg_replace('/\r\n/', "\n", $data['body']);

		$item = new FeedItem(); 
		$item->post_date = $data['post_date']; 
		$item->title = $data['subject']; 
		$item->link = $system_top_address . $data['bbs_url'];
		$item->description = $body;
		$item->image_link = $data['file_url'];
		$item->description2 = $body; 		//Âè£²¤ÎËÜÊ¸
			
		$rss->addItem($item); 

		$rss_count++;
		if ($rss_count == $rss_display_max_count) {
			// ºÇÂç½ÐÎÏÉ½¼¨·ï¿ô¤Î¾ì¹ç¡¢½ªÎ»
			break;
		}
	}
}
// http-header
mb_http_output('pass');
header('Content-type: application/xml; charset=UTF-8');
echo mb_convert_encoding($rss->createFeed("RSS1.0"), 'UTF-8', mb_internal_encoding());

?>
