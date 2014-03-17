<?php
ini_set('max_execution_time', '0');
class Changyan_Synchronizer
{
    private static $instance = null;
    private $PluginURL = 'changyan';

    private function __construct()
    {
        $this->PluginURL = plugin_dir_url(__FILE__);
    }

    private function __clone()
    {
        //Prevent from being cloned
    }

    //return the single instance of this class
    public static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function sync2Wordpress(){
        global $zbp;
	
        @set_time_limit(0);
        @ini_set('memory_limit', '256M');

        $script = $this->getOption('changyan_script');
        $appID = explode("'", $script);
        //Now we get the appID from the script
        $appID = $appID[1];

        //not site_url: the folder of wp installed, see http://www.andelse.com/wordpress-url-strategy-url-function-list.html
        //echo "<br/>".home_url();//it is right
        //get post list from
        $nextID2CY = (int)$this->getOption('changyan_sync2CY');
        $nextID2WP = (int)$this->getOption('changyan_sync2WP');

		$cmt=$zbp->GetCommentByID($nextID2WP);
		date_default_timezone_set('Etc/GMT-8');
        if ($cmt->ID==0) {
			$time = date ( "Y-m-d H:i:s",0 );
        }else{
			$time = date ( "Y-m-d H:i:s",$cmt->PostTime );
		}
		date_default_timezone_set($zbp->option['ZC_TIME_ZONE_NAME']);

        $params = array(
            'appId' => $appID,
            'date' => $time
        );

        $URL = "http://changyan.sohu.com/admin/api/recent-comment-topics";
        $URL = $this->buildURL($params, $URL);

        $data = $this->getContents_curl($URL);
        $data = json_decode($data);

        if (empty($data->success)) {
            die("同步失败:服务器返回空值");
        }
        if ('false' === $data->success) {
            die("同步失败:" . ($data->success));
        }
		
		$postGroup = $data->topics;
		//die(var_dump($postGroup));
        $lastCommentID = $this->getOption('changyan_sync2WP');
        if (empty($lastCommentID)) {
            $lastCommentID = 0;
        }

        foreach ($postGroup as $aPost) {

            $cyanCommentList = $this->getCommentList_curl($appID, $aPost->topic_id);

            $commentID = $this->insertComments($cyanCommentList, $aPost->topic_source_id);

            if ($commentID > $lastCommentID) {
                $lastCommentID = $commentID;
            }
			
			//recode the latest synchronization time
			$this->setOption('changyan_lastSyncTime', time());
			$this->setOption('changyan_sync2WP', $lastCommentID);

        }

        die("同步成功");
	}
	
    //get comment list through cURL
    //return Array
    private function getCommentList_curl($appID, $aPostID)
    {
        #region 'Using api/open/comment/list to get comment list in Changyan'
        //page_no is the current comment page number
        $page_no = 1;
        //page_sum is the sum of comment pages
        $page_sum = 1;
        //commentPageArray is array of the comment pages
        $commentPageArray = array();

        while ($page_no <= $page_sum) {
            //clear $data
            unset($data);
            //generate the params
            $data = array(
                'client_id' => $appID,
                'topic_id' => $aPostID,
                'outer_page_no' => $page_no,
                'style' => 'terrace'
            );

            //append the params behind the url
            $sUrl = 'https://changyan.sohu.com/api/open/comment/list';
            $sUrl = $this->buildURL($data, $sUrl);

            //execute GET through cURL
            $data = $this->getContents_curl($sUrl);
            //$data is object now
            $data = json_decode($data);

            $page_sum = intval(($data->cmt_sum) / 30) + 1;

            //insert data into $commentPageArray
            $commentPageArray[] = $data;
            $page_no += 1;
        }
        #endregion
        return $commentPageArray;
	}
	
    //build GET URL by data array and base url
    public function buildURL($dataArray, $baseURL)
    {
        $dataSection = http_build_query($dataArray);
        $URL = $baseURL;
        $URL .= ("?" . $dataSection);

        return $URL;
    }

    /**
     * execute GET function using cURL, return JSON
     */
    public function getContents_curl($aUrl)
    {
        //check if exits
        if (!function_exists('curl_init')) {
            throw new Exception('server not install curl');
        }
        //curl intialization and execution
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $aUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 900);

        $data = curl_exec($ch);

        $error = curl_error($ch);
        //close
        if ($data == false || !empty($error)) {
            curl_close($ch);
            die("同步失败: " . $error);
        }
        @curl_close($ch);

        return $data;
    }

	function findCommentInDB($aComment,$postID){
		global $zbp;
		$date=$aComment->create_time;
		$ip=$aComment->ip;
		$w[] = array('=', 'comm_LogID', $postID);
		$w[] = array('=', 'comm_PostTime', $date);
		$w[] = array('=', 'comm_IP', $ip);
		$comments = $zbp->GetCommentList('*', $w, null, null, null);
		if(count($comments)==0){
			$cmt = New Comment;
			
            $cmt->LogID = $postID;
            $cmt->IsChecking = (bool)$aComment->status;
			$cmt->AuthorID = 0;
            $cmt->Name = $aComment->passport->nickname;
            $cmt->Email = '';
            $cmt->HomePage = $aComment->passport->profile_url;
            $cmt->IP = $aComment->ip;

            $cmt->PostTime = $aComment->create_time;
            $cmt->Content = $aComment->content;
            $cmt->Agent = "Changyan_" . $aComment->comment_id;
			
			return $cmt;
		}else{
			return $comments[0];
		}
	}
	
    /**
     * get comment information object generated by changyan server
     *
     * @param $cmts object of a node of the comment page array file
     * @param $postID id of the post which the comments reply to
     * @return int Array of comments array
     */
    private function insertComments($cmts, $postID)
    {
		global $zbp;
		$commentsArray = array();

		foreach ($cmts as $cmt) {
			foreach (($cmt->comments) as $aComment) {
				$commentsArray[] = $aComment;
			}
		}
		$commentsArray = array_reverse($commentsArray);

		$comments = array();
		$commentscy = array();

		foreach ($commentsArray as &$aComment) {
			if(count($aComment->comments)>3){
				continue;
			}
			$aComment->create_time=(int)substr($aComment->create_time,0,-3);
			$comments[$aComment->comment_id]=$aComment;
			$commentscy[$aComment->comment_id]=$aComment;

		}
		
		foreach ($comments as $aComment) {
			$cmt = $this->findCommentInDB($aComment,$postID);

			if($cmt->ID==0){
				//echo $cmt->ID;
				if(isset($aComment->comments[0])){
					if(isset($commentscy[$aComment->comments[0]->comment_id])){
						if(isset($commentscy[$aComment->comments[0]->comment_id]->ID))
							$cmt->ParentID = $commentscy[$aComment->comments[0]->comment_id]->ID;
					}
				}else{
					$cmt->ParentID = 0;
				}
				$cmt->RootID = Comment::GetRootID($cmt->ParentID);
				$cmt->Save();
				$commentscy[$aComment->comment_id]=$cmt;
				$zbp->comments[$aComment->comment_id]=$cmt;
			}else{
				$commentscy[$aComment->comment_id]=$cmt;
			}
			unset($cmt);
		}
		$commentLastID=0;
		foreach ($commentscy as $aComment) {
			if(isset($aComment->ID))
				if($aComment->ID > $commentLastID)$commentLastID = $aComment->ID;
		}
		return $commentLastID;
	}
	

    //This is a comparation function used by usort in function insertComments()
    private function cmtAscend($x, $y)
    {
        return (intval($x->create_time)) > (intval($y->create_time)) ? 1 : -1;
    }

    //This is a comparation function used by usort in function insertComments()
    private function cmtDescend($x, $y)
    {
        return (intval($x->create_time)) > (intval($y->create_time)) ? -1 : 1;
    }

    //to check if this comment is in the wpdb
    private function isCommentExits($post_id, $comment_author, $comment_content, $date_gmt)
    {
        global $wpdb;
        $comment = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT comment_ID FROM $wpdb->comments
                    WHERE  comment_post_ID = %s
                    AND comment_content = %s
                    AND comment_date_gmt = %s
                    AND comment_author = %s",
                $post_id,
                stripslashes($comment_content),
                $date_gmt,
                $comment_author
            )
        );

        if (is_array($comment) && !empty($comment)) {
            return true;
        } else {
            return false;
        }
    }

    #endregion

    #region 'Synchronize to Changyan'
    public function sync2Changyan($isSetup = false)
    {
        global $zbp;
        @set_time_limit(0);
        @ini_set('memory_limit', '256M');

        $nextID2CY = $this->getOption('changyan_sync2CY');

        if ($nextID2CY==0) {
            $nextID2CY = 1;
        }

        $maxID = $zbp->db->Query(
			$zbp->db->sql->Select(
				'%pre%comment',
				'MAX(comm_ID)',
				array(
					array('CUSTOM',"comm_Agent NOT LIKE 'Changyan_%'")
				),
				null,
				null,
				null
			)
		);

        $maxID = $maxID[0]['MAX(comm_ID)'];
		
        $postIDList = $zbp->db->Query(
			$zbp->db->sql->Select(
				'%pre%comment',
				'DISTINCT comm_LogID',
				array(
					array('CUSTOM',"comm_ID > $nextID2CY AND comm_ID <= $maxID")
				),
				null,
				null,
				null
			)
		);
		$postIDList2=array();
		foreach($postIDList as $id){
			$postIDList2[]=(int)$id['comm_LogID'];
		}

        //flag of response
        $flag = true;
        $response = "";
		
        foreach ($postIDList2 as $aPostID) {
            //in case of bug of duoshuo or other plugins: postID larger than maxPostID
            //echo ($aPost -> comment_post_ID)."  ";//////////////////xcv
            $postInfo = $zbp->GetPostByID($aPostID);
			if($postInfo->ID==0)continue;

            //build the comments to be synchronized
            $topic_id = $postInfo->ID;
            $topic_url = $postInfo->Url;
            $topic_title = $postInfo->Title;
			
			date_default_timezone_set('Etc/GMT-8');
			$topic_time = date ( "Y-m-d H:i:s",$postInfo->PostTime );
			
            $topic_parents = ""; //$postInfo[0]->post_parents;
            $script = $this->getOption('changyan_script');
            $appID = explode("'", $script);
            //get the appID from the script
            $appID = $appID[1];
            //echo $topic_title."<br/>";//////////////////////xcv
            if ($isSetup == true) {            } else {            }
            $comments = array();
			
			$commentsList = $zbp->GetCommentList(
					'*',
					array(
						array('=', 'comm_IsChecking', 0),
						array('=', 'comm_LogID', $aPostID)
					)
				);
            $comments = array();
            //insert comments into the commentsArray
            foreach ($commentsList as $comment) {
				if(strpos($comment->Agent,'Changyan_')===0)continue;
                $user = array(
                    'userid' => $comment->AuthorID,
                    'nickname' => $comment->Name,
                    'usericon' => '',
                    'userurl' => $comment->HomePage
                );
                $comments[] = array(
                    'cmtid' => $comment->ID,
                    'ctime' => $comment->Time(),
                    'content' => $comment->Content,
                    'replyid' => $comment->ParentID,
                    'user' => $user,
                    'ip' => $comment->IP,
                    'useragent' => $comment->Agent,
                    'channeltype' => '1',
                    'from' => '',
                    'spcount' => '',
                    'opcount' => ''
                );
            }
			date_default_timezone_set($zbp->option['ZC_TIME_ZONE_NAME']);

            //comments under a post to be synchronized
            $postComments = array(
                'title' => $topic_title,
                'url' => $topic_url,
                'ttime' => $topic_time,
                'sourceid' => $topic_id,
                'parentid' => $topic_parents,
                'categoryid' => '',
                'ownerid' => '',
                'metadata' => '',
                'comments' => $comments
            );

            if (empty($comments)) {
                continue;
            }

            //get the appID from the script
            $script = $this->getOption('changyan_script');
            $appID = explode("'", $script);
            $appID = $appID[1];

            $postComments = json_encode($postComments);

            //hmac encode
            $appKey = $this->getOption('changyan_appKey');
            $appKey = trim($appKey);
            $md5 = hash_hmac('sha1', $postComments, $appKey);

            $postData = "appid=" . $appID . "&md5=" . $md5 . "&jsondata=" . $postComments;
            //print_r($postData);////////////xcv//////////
            $response = $this->postContents_curl("http://changyan.sohu.com/admin/api/import/comment", $postData);
            $regex = '/success":true/';
            //if "true" not found
            //echo "Response is ".$response;//xcv
            if (!preg_match($regex, $response)) {
                $flag = false;
                break;
            }
		}
        if ($flag === true) {
            //recode the latest synchronization time
            $this->setOption('changyan_lastSyncTimeCY', time());
            $this->setOption('changyan_sync2CY', $maxID);
            die("同步成功");
        } else {
            die("同步失败:" . $response);
        }
    }
	
	
	
    public function sync2Changyan1($isSetup = false)
    {
        global $wpdb;
        @set_time_limit(0);
        @ini_set('memory_limit', '256M');

        $nextID2CY = $this->getOption('changyan_sync2CY');

        if (empty($nextID2CY)) {
            $nextID2CY = 1;
        }

        $maxID = $wpdb->get_results(
            "SELECT MAX(comment_ID) AS maxID FROM $wpdb->comments
                WHERE comment_agent NOT LIKE '%%Changyan%%'"
        );
        $maxID = $maxID[0]->maxID;

        $postIDList = $wpdb->get_results($wpdb->prepare(
            "SELECT DISTINCT comment_post_ID FROM $wpdb->comments
                WHERE comment_ID > %s
                AND comment_ID <= %s",
            $nextID2CY,
            $maxID
        ));
        //echo "nextID2CY is ".$nextID2CY.";maxID is ".$maxID."<br/>";//xcv
        //echo "postIDlist: <br/>";print_r($postIDList);echo "<br/>";//xcv
        $maxPostID = $wpdb->get_results("SELECT MAX(ID) AS maxPostID FROM $wpdb->posts"); //
        $maxPostID = $maxPostID[0]->maxPostID; //
        //flag of response
        $flag = true;
        $response = "";

        foreach ($postIDList as $aPost) {
            //in case of bug of duoshuo or other plugins: postID larger than maxPostID
            //echo ($aPost -> comment_post_ID)."  ";//////////////////xcv
            if ($aPost->comment_post_ID > $maxPostID) {
                continue;
            }
            $postInfo = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT ID AS post_ID,
                        post_title AS post_title,
                        post_date AS post_time,
                        post_parent AS post_parents
                        FROM $wpdb->posts
                        WHERE post_type NOT IN ('attachment', 'nav_menu_item', 'revision')
                        AND post_status NOT IN ('future', 'auto-draft', 'draft', 'trash', 'inherit')
                        AND ID = %s",
                    $aPost->comment_post_ID
                )
            );
            //build the comments to be synchronized
            $topic_url = get_permalink($postInfo[0]->post_ID);
            $topic_title = $postInfo[0]->post_title;
            $topic_time = $postInfo[0]->post_time;
            $topic_id = $postInfo[0]->post_ID;
            $topic_parents = ""; //$postInfo[0]->post_parents;
            $script = $this->getOption('changyan_script');
            $appID = explode("'", $script);
            //get the appID from the script
            $appID = $appID[1];
            //echo $topic_title."<br/>";//////////////////////xcv
            if ($isSetup == true) {
                $commentsList = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT * FROM  $wpdb->comments
                            WHERE comment_post_ID = %s
                            AND comment_ID BETWEEN %s AND %s",
                        $postInfo[0]->post_ID,
                        $nextID2CY,
                        $maxID
                    )
                );
            } else {
                $commentsList = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT * FROM $wpdb->comments
                            WHERE comment_post_ID = %s
                            AND comment_agent NOT LIKE '%%Changyan%%'
                            AND comment_ID BETWEEN %s AND %s",
                        $postInfo[0]->post_ID,
                        $nextID2CY,
                        $maxID
                    )
                );
                //echo "postID: ".$postInfo[0]->post_ID." :<br/>";print_r($commentsList);echo "<br/>";//xcv
            }
            $comments = array();
            //insert comments into the commentsArray
            foreach ($commentsList as $comment) {
                $user = array(
                    'userid' => $comment->user_id,
                    'nickname' => $comment->comment_author,
                    'usericon' => '',
                    'userurl' => $comment->comment_author_url
                );
                $comments[] = array(
                    'cmtid' => $comment->comment_ID,
                    'ctime' => $comment->comment_date,
                    'content' => $comment->comment_content,
                    'replyid' => $comment->comment_parent,
                    'user' => $user,
                    'ip' => $comment->comment_author_IP,
                    'useragent' => $comment->comment_agent,
                    'channeltype' => '1',
                    'from' => '',
                    'spcount' => '',
                    'opcount' => ''
                );
            }
            //comments under a post to be synchronized
            $postComments = array(
                'title' => $topic_title,
                'url' => $topic_url,
                'ttime' => $topic_time,
                'sourceid' => "",
                'parentid' => $topic_parents,
                'categoryid' => '',
                'ownerid' => '',
                'metadata' => '',
                'comments' => $comments
            );

            if (empty($comments)) {
                continue;
            }

            //get the appID from the script
            $script = $this->getOption('changyan_script');
            $appID = explode("'", $script);
            $appID = $appID[1];

            $postComments = json_encode($postComments);

            //hmac encode
            $appKey = $this->getOption('changyan_appKey');
            $appKey = trim($appKey);
            $md5 = hash_hmac('sha1', $postComments, $appKey);

            $postData = "appid=" . $appID . "&md5=" . $md5 . "&jsondata=" . $postComments;
            //print_r($postData);////////////xcv//////////
            $response = $this->postContents_curl("http://changyan.sohu.com/admin/api/import/comment", $postData);
            $regex = '/success":true/';
            //if "true" not found
            //echo "Response is ".$response;//xcv
            if (!preg_match($regex, $response)) {
                $flag = false;
                break;
            }
        }
        if ($flag === true) {
            //recode the latest synchronization time
            $this->setOption('changyan_lastSyncTime', date("Y-m-d G:i:s", time() + get_option('gmt_offset') * 3600));
            $this->setOption('changyan_sync2CY', $maxID);
            die("同步成功");
        } else {
            die("同步失败:" . $response);
        }
    }

    //execute POST function using cURL and return JSON array containing comment_ids in Changyan
    private function postContents_curl($aUrl, $aCommentsArray, $headerPart = array())
    {
        //check if exits
        if (!function_exists('curl_init')) {
            throw new Exception('server not install curl');
        }

        //curl intialization and execution
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $aUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 900);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $aCommentsArray);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //to do a HTTP POST of over 1024 characters
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Expect:'));

        $data = curl_exec($ch);

        $error = curl_error($ch);
        //close
        if ($data == false || !empty($error)) {
            curl_close($ch);
            die("Error: " . $error);
        }
        @curl_close($ch);

        return $data;
    }

    #endregion

    private function getOption($option)
    {
		global $zbp;
		return $zbp->Config('changyan')->$option;
        //return get_option($option);
    }

    private function setOption($option, $value)
    {
		global $zbp;
		$zbp->Config('changyan')->$option=$value;
		$zbp->SaveConfig('changyan');
		return true;
        //return update_option($option, $value);
    }

    private function delOption($option)
    {
		global $zbp;
		$zbp->Config('changyan')->Del($option);
		$zbp->SaveConfig('changyan');
		return true;
        //return delete_option($option);
    }

    private function showAllComments()
    {
        global $wpdb;
        $cmtlist = $wpdb->get_results("SELECT * FROM $wpdb->comments", ARRAY_A);
        foreach ($cmtlist as $aCmt) {
            foreach ($aCmt as $v) {
                echo $v . ";  ";
            }
            echo "<br/>";
        }
    }
}

?>