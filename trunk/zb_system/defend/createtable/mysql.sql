

CREATE TABLE IF NOT EXISTS `zbp_article` (
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
  KEY `zbp_log_PostTime` (`log_PostTime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `zbp_category` (
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