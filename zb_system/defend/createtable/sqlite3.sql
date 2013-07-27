CREATE TABLE %pre%log (
  log_ID integer primary key autoincrement,
  log_CateID integer NOT NULL DEFAULT 0,
  log_AuthorID integer NOT NULL DEFAULT 0,
  log_Tag varchar(255) NOT NULL DEFAULT '',
  log_Status integer NOT NULL DEFAULT 0,
  log_Type integer NOT NULL DEFAULT 0,
  log_Alias varchar(255) NOT NULL DEFAULT '',
  log_IsTop bit NOT NULL DEFAULT 0,
  log_IsLock bit NOT NULL DEFAULT 0,
  log_Title varchar(255) NOT NULL DEFAULT '',
  log_Intro Meta text NOT NULL DEFAULT '',
  log_Content Meta text NOT NULL DEFAULT '',
  log_IP varchar(15) NOT NULL DEFAULT '',
  log_PostTime integer NOT NULL DEFAULT 0,
  log_CommNums integer NOT NULL DEFAULT 0,
  log_ViewNums integer NOT NULL DEFAULT 0,
  log_Template varchar(50) NOT NULL DEFAULT '',
  log_Meta Meta text NOT NULL DEFAULT ''
);


CREATE TABLE %pre%category (
  cate_ID integer primary key autoincrement,
  cate_Name varchar(50) NOT NULL DEFAULT '',
  cate_Order integer NOT NULL DEFAULT 0,
  cate_Count integer NOT NULL DEFAULT 0,
  cate_Alias varchar(255) NOT NULL DEFAULT '',
  cate_Intro Meta text NOT NULL DEFAULT '',
  cate_RootID integer NOT NULL DEFAULT 0,
  cate_ParentID integer NOT NULL DEFAULT 0,
  cate_Template varchar(50) NOT NULL DEFAULT '',
  cate_LogTemplate varchar(50) NOT NULL DEFAULT '',
  cate_Meta Meta text NOT NULL DEFAULT ''
);


CREATE TABLE %pre%comment (
  comm_ID integer primary key autoincrement,
  comm_LogID integer NOT NULL DEFAULT 0,
  comm_IsCheck bit NOT NULL DEFAULT 0,
  comm_RootID integer NOT NULL DEFAULT 0,
  comm_ParentID integer NOT NULL DEFAULT 0,
  comm_AuthorID integer NOT NULL DEFAULT 0,
  comm_Author varchar(20) NOT NULL DEFAULT '',
  comm_Content Meta text NOT NULL DEFAULT '',
  comm_Email varchar(50) NOT NULL DEFAULT '',
  comm_HomePage varchar(255) NOT NULL DEFAULT '',
  comm_PostTime integer NOT NULL DEFAULT 0,
  comm_IP varchar(15) NOT NULL DEFAULT '',
  comm_Agent Meta text NOT NULL DEFAULT '',
  comm_Meta Meta text NOT NULL DEFAULT ''
);


CREATE TABLE %pre%config (
  conf_Name varchar(255) NOT NULL NOT NULL DEFAULT '',
  conf_Value text
);


CREATE TABLE %pre%counter (
  coun_ID integer primary key autoincrement,
  coun_MemID integer NOT NULL DEFAULT 0,
  coun_IP varchar(15) NOT NULL DEFAULT '',
  coun_Agent Meta text NOT NULL DEFAULT '',
  coun_Refer varchar(255) NOT NULL DEFAULT '',
  coun_Title varchar(255) NOT NULL DEFAULT '',
  coun_PostTime integer NOT NULL DEFAULT 0,
  coun_Description Meta text NOT NULL DEFAULT '',
  coun_PostData Meta text NOT NULL DEFAULT '',
  coun_AllRequestHeader text
);


CREATE TABLE %pre%member (
  mem_ID integer primary key autoincrement,
  mem_Guid varchar(36) NOT NULL DEFAULT '',
  mem_Level integer NOT NULL DEFAULT 0,
  mem_Status integer NOT NULL DEFAULT 0,
  mem_Name varchar(20) NOT NULL DEFAULT '',
  mem_Password varchar(32) NOT NULL DEFAULT '',
  mem_Email varchar(50) NOT NULL DEFAULT '',
  mem_HomePage varchar(255) NOT NULL DEFAULT '',
  mem_IP varchar(15) NOT NULL DEFAULT '',
  mem_PostTime integer NOT NULL DEFAULT 0,
  mem_Alias varchar(255) NOT NULL DEFAULT '',
  mem_Intro Meta text NOT NULL DEFAULT '',
  mem_Articles integer NOT NULL DEFAULT 0,
  mem_Pages integer NOT NULL DEFAULT 0,
  mem_Comments integer NOT NULL DEFAULT 0,
  mem_Uploads integer NOT NULL DEFAULT 0,
  mem_Template varchar(50) NOT NULL DEFAULT '',  
  mem_Meta Meta text NOT NULL DEFAULT ''
);


CREATE TABLE %pre%module (
  mod_ID integer primary key autoincrement,
  mod_Name varchar(50) NOT NULL DEFAULT '',
  mod_FileName varchar(50) NOT NULL DEFAULT '',
  mod_Order integer NOT NULL DEFAULT 0,
  mod_Content Meta text NOT NULL DEFAULT '',
  mod_IsHidden bit NOT NULL DEFAULT b0,
  mod_SidebarID integer NOT NULL DEFAULT 0,
  mod_HtmlID varchar(50) NOT NULL DEFAULT '',
  mod_Type varchar(5) NOT NULL DEFAULT '',
  mod_MaxLi integer NOT NULL DEFAULT 0,
  mod_Source varchar(50) NOT NULL DEFAULT '',
  mod_ViewType varchar(50) NOT NULL DEFAULT '',
  mod_IsHideTitle bit NOT NULL DEFAULT b0,
  mod_Meta Meta text NOT NULL DEFAULT ''
);


CREATE TABLE %pre%tag (
  tag_ID integer primary key autoincrement,
  tag_Name varchar(255) NOT NULL DEFAULT '',
  tag_Alias varchar(255) NOT NULL DEFAULT '',
  tag_Order integer NOT NULL DEFAULT 0,
  tag_Count integer NOT NULL DEFAULT 0,
  tag_Intro Meta text NOT NULL DEFAULT '',
  tag_Template varchar(50) NOT NULL DEFAULT '',
  tag_Meta Meta text NOT NULL DEFAULT ''
);


CREATE TABLE %pre%upload (
  ul_ID integer primary key autoincrement,
  ul_AuthorID integer NOT NULL DEFAULT 0,
  ul_FileSize integer NOT NULL DEFAULT 0,
  ul_FileName varchar(255) NOT NULL DEFAULT '',
  ul_PostTime integer NOT NULL DEFAULT 0,
  ul_DownNum integer NOT NULL DEFAULT 0,
  ul_Intro Meta text NOT NULL DEFAULT '',
  ul_Meta Meta text NOT NULL DEFAULT ''
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
