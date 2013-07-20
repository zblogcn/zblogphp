CREATE TABLE IF NOT EXISTS `%pre%article` (
  `log_ID` int(11) NOT NULL AUTO_INCREMENT,
  `log_CateID` int(11) DEFAULT '0',
  `log_AuthorID` int(11) DEFAULT '0',
  `log_Level` int(11) DEFAULT '0',
  `log_Alias` varchar(255) DEFAULT '',
  `log_Title` varchar(255) DEFAULT '',
  `log_Intro` text,
  `log_Content` text,
  `log_IP` varchar(20) DEFAULT '',
  `log_PostTime` int(11) DEFAULT '0',
  `log_CommNums` int(11) DEFAULT '0',
  `log_ViewNums` int(11) DEFAULT '0',
  `log_TrackBackNums` int(11) DEFAULT '0',
  `log_Tag` varchar(255) DEFAULT '',
  `log_IsTop` bit(1) DEFAULT b'0',
  `log_Yea` int(11) DEFAULT '0',
  `log_Nay` int(11) DEFAULT '0',
  `log_Ratting` int(11) DEFAULT '0',
  `log_Template` varchar(50) DEFAULT '',
  `log_Url` varchar(255) DEFAULT '',
  `log_Type` int(11) DEFAULT '0',
  `log_Meta` text,
  PRIMARY KEY (`log_ID`),
  KEY `%pre%log_PostTime` (`log_PostTime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `%pre%category` (
  `cate_ID` int(11) NOT NULL AUTO_INCREMENT,
  `cate_Name` varchar(50) DEFAULT '',
  `cate_Order` int(11) DEFAULT '0',
  `cate_Intro` text,
  `cate_Count` int(11) DEFAULT '0',
  `cate_Alias` varchar(255) DEFAULT '',
  `cate_ParentID` int(11) DEFAULT '0',
  `cate_Template` varchar(50) DEFAULT '',
  `cate_LogTemplate` varchar(50) DEFAULT '',
  `cate_Url` varchar(255) DEFAULT '',
  `cate_Meta` text,
  PRIMARY KEY (`cate_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `%pre%comment` (
  `comm_ID` int(11) NOT NULL AUTO_INCREMENT,
  `log_ID` int(11) DEFAULT '0',
  `comm_AuthorID` int(11) DEFAULT '0',
  `comm_Author` varchar(20) DEFAULT '',
  `comm_Content` text,
  `comm_Email` varchar(50) DEFAULT '',
  `comm_HomePage` varchar(255) DEFAULT '',
  `comm_PostTime` int(11) DEFAULT '0',
  `comm_IP` varchar(20) DEFAULT '',
  `comm_Agent` text,
  `comm_Reply` text,
  `comm_LastReplyIP` varchar(20) DEFAULT '',
  `comm_LastReplyTime` int(11) DEFAULT '0',
  `comm_Yea` int(11) DEFAULT '0',
  `comm_Nay` int(11) DEFAULT '0',
  `comm_Ratting` int(11) DEFAULT '0',
  `comm_ParentID` int(11) DEFAULT '0',
  `comm_IsCheck` bit(1) DEFAULT b'0',
  `comm_Meta` text,
  PRIMARY KEY (`comm_ID`),
  KEY `%pre%comm_PostTime` (`comm_PostTime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `%pre%config` (
  `conf_Name` varchar(255) NOT NULL DEFAULT '',
  `conf_Value` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `%pre%counter` (
  `coun_ID` int(11) NOT NULL AUTO_INCREMENT,
  `coun_IP` varchar(20) DEFAULT '',
  `coun_Agent` text,
  `coun_Refer` varchar(255) DEFAULT '',
  `coun_PostTime` int(11) DEFAULT '0',
  `coun_Content` text,
  `coun_UserID` int(11) DEFAULT '0',
  `coun_PostData` text,
  `coun_Alias` text,
  `coun_AllRequestHeader` text,
  `coun_LogName` text,
  PRIMARY KEY (`coun_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `%pre%member` (
  `mem_ID` int(11) NOT NULL AUTO_INCREMENT,
  `mem_Guid` varchar(36) DEFAULT '',
  `mem_Level` int(11) DEFAULT '0',
  `mem_Name` varchar(20) DEFAULT '',
  `mem_Password` varchar(32) DEFAULT '',
  `mem_Sex` int(11) DEFAULT '0',
  `mem_Email` varchar(50) DEFAULT '',
  `mem_HomePage` varchar(255) DEFAULT '',
  `mem_PostTime` int(11) DEFAULT '0',
  `mem_Status` int(11) DEFAULT '0',
  `mem_Articles` int(11) DEFAULT '0',
  `mem_Pages` int(11) DEFAULT '0',
  `mem_Comments` int(11) DEFAULT '0',
  `mem_Intro` text,
  `mem_IP` varchar(20) DEFAULT '',
  `mem_Count` int(11) DEFAULT '0',
  `mem_Template` varchar(50) DEFAULT '',
  `mem_Url` varchar(255) DEFAULT '',
  `mem_Alias` varchar(255) DEFAULT '',
  `mem_Meta` text,
  PRIMARY KEY (`mem_ID`),
  KEY `%pre%mem_Name` (`mem_Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `%pre%module` (
  `mod_ID` int(11) NOT NULL AUTO_INCREMENT,
  `mod_Name` varchar(50) DEFAULT '',
  `mod_FileName` varchar(50) DEFAULT '',
  `mod_Order` int(11) DEFAULT '0',
  `mod_Content` text,
  `mod_IsHidden` bit(1) DEFAULT b'0',
  `mod_SidebarID` int(11) DEFAULT '0',
  `mod_HtmlID` varchar(50) DEFAULT '',
  `mod_Ftype` varchar(5) DEFAULT '',
  `mod_MaxLi` int(11) DEFAULT '0',
  `mod_Source` varchar(50) DEFAULT '',
  `mod_ViewType` varchar(50) DEFAULT '',
  `mod_IsHideTitle` bit(1) DEFAULT b'0',
  `mod_Meta` text,
  PRIMARY KEY (`mod_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `%pre%tag` (
  `tag_ID` int(11) NOT NULL AUTO_INCREMENT,
  `tag_Name` varchar(255) DEFAULT '',
  `tag_Intro` text,
  `tag_ParentID` int(11) DEFAULT '0',
  `tag_Alias` varchar(255) DEFAULT '',
  `tag_Order` int(11) DEFAULT '0',
  `tag_Count` int(11) DEFAULT '0',
  `tag_Template` varchar(50) DEFAULT '',
  `tag_Url` varchar(255) DEFAULT '',
  `tag_Meta` text,
  PRIMARY KEY (`tag_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `%pre%upload` (
  `ul_ID` int(11) NOT NULL AUTO_INCREMENT,
  `ul_AuthorID` int(11) DEFAULT '0',
  `ul_FileSize` int(11) DEFAULT '0',
  `ul_FileName` varchar(255) DEFAULT '',
  `ul_PostTime` int(11) DEFAULT '0',
  `ul_Quote` varchar(255) DEFAULT '',
  `ul_DownNum` int(11) DEFAULT '0',
  `ul_FileIntro` varchar(255) DEFAULT '',
  `ul_DirByTime` bit(1) DEFAULT b'0',
  `ul_Meta` text,
  PRIMARY KEY (`ul_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
