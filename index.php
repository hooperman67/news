<?php
    include_once('SimplePie.compiled.php');

$news_rss = array(
    'https://www.dailyrecord.co.uk/all-about/celtic-fc/?service=rss',
    'https://www.scotsman.com/sport/football/celtic/rss',
    'https://feeds.bbci.co.uk/sport/6d397eab-9d0d-b84a-a746-8062a76649e5/rss.xml',
    'https://www.glasgowtimes.co.uk/sport/celtic/rss/',
    'https://news.stv.tv/topic/celtic/feed',
    'https://www.footballscotland.co.uk/all-about/celtic-fc?service=rss',
    'https://www.glasgowworld.com/sport/football/celtic/rss',
    'https://www.glasgowlive.co.uk/all-about/celtic-fc/?service=rss'
);

$blogs_rss = array(
    'https://www.celticquicknews.co.uk/feed/',
    'https://readceltic.com/feed',
    'https://thecelticstar.com/feed/',
    'https://www.67hailhail.com/feed/',
    'https://celtic365.com/feed/',
    'https://celticfanzine.com/category/news/feed/',
    'http://celticunderground.net/feed/',
    'https://celticbynumberscom.ipage.com/feed/',
    'https://videocelts.com/category/blogs/latest-news/feed/',    
    'https://www.sportsmole.co.uk/football/celtic.xml'
);

// Create instances for each array
$news_feed = new SimplePie();
$blogs_feed = new SimplePie();

// Set feed URLs for each instance
$news_feed->set_feed_url($news_rss);
$blogs_feed->set_feed_url($blogs_rss);

$news_feed->set_item_limit(15);
$blogs_feed->set_item_limit(15);

// Initialize feeds
$news_feed->init();
$blogs_feed->init();

     function shorten($string, $length)
{
    // By default, an ellipsis will be appended to the end of the text.
    $suffix = '&hellip;';

    $short_desc = trim(str_replace(array("\r","\n", "\t"), ' ', strip_tags($string)));
 
    // Cut the string to the requested length, and strip any extraneous spaces 
    // from the beginning and end.
    $desc = trim(substr($short_desc, 0, $length));
 
    // Find out what the last displayed character is in the shortened string
    $lastchar = substr($desc, -1, 1);
 
    // If the last character is a period, an exclamation point, or a question 
    // mark, clear out the appended text.
    if ($lastchar == '.' || $lastchar == '!' || $lastchar == '?') $suffix='';
 
    // Append the text.
    $desc .= $suffix;
 
    // Send the new description back to the page.
    return $desc;
} 
    $blogitems = [];  
foreach ($blogs_feed->get_items(0, 20) as $item) {
    $blogs_feed = $item->get_feed(); 
    
    // Extracting thumbnail from srcset attribute
    $thumbnail = '';
    $content = $item->get_content();
    $doc = new DOMDocument();
    $doc->loadHTML($content);
    $xpath = new DOMXPath($doc);
    $srcset = $xpath->evaluate("string(//img/@srcset)");

    if (!empty($srcset)) {
        // Split the srcset into individual image sources
        $sources = explode(',', $srcset);

        // Initialize variables to keep track of the selected image URL
        $selected_url = '';

        foreach ($sources as $source) {
            $parts = explode(' ', trim($source));
            $url = trim($parts[0]);
            $width = (int)trim($parts[1], 'w'); // Remove 'w' from width and convert to integer

            // Check if this image meets the maximum width requirement
            if ($width <= 600) {
                // If the width is within the limit, select this image
                $selected_url = $url;
                break; // Stop searching once a suitable image is found
            }
        }

        // Assign the selected image URL as the thumbnail
        $thumbnail = $selected_url;
    }

    // Check if thumbnail is still empty and attempt to get it from other methods
    if (empty($thumbnail)) {
        if (null !== ($enclosure = $item->get_enclosure(0))) {
            if ($enclosure->get_thumbnail()) {
                $thumbnail = $enclosure->get_thumbnail();
            } elseif ($enclosure->get_link()) {
                $thumbnail = str_replace("_m.jpg", "_s.jpg", $enclosure->get_link());
            }
        }

}
               // Assign default thumbnail if no image URL found in <content:encoded> tag
                if (empty($thumbnail)) {
                    $thumbnail = 'images/craic.webp';
                }
   $blogs_feed = $item->get_feed();
         
$blogitems [] = [ 
          "title" => $item->get_title(),
          "date" => $item->get_date("Y-m-d H:i"),
          "feed_title" => $item->get_feed()->get_title(),
          "link" => $item->get_permalink(),
          "description" => $item->get_description(),
          "desc" => $blogs_feed->get_title(),
          "enclosure_url" => $thumbnail,
          "guid" => $item->get_id()
      ];
      array_push($items);
$rss1 .= '<div class="col">';
$rss1 .= '<div class="card">';
$rss1 .= '<div class="card-image"><img loading="lazy" src="'. $thumbnail .'" alt="' . $item->get_title() . '"></div>';
$rss1 .= '<span class="card-title"><a rel="nofollow" target="_blank" href="' . $item->get_permalink() . '">' . $item->get_title() . '</a></span>';
$rss1 .= '<div class="card-content"><p>'. $item->get_date() .'</p><p>'. shorten($item->get_description(), 350) . '</p>';
$rss1 .= '<img src="images/user.svg" width="32px" height="32px" alt="Author">'.$item->get_feed()->get_title().'';
$rss1 .= '<br><span class="left"><a target="_blank" href="https://facebook.com/sharer/sharer.php?u='. urlencode($item->get_permalink()) .'"><img src="images/facebook.svg" width="32px" height="32px" alt="Facebook"> Share</a></span>';
$rss1 .= '<br>';
$rss1 .= '</div></div></div>';

    }
    
$jsonOutput = json_encode($blogitems, JSON_PRETTY_PRINT);    
file_put_contents('blogsfeed.json', $jsonOutput);
$template = file_get_contents('blogsbase.html');
$html = str_replace('<!-- posts here -->', $rss1, $template);
file_put_contents('blogs.html', $html);


    $newsitems = [];  
    foreach($news_feed->get_items(0,20) as $item) {
            if (null !== ($enclosure = $item->get_enclosure(0))) {
            // Output enclosure properties
            if ($enclosure->get_link() && $enclosure->get_type()) {
                $type = $enclosure->get_type();
                $size = $enclosure->get_size() ? $enclosure->get_size() . ' MB' : '';
               // echo "Enclosure Type: $type, Size: $size\n";
            }
            
            // Output thumbnail if available
            if ($enclosure->get_thumbnail()) {
                $thumbnail = $enclosure->get_thumbnail();
               // echo "Thumbnail: $thumbnail\n";
            }

            if ($enclosure->get_link()) {
                $thumbnail = str_replace("_m.jpg","_s.jpg" , $enclosure->get_link());
               // echo "Thumbnail: $thumbnail\n";
            }
    
            // You had an incomplete if block here, I'll correct it below
            if ($return = $item->get_item_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'thumbnail')) {
                $thumbnail_attribs = $return[0]['attribs'];
                // Do something with $thumbnail_attribs if needed
            }
            }
$newsitems [] =     ["title" => $item->get_title(),
          "date" => $item->get_date("Y-m-d H:i"),
          "feed_title" => $item->get_feed()->get_title(),
           "link" => $item->get_permalink(),
           "site_title" => $news_feed->get_title(),
           "description" => $item->get_description(),
          "enclosure_url" => $thumbnail,
          "guid" => $item->get_id()
      ];
      array_push($items);
      $rss .= '<div class="col">';
      $rss .= '<div class="card">';
      $rss .= '<div class="card-image"><img loading="lazy" src="'. $thumbnail .'"  alt="' . $item->get_title() . '"></div>';
      $rss .= '<span class="card-title"><a rel="nofollow" target="_blank" href="' . $item->get_permalink() . '">' . $item->get_title() . '</a></span>';
      $rss .= '<div class="card-content"><p>'. $item->get_date() .'</p><p>'. shorten($item->get_description(), 350) . '</p>'; 
      $rss .= '<img src="images/user.svg" width="32px" height="32px" alt="Author">'.$item->get_feed()->get_title().'';
      $rss .= '<br><span class="left"><a target="_blank" href="https://facebook.com/sharer/sharer.php?u='. urlencode($item->get_permalink()) .'"><img src="images/facebook.svg" width="32px" height="32px" alt="Facebook"> Share</a></span>';
      $rss .= '<br>';
     $rss .= '</div></div></div>';
   
    }
$jsonOutput = json_encode($newsitems, JSON_PRETTY_PRINT);    
file_put_contents('newsfeed.json', $jsonOutput);
$template = file_get_contents('newsbase.html');
$html = str_replace('<!-- posts here -->', $rss, $template);
file_put_contents('news.html', $html);