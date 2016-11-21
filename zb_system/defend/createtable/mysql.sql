CREATE TABLE IF NOT EXISTS %pre%post (
  log_ID int(11) NOT NULL AUTO_INCREMENT,
  log_CateID smallint(6) NOT NULL DEFAULT '0',
  log_AuthorID int(11) NOT NULL DEFAULT '0',
  log_Tag varchar(255) NOT NULL DEFAULT '',
  log_Status tinyint(4) NOT NULL DEFAULT '0',
  log_Type tinyint(4) NOT NULL DEFAULT '0',
  log_Alias varchar(255) NOT NULL DEFAULT '',
  log_IsTop tinyint(1) NOT NULL DEFAULT '0',
  log_IsLock tinyint(1) NOT NULL DEFAULT '0',
  log_Title varchar(255) NOT NULL DEFAULT '',
  log_Intro text NOT NULL,
  log_Content longtext NOT NULL,
  log_PostTime int(11) NOT NULL DEFAULT '0',
  log_CommNums int(11) NOT NULL DEFAULT '0',
  log_ViewNums int(11) NOT NULL DEFAULT '0',
  log_Template varchar(50) NOT NULL DEFAULT '',
  log_Meta longtext NOT NULL,
  PRIMARY KEY (log_ID),
  KEY %pre%log_TPISC (log_Type,log_PostTime,log_IsTop,log_Status,log_CateID),
  KEY %pre%log_VTSC (log_ViewNums,log_Type,log_Status,log_CateID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS %pre%category (
  cate_ID int(11) NOT NULL AUTO_INCREMENT,
  cate_Name varchar(255) NOT NULL DEFAULT '',
  cate_Order int(11) NOT NULL DEFAULT '0',
  cate_Count int(11) NOT NULL DEFAULT '0',
  cate_Alias varchar(255) NOT NULL DEFAULT '',
  cate_Intro text NOT NULL,
  cate_RootID int(11) NOT NULL DEFAULT '0',
  cate_ParentID int(11) NOT NULL DEFAULT '0',
  cate_Template varchar(50) NOT NULL DEFAULT '',
  cate_LogTemplate varchar(50) NOT NULL DEFAULT '',
  cate_Meta longtext NOT NULL,
  PRIMARY KEY (cate_ID),
  KEY %pre%cate_Order (cate_Order)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS %pre%comment (
  comm_ID int(11) NOT NULL AUTO_INCREMENT,
  comm_LogID int(11) NOT NULL DEFAULT '0',
  comm_IsChecking tinyint(1) NOT NULL DEFAULT '0',
  comm_RootID int(11) NOT NULL DEFAULT '0',
  comm_ParentID int(11) NOT NULL DEFAULT '0',
  comm_AuthorID int(11) NOT NULL DEFAULT '0',
  comm_Name varchar(50) NOT NULL DEFAULT '',
  comm_Email varchar(50) NOT NULL DEFAULT '',
  comm_HomePage varchar(255) NOT NULL DEFAULT '',
  comm_Content text NOT NULL,
  comm_PostTime int(11) NOT NULL DEFAULT '0',
  comm_IP varchar(15) NOT NULL DEFAULT '',
  comm_Agent text NOT NULL,
  comm_Meta longtext NOT NULL,
  PRIMARY KEY (comm_ID),
  KEY %pre%comm_LRI (comm_LogID,comm_RootID,comm_IsChecking),
  KEY %pre%comm_IsChecking (comm_IsChecking)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS %pre%config (
  conf_ID int(11) NOT NULL AUTO_INCREMENT,
  conf_Name varchar(50) NOT NULL DEFAULT '',
  conf_Value text,
  PRIMARY KEY (conf_ID),
  KEY %pre%conf_Name (conf_Name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS %pre%member (
  mem_ID int(11) NOT NULL AUTO_INCREMENT,
  mem_Guid varchar(36) NOT NULL DEFAULT '',
  mem_Level tinyint(4) NOT NULL DEFAULT '0',
  mem_Status tinyint(4) NOT NULL DEFAULT '0',
  mem_Name varchar(50) NOT NULL DEFAULT '',
  mem_Password varchar(32) NOT NULL DEFAULT '',
  mem_Email varchar(50) NOT NULL DEFAULT '',
  mem_HomePage varchar(255) NOT NULL DEFAULT '',
  mem_IP varchar(15) NOT NULL DEFAULT '',
  mem_PostTime int(11) NOT NULL DEFAULT '0',
  mem_Alias varchar(50) NOT NULL DEFAULT '',
  mem_Intro text NOT NULL,
  mem_Articles int(11) NOT NULL DEFAULT '0',
  mem_Pages int(11) NOT NULL DEFAULT '0',
  mem_Comments int(11) NOT NULL DEFAULT '0',
  mem_Uploads int(11) NOT NULL DEFAULT '0',
  mem_Template varchar(50) NOT NULL DEFAULT '',
  mem_Meta longtext NOT NULL,
  PRIMARY KEY (mem_ID),
  KEY %pre%mem_Name (mem_Name),
  KEY %pre%mem_Alias (mem_Alias),
  KEY %pre%mem_Level (mem_Level)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS %pre%module (
  mod_ID int(11) NOT NULL AUTO_INCREMENT,
  mod_Name varchar(255) NOT NULL DEFAULT '',
  mod_FileName varchar(50) NOT NULL DEFAULT '',
  mod_Content text NOT NULL,
  mod_SidebarID int(11) NOT NULL DEFAULT '0',
  mod_HtmlID varchar(50) NOT NULL DEFAULT '',
  mod_Type varchar(5) NOT NULL DEFAULT '',
  mod_MaxLi int(11) NOT NULL DEFAULT '0',
  mod_Source varchar(50) NOT NULL DEFAULT '',
  mod_IsHideTitle tinyint(1) NOT NULL DEFAULT '0',
  mod_Meta longtext NOT NULL,
  PRIMARY KEY (mod_ID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS %pre%tag (
  tag_ID int(11) NOT NULL AUTO_INCREMENT,
  tag_Name varchar(255) NOT NULL DEFAULT '',
  tag_Order int(11) NOT NULL DEFAULT '0',
  tag_Count int(11) NOT NULL DEFAULT '0',
  tag_Alias varchar(255) NOT NULL DEFAULT '', 
  tag_Intro text NOT NULL,  
  tag_Template varchar(50) NOT NULL DEFAULT '',
  tag_Meta longtext NOT NULL,
  PRIMARY KEY (tag_ID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS %pre%upload (
  ul_ID int(11) NOT NULL AUTO_INCREMENT,
  ul_AuthorID int(11) NOT NULL DEFAULT '0',
  ul_Size integer NOT NULL DEFAULT 0,
  ul_Name varchar(255) NOT NULL DEFAULT '',
  ul_SourceName varchar(255) NOT NULL DEFAULT '',
  ul_MimeType varchar(50) NOT NULL DEFAULT '',
  ul_PostTime int(11) NOT NULL DEFAULT '0',
  ul_DownNums int(11) NOT NULL DEFAULT '0',
  ul_LogID int(11) NOT NULL DEFAULT '0',  
  ul_Intro text NOT NULL,
  ul_Meta longtext NOT NULL,
  PRIMARY KEY (ul_ID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;