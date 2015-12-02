<?php

/*
  Plugin Name: ailyak.facebook.group.feed
  Plugin URI: 
  Version: 0.1
  Author: Angel Koilov
  Description: @TODO
 */

function ailyak_facebook_group_feed()
{
    // fuck options!
    // hardcode everything!
    
    // @TODO remove access_token before github publishing
    $token = ''; // put your token here!
    $url = "https://graph.facebook.com/v2.3/561003627289743/feed?limit=10&access_token={$token}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    $data = curl_exec($ch);
    curl_close($ch);
    $dataDecoded = json_decode($data, true);
    $posts = $dataDecoded['data'];
    
    // I have twig template,
    // but I don't have time to
    // include twig in stupid the wordpress
    
    // yes, I know ....
    // code sux... I know...
    // Today worktime: 10h 25min
    
    $return = '
            <style>

                .fbcomments {
                    width: 100%;
                }
                
                .fbcommentreply {
                    margin-left: 30px !important;
                }

                .fbcomments div {
                    margin: auto; 
                }

                /* Default Facebook CSS */
                .fbbody
                {
                    font-family: "lucida grande" ,tahoma,verdana,arial,sans-serif;
                    font-size: 11px;
                    color: #333333;
                }
                /* Default Anchor Style */
                .fbbody a
                {
                    color: #3b5998;
                    outline-style: none;
                    text-decoration: none;
                    font-size: 11px;
                    font-weight: bold;
                }
                .fbbody a:hover
                {
                    text-decoration: underline;
                }
                /* Facebook Box Styles */
                .fbgreybox
                {
                    background-color: #f7f7f7;
                    border: 1px solid #cccccc;
                    color: #333333;
                    padding: 10px;
                    font-size: 13px;
                    font-weight: bold;
                }
                .fbbluebox
                {
                    background-color: #eceff6;
                    border: 1px solid #d4dae8;
                    color: #333333;
                    padding: 10px;
                    font-size: 13px;
                    font-weight: bold;
                }
                .fbinfobox
                {
                    background-color: #fff9d7;
                    border: 1px solid #e2c822;
                    color: #333333;
                    padding: 10px;
                    font-size: 13px;
                    font-weight: bold;
                }

                .fbcomment {
                    text-align: left;
                }

                .fberrorbox
                {
                    background-color: #ffebe8;
                    border: 1px solid #dd3c10;
                    color: #333333;
                    padding: 10px;
                    font-size: 13px;
                    font-weight: bold;
                }
                /* Content Divider on White Background */
                .fbcontentdivider
                {
                    margin-top: 15px;
                    margin-bottom: 15px;
                    width: 520px;
                    height: 1px;
                    background-color: #d8dfea;
                }
                /* Facebook Tab Style */
                .fbtab
                {
                    padding: 8px;
                    background-color: #d8dfea;
                    color: #3b5998;
                    font-weight: bold;
                    float: left;
                    margin-right: 4px;
                    text-decoration: none;
                }
                .fbtab:hover
                {
                    background-color: #3b5998;
                    color: #ffffff;
                    cursor: hand;
                }

            </style>
    ';
    
    $return .= '<div class="fbcomments">';
    foreach ($posts as $post) :
        
        array_walk_recursive($post, 'ailyak_facebook_group_feed_html_escape');
        if (!empty($post['message'])):
            $postId = $post['id'];
            $postName = $post['from']['name'];
            $message = nl2br($post['message']);
            $postTimeDatetime = new \DateTime($post['created_time']);
            $postTime = $postTimeDatetime->format("M, d, D, G:h");
            $postLink = $post['actions'][0]['link'];
            
            $return .= '
                <div class="fbinfobox fbcomment"> 
                    <div>
                        <a href="https://www.facebook.com/' . $postId . '">' . $postName . '</a>,
                        <a href="' . $postLink . '">
                            ' . $postTime  . '
                        </a>
                    </div>

                    ' . $message . '
                </div>
            ';

            if (!empty($post['comments'])):
                foreach ($post['comments']['data'] as $comment):
                    $commentId = $comment['id'];
                    $commentFromName = $comment['from']['name'];
                    $commentMessage = nl2br($comment['message']);
                    $commentDateDatetime = new \DateTime($comment['created_time']);
                    $commentDate = $commentDateDatetime->format("M, d, D, G:h");
                    $return .= '
                        <div class="fbgreybox fbcommentreply">  
                            <div>
                                <a href="https://www.facebook.com/' . $commentId . '">' . $commentFromName . '</a>,
                                ' . $commentDate . '
                            </div> 
                            ' . $commentMessage . '
                        </div>
                    ';
                endforeach;
            endif;
        endif;
    endforeach;
    
    return $return;
}

// just to have some escape...
function ailyak_facebook_group_feed_html_escape(&$text) {
    $text = htmlspecialchars($text);
}

add_shortcode('ailyak.facebook.group.feed', 'ailyak_facebook_group_feed');
