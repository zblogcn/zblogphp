CREATE TABLE IF NOT EXISTS %pre%log (
  log_ID int(11) NOT NULL AUTO_INCREMENT,
  log_CateID int(11) DEFAULT '0',
  log_AuthorID int(11) DEFAULT '0',
  log_Tag varchar(255) DEFAULT NULL,
  log_Status int(11) DEFAULT '0',
  log_Type int(11) DEFAULT '0',
  log_Alias varchar(255) DEFAULT '',
  log_IsTop tinyint(1) DEFAULT '0',
  log_IsLock tinyint(1) DEFAULT '0',
  log_Title varchar(255) DEFAULT '',
  log_Intro text,
  log_Content text,
  log_IP varchar(15) DEFAULT '',
  log_PostTime int(11) DEFAULT '0',
  log_CommNums int(11) DEFAULT '0',
  log_ViewNums int(11) DEFAULT '0',
  log_Template varchar(50) DEFAULT '',
  log_Meta text,
  PRIMARY KEY (log_ID),
  KEY %pre%log_PostTime (log_PostTime)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS %pre%category (
  cate_ID int(11) NOT NULL AUTO_INCREMENT,
  cate_Name varchar(50) DEFAULT '',
  cate_Order int(11) DEFAULT '0',
  cate_Count int(11) DEFAULT '0',
  cate_Alias varchar(255) DEFAULT NULL,
  cate_Intro text,
  cate_RootID int(11) DEFAULT '0',
  cate_ParentID int(11) DEFAULT '0',
  cate_Template varchar(50) DEFAULT NULL,
  cate_LogTemplate varchar(50) DEFAULT NULL,
  cate_Meta text,
  PRIMARY KEY (cate_ID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS %pre%comment (
  comm_ID int(11) NOT NULL AUTO_INCREMENT,
  comm_LogID int(11) DEFAULT '0',
  comm_IsCheck tinyint(1) DEFAULT '0',
  comm_RootID int(11) DEFAULT '0',
  comm_ParentID int(11) DEFAULT '0',
  comm_AuthorID int(11) DEFAULT '0',
  comm_Author varchar(20) DEFAULT '',
  comm_Content text,
  comm_Email varchar(50) DEFAULT '',
  comm_HomePage varchar(255) DEFAULT '',
  comm_PostTime int(11) DEFAULT '0',
  comm_IP varchar(15) DEFAULT '',
  comm_Agent text,
  comm_Meta text,
  PRIMARY KEY (comm_ID),
  KEY %pre%comm_PostTime (comm_PostTime)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS %pre%config (
  conf_Name varchar(255) NOT NULL DEFAULT '',
  conf_Value text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS %pre%counter (
  coun_ID int(11) NOT NULL AUTO_INCREMENT,
  coun_MemID int(11) DEFAULT '0',
  coun_IP varchar(15) DEFAULT '',
  coun_Agent text,
  coun_Refer varchar(255) DEFAULT '',
  coun_Title varchar(255) DEFAULT NULL,
  coun_PostTime int(11) DEFAULT '0',
  coun_Description text,
  coun_PostData text,
  coun_AllRequestHeader text,
  PRIMARY KEY (coun_ID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS %pre%member (
  mem_ID int(11) NOT NULL AUTO_INCREMENT,
  mem_Guid varchar(36) DEFAULT '',
  mem_Level int(11) DEFAULT '0',
  mem_Status int(11) DEFAULT '0',
  mem_Name varchar(20) DEFAULT NULL,
  mem_Password varchar(32) DEFAULT NULL,
  mem_Email varchar(50) DEFAULT NULL,
  mem_HomePage varchar(255) DEFAULT NULL,
  mem_IP varchar(15) DEFAULT NULL,
  mem_PostTime int(11) DEFAULT '0',
  mem_Alias varchar(255) DEFAULT NULL,
  mem_Intro text,
  mem_Articles int(11) DEFAULT '0',
  mem_Pages int(11) DEFAULT '0',
  mem_Comments int(11) DEFAULT '0',
  mem_Attachments int(11) DEFAULT '0',
  mem_Template varchar(50) DEFAULT NULL,
  mem_Meta text,
  PRIMARY KEY (mem_ID),
  KEY %pre%mem_Name (mem_Name)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS %pre%module (
  mod_ID int(11) NOT NULL AUTO_INCREMENT,
  mod_Name varchar(50) DEFAULT '',
  mod_FileName varchar(50) DEFAULT '',
  mod_Order int(11) DEFAULT '0',
  mod_Content text,
  mod_IsHidden bit(1) DEFAULT b'0',
  mod_SidebarID int(11) DEFAULT '0',
  mod_HtmlID varchar(50) DEFAULT '',
  mod_Ftype varchar(5) DEFAULT '',
  mod_MaxLi int(11) DEFAULT '0',
  mod_Source varchar(50) DEFAULT '',
  mod_ViewType varchar(50) DEFAULT '',
  mod_IsHideTitle bit(1) DEFAULT b'0',
  mod_Meta text,
  PRIMARY KEY (mod_ID)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS %pre%tag (
  tag_ID int(11) NOT NULL AUTO_INCREMENT,
  tag_Name varchar(255) DEFAULT '',
  tag_Alias varchar(255) DEFAULT NULL,
  tag_Order int(11) DEFAULT '0',
  tag_Count int(11) DEFAULT '0',
  tag_Intro text,  
  tag_Template varchar(50) DEFAULT NULL,
  tag_Meta text,
  PRIMARY KEY (tag_ID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS %pre%upload (
  ul_ID int(11) NOT NULL AUTO_INCREMENT,
  ul_AuthorID int(11) DEFAULT '0',
  ul_FileSize int(11) DEFAULT '0',
  ul_FileName varchar(255) DEFAULT '',
  ul_PostTime int(11) DEFAULT '0',
  ul_DownNum int(11) DEFAULT '0',
  ul_Intro text,
  ul_Meta text,
  PRIMARY KEY (ul_ID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
