CREATE TABLE %pre%log (
  log_ID integer primary key autoincrement,
  log_CateID integer DEFAULT 0,
  log_AuthorID integer DEFAULT 0,
  log_Tag varchar(255) DEFAULT NULL,
  log_Status integer DEFAULT 0,
  log_Type integer DEFAULT 0,
  log_Alias varchar(255) DEFAULT '',
  log_IsTop bit DEFAULT 0,
  log_IsLock bit DEFAULT 0,
  log_Title varchar(255) DEFAULT '',
  log_Intro text,
  log_Content text,
  log_IP varchar(15) DEFAULT '',
  log_PostTime integer DEFAULT 0,
  log_CommNums integer DEFAULT 0,
  log_ViewNums integer DEFAULT 0,
  log_Template varchar(50) DEFAULT '',
  log_Meta text
);


CREATE TABLE %pre%category (
  cate_ID integer primary key autoincrement,
  cate_Name varchar(50) DEFAULT '',
  cate_Order integer DEFAULT 0,
  cate_Count integer DEFAULT 0,
  cate_Alias varchar(255) DEFAULT NULL,
  cate_Intro text,
  cate_RootID integer DEFAULT 0,
  cate_ParentID integer DEFAULT 0,
  cate_Template varchar(50) DEFAULT NULL,
  cate_LogTemplate varchar(50) DEFAULT NULL,
  cate_Meta text
);


CREATE TABLE %pre%comment (
  comm_ID integer primary key autoincrement,
  comm_LogID integer DEFAULT 0,
  comm_IsCheck bit DEFAULT 0,
  comm_RootID integer DEFAULT 0,
  comm_ParentID integer DEFAULT 0,
  comm_AuthorID integer DEFAULT 0,
  comm_Author varchar(20) DEFAULT '',
  comm_Content text,
  comm_Email varchar(50) DEFAULT '',
  comm_HomePage varchar(255) DEFAULT '',
  comm_PostTime integer DEFAULT 0,
  comm_IP varchar(15) DEFAULT '',
  comm_Agent text,
  comm_Meta text
);


CREATE TABLE %pre%config (
  conf_Name varchar(255) NOT NULL DEFAULT '',
  conf_Value text
);


CREATE TABLE %pre%counter (
  coun_ID integer primary key autoincrement,
  coun_MemID integer DEFAULT 0,
  coun_IP varchar(15) DEFAULT '',
  coun_Agent text,
  coun_Refer varchar(255) DEFAULT '',
  coun_Title varchar(255) DEFAULT NULL,
  coun_PostTime integer DEFAULT 0,
  coun_Description text,
  coun_PostData text,
  coun_AllRequestHeader text
);


CREATE TABLE %pre%member (
  mem_ID integer primary key autoincrement,
  mem_Guid varchar(36) DEFAULT '',
  mem_Level integer DEFAULT 0,
  mem_Status integer DEFAULT 0,
  mem_Name varchar(20) DEFAULT NULL,
  mem_Password varchar(32) DEFAULT NULL,
  mem_Email varchar(50) DEFAULT NULL,
  mem_HomePage varchar(255) DEFAULT NULL,
  mem_IP varchar(15) DEFAULT NULL,
  mem_PostTime integer DEFAULT 0,
  mem_Alias varchar(255) DEFAULT NULL,
  mem_Intro text,
  mem_Articles integer DEFAULT 0,
  mem_Pages integer DEFAULT 0,
  mem_Comments integer DEFAULT 0,
  mem_Uploads integer DEFAULT 0,
  mem_Template varchar(50) DEFAULT NULL,  
  mem_Meta text
);


CREATE TABLE %pre%module (
  mod_ID integer primary key autoincrement,
  mod_Name varchar(50) DEFAULT '',
  mod_FileName varchar(50) DEFAULT '',
  mod_Order integer DEFAULT 0,
  mod_Content text,
  mod_IsHidden bit DEFAULT b0,
  mod_SidebarID integer DEFAULT 0,
  mod_HtmlID varchar(50) DEFAULT '',
  mod_Ftype varchar(5) DEFAULT '',
  mod_MaxLi integer DEFAULT 0,
  mod_Source varchar(50) DEFAULT '',
  mod_ViewType varchar(50) DEFAULT '',
  mod_IsHideTitle bit DEFAULT b0,
  mod_Meta text
);


CREATE TABLE %pre%tag (
  tag_ID integer primary key autoincrement,
  tag_Name varchar(255) DEFAULT '',
  tag_Alias varchar(255) DEFAULT NULL,
  tag_Order integer DEFAULT 0,
  tag_Count integer DEFAULT 0,
  tag_Intro text,
  tag_Template varchar(50) DEFAULT NULL,
  tag_Meta text
);


CREATE TABLE %pre%upload (
  ul_ID integer primary key autoincrement,
  ul_AuthorID integer DEFAULT 0,
  ul_FileSize integer DEFAULT 0,
  ul_FileName varchar(255) DEFAULT '',
  ul_PostTime integer DEFAULT 0,
  ul_DownNum integer DEFAULT 0,
  ul_Intro text,
  ul_Meta text
);

CREATE INDEX %pre%log_PostTime on %pre%Log (log_PostTime);
CREATE INDEX %pre%comm_PostTime on %pre%Comment (comm_PostTime);
CREATE INDEX %pre%mem_Name on %pre%Member (mem_Name);
CREATE UNIQUE INDEX %pre%tag_ID on %pre%Tag (tag_ID);
CREATE UNIQUE INDEX %pre%log_ID on %pre%Log (log_ID);
CREATE UNIQUE INDEX %pre%cate_ID on %pre%Category (cate_ID);
CREATE UNIQUE INDEX %pre%comm_ID on %pre%Comment (comm_ID);
CREATE UNIQUE INDEX %pre%ul_ID on %pre%UpLoad (ul_ID);
CREATE UNIQUE INDEX %pre%mem_ID on %pre%Member (mem_ID);
CREATE UNIQUE INDEX %pre%mod_ID on %pre%Module (mod_ID);
