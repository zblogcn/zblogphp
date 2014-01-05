CREATE TABLE IF NOT EXISTS %pre%post (
  log_ID serial,
  log_CateID smallint NOT NULL DEFAULT '0',
  log_AuthorID integer NOT NULL DEFAULT '0',
  log_Tag varchar(255) NOT NULL DEFAULT '',
  log_Status smallint NOT NULL DEFAULT '0',
  log_Type smallint NOT NULL DEFAULT '0',
  log_Alias varchar(255) NOT NULL DEFAULT '',
  log_IsTop boolean NOT NULL DEFAULT '0',
  log_IsLock boolean NOT NULL DEFAULT '0',
  log_Title varchar(255) NOT NULL DEFAULT '',
  log_Intro text NOT NULL,
  log_Content text NOT NULL,
  log_PostTime integer NOT NULL DEFAULT '0',
  log_CommNums integer NOT NULL DEFAULT '0',
  log_ViewNums integer NOT NULL DEFAULT '0',
  log_Template varchar(50) NOT NULL DEFAULT '',
  log_Meta text NOT NULL,
  PRIMARY KEY (log_ID)
);

CREATE INDEX %pre%log_PT ON %pre%post (log_PostTime);
CREATE INDEX %pre%log_TISC ON %pre%post (log_Type,log_IsTop,log_Status,log_CateID);


CREATE TABLE IF NOT EXISTS %pre%category (
  cate_ID serial,
  cate_Name varchar(50) NOT NULL DEFAULT '',
  cate_Order integer NOT NULL DEFAULT '0',
  cate_Count integer NOT NULL DEFAULT '0',
  cate_Alias varchar(255) NOT NULL DEFAULT '',
  cate_Intro text NOT NULL,
  cate_RootID integer NOT NULL DEFAULT '0',
  cate_ParentID integer NOT NULL DEFAULT '0',
  cate_Template varchar(50) NOT NULL DEFAULT '',
  cate_LogTemplate varchar(50) NOT NULL DEFAULT '',
  cate_Meta text NOT NULL,
  PRIMARY KEY (cate_ID)
);


CREATE TABLE IF NOT EXISTS %pre%comment (
  comm_ID serial,
  comm_LogID integer NOT NULL DEFAULT '0',
  comm_IsChecking boolean NOT NULL DEFAULT '0',
  comm_RootID integer NOT NULL DEFAULT '0',
  comm_ParentID integer NOT NULL DEFAULT '0',
  comm_AuthorID integer NOT NULL DEFAULT '0',
  comm_Name varchar(20) NOT NULL DEFAULT '',
  comm_Email varchar(50) NOT NULL DEFAULT '',
  comm_HomePage varchar(255) NOT NULL DEFAULT '',
  comm_Content text NOT NULL,
  comm_PostTime integer NOT NULL DEFAULT '0',
  comm_IP varchar(15) NOT NULL DEFAULT '',
  comm_Agent text NOT NULL,
  comm_Meta text NOT NULL,
  PRIMARY KEY (comm_ID)
);

CREATE INDEX %pre%comm_RIL ON %pre%comment (comm_RootID,comm_IsChecking,comm_LogID);

CREATE TABLE IF NOT EXISTS %pre%config (
  conf_Name varchar(255) NOT NULL DEFAULT '',
  conf_Value text
);


CREATE TABLE IF NOT EXISTS %pre%counter (
  coun_ID serial,
  coun_MemID integer NOT NULL DEFAULT '0',
  coun_IP varchar(15) NOT NULL DEFAULT '',
  coun_Agent text NOT NULL,
  coun_Refer varchar(255) NOT NULL DEFAULT '',
  coun_Title varchar(255) NOT NULL DEFAULT '',
  coun_PostTime integer NOT NULL DEFAULT '0',
  coun_Description text NOT NULL,
  coun_PostData text NOT NULL,
  coun_AllRequestHeader text NOT NULL,
  PRIMARY KEY (coun_ID)
);


CREATE TABLE IF NOT EXISTS %pre%member (
  mem_ID serial,
  mem_Guid varchar(36) NOT NULL DEFAULT '',
  mem_Level smallint NOT NULL DEFAULT '0',
  mem_Status smallint NOT NULL DEFAULT '0',
  mem_Name varchar(20) NOT NULL DEFAULT '',
  mem_Password varchar(32) NOT NULL DEFAULT '',
  mem_Email varchar(50) NOT NULL DEFAULT '',
  mem_HomePage varchar(255) NOT NULL DEFAULT '',
  mem_IP varchar(15) NOT NULL DEFAULT '',
  mem_PostTime integer NOT NULL DEFAULT '0',
  mem_Alias varchar(255) NOT NULL DEFAULT '',
  mem_Intro text NOT NULL,
  mem_Articles integer NOT NULL DEFAULT '0',
  mem_Pages integer NOT NULL DEFAULT '0',
  mem_Comments integer NOT NULL DEFAULT '0',
  mem_Uploads integer NOT NULL DEFAULT '0',
  mem_Template varchar(50) NOT NULL DEFAULT '',
  mem_Meta text NOT NULL,
  PRIMARY KEY (mem_ID)
);

CREATE INDEX %pre%mem_Name ON %pre%member (mem_Name);


CREATE TABLE IF NOT EXISTS %pre%module (
  mod_ID serial,
  mod_Name varchar(100) NOT NULL DEFAULT '',
  mod_FileName varchar(50) NOT NULL DEFAULT '',
  mod_Content text NOT NULL,
  mod_SidebarID integer NOT NULL DEFAULT '0',
  mod_HtmlID varchar(50) NOT NULL DEFAULT '',
  mod_Type varchar(5) NOT NULL DEFAULT '',
  mod_MaxLi integer NOT NULL DEFAULT '0',
  mod_Source varchar(50) NOT NULL DEFAULT '',
  mod_IsHideTitle boolean NOT NULL DEFAULT '0',
  mod_Meta text NOT NULL,
  PRIMARY KEY (mod_ID)
);


CREATE TABLE IF NOT EXISTS %pre%tag (
  tag_ID serial,
  tag_Name varchar(255) NOT NULL DEFAULT '',
  tag_Order integer NOT NULL DEFAULT '0',
  tag_Count integer NOT NULL DEFAULT '0',
  tag_Alias varchar(255) NOT NULL DEFAULT '', 
  tag_Intro text NOT NULL,  
  tag_Template varchar(50) NOT NULL DEFAULT '',
  tag_Meta text NOT NULL,
  PRIMARY KEY (tag_ID)
);


CREATE TABLE IF NOT EXISTS %pre%upload (
  ul_ID serial,
  ul_AuthorID integer NOT NULL DEFAULT '0',
  ul_Size integer NOT NULL DEFAULT 0,
  ul_Name varchar(255) NOT NULL DEFAULT '',
  ul_SourceName varchar(255) NOT NULL DEFAULT '',
  ul_MimeType varchar(50) NOT NULL DEFAULT '',
  ul_PostTime integer NOT NULL DEFAULT '0',
  ul_DownNums integer NOT NULL DEFAULT '0',
  ul_LogID integer NOT NULL DEFAULT '0',  
  ul_Intro text NOT NULL,
  ul_Meta text NOT NULL,
  PRIMARY KEY (ul_ID)
);
