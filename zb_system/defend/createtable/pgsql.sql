CREATE SEQUENCE %pre%post_seq;
CREATE TABLE %pre%post (
 log_ID INT NOT NULL DEFAULT nextval('%pre%post_seq'),
 log_CateID smallint NOT NULL DEFAULT '0',
 log_AuthorID integer NOT NULL DEFAULT '0',
 log_Tag varchar(255) NOT NULL DEFAULT '',
 log_Status smallint NOT NULL DEFAULT '0',
 log_Type smallint NOT NULL DEFAULT '0',
 log_Alias varchar(255) NOT NULL DEFAULT '',
 log_IsTop char(1) NOT NULL DEFAULT '0',
 log_IsLock char(1) NOT NULL DEFAULT '0',
 log_Title varchar(255) NOT NULL DEFAULT '',
 log_Intro text NOT NULL,
 log_Content text NOT NULL,
 log_PostTime integer NOT NULL DEFAULT '0',
 log_CommNums integer NOT NULL DEFAULT '0',
 log_ViewNums integer NOT NULL DEFAULT '0',
 log_Template varchar(50) NOT NULL DEFAULT '',
 log_Meta text NOT NULL,
  PRIMARY KEY (log_ID)
) ;
CREATE INDEX %pre%post_ix_id ON %pre%post(log_ID);
CREATE INDEX %pre%post_ix_pt ON %pre%post(log_PostTime);
CREATE INDEX %pre%post_ix_tpisc ON %pre%post(log_Type,log_PostTime,log_IsTop,log_Status,log_CateID);
CREATE INDEX %pre%post_ix_vtsc ON %pre%post(log_ViewNums,log_Type,log_Status,log_CateID);

CREATE SEQUENCE %pre%category_seq;
CREATE TABLE %pre%category (
 cate_ID INT NOT NULL DEFAULT nextval('%pre%category_seq'),
 cate_Name varchar(255) NOT NULL DEFAULT '',
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
) ;
CREATE INDEX %pre%category_ix_id ON %pre%category(cate_ID);

CREATE SEQUENCE %pre%comment_seq;
CREATE TABLE %pre%comment (
 comm_ID INT NOT NULL DEFAULT nextval('%pre%comment_seq'),
 comm_LogID integer NOT NULL DEFAULT '0',
 comm_IsChecking char(1) NOT NULL DEFAULT '0',
 comm_RootID integer NOT NULL DEFAULT '0',
 comm_ParentID integer NOT NULL DEFAULT '0',
 comm_AuthorID integer NOT NULL DEFAULT '0',
 comm_Name varchar(50) NOT NULL DEFAULT '',
 comm_Email varchar(50) NOT NULL DEFAULT '',
 comm_HomePage varchar(255) NOT NULL DEFAULT '',
 comm_Content text NOT NULL,
 comm_PostTime integer NOT NULL DEFAULT '0',
 comm_IP varchar(15) NOT NULL DEFAULT '',
 comm_Agent text NOT NULL,
 comm_Meta text NOT NULL,
  PRIMARY KEY (comm_ID)
) ;
CREATE INDEX %pre%comment_ix_id ON %pre%comment(comm_ID);

CREATE SEQUENCE %pre%config_seq;
CREATE TABLE %pre%config (
 conf_ID INT NOT NULL DEFAULT nextval('%pre%config_seq'),
 conf_Name varchar(255) NOT NULL DEFAULT '',
 conf_Value text,
  PRIMARY KEY (conf_ID)
) ;
CREATE INDEX %pre%config_ix_id ON %pre%config(conf_ID);

CREATE SEQUENCE %pre%member_seq;
CREATE TABLE %pre%member (
 mem_ID INT NOT NULL DEFAULT nextval('%pre%member_seq'),
 mem_Guid varchar(36) NOT NULL DEFAULT '',
 mem_Level smallint NOT NULL DEFAULT '0',
 mem_Status smallint NOT NULL DEFAULT '0',
 mem_Name varchar(50) NOT NULL DEFAULT '',
 mem_Password varchar(32) NOT NULL DEFAULT '',
 mem_Email varchar(50) NOT NULL DEFAULT '',
 mem_HomePage varchar(255) NOT NULL DEFAULT '',
 mem_IP varchar(15) NOT NULL DEFAULT '',
 mem_PostTime integer NOT NULL DEFAULT '0',
 mem_Alias varchar(50) NOT NULL DEFAULT '',
 mem_Intro text NOT NULL,
 mem_Articles integer NOT NULL DEFAULT '0',
 mem_Pages integer NOT NULL DEFAULT '0',
 mem_Comments integer NOT NULL DEFAULT '0',
 mem_Uploads integer NOT NULL DEFAULT '0',
 mem_Template varchar(50) NOT NULL DEFAULT '',
 mem_Meta text NOT NULL,
  PRIMARY KEY (mem_ID)
) ;
CREATE INDEX %pre%member_ix_id ON %pre%member(mem_ID);
CREATE INDEX %pre%member_ix_name ON %pre%member(mem_Name);
CREATE INDEX %pre%member_ix_alias ON %pre%member(mem_Alias);

CREATE SEQUENCE %pre%module_seq;
CREATE TABLE %pre%module (
 mod_ID INT NOT NULL DEFAULT nextval('%pre%module_seq'),
 mod_Name varchar(255) NOT NULL DEFAULT '',
 mod_FileName varchar(50) NOT NULL DEFAULT '',
 mod_Content text NOT NULL,
 mod_SidebarID integer NOT NULL DEFAULT '0',
 mod_HtmlID varchar(50) NOT NULL DEFAULT '',
 mod_Type varchar(5) NOT NULL DEFAULT '',
 mod_MaxLi integer NOT NULL DEFAULT '0',
 mod_Source varchar(50) NOT NULL DEFAULT '',
 mod_IsHideTitle char(1) NOT NULL DEFAULT '0',
 mod_Meta text NOT NULL,
  PRIMARY KEY (mod_ID)
) ;
CREATE INDEX %pre%module_ix_id ON %pre%module(mod_ID);

CREATE SEQUENCE %pre%tag_seq;
CREATE TABLE %pre%tag (
  tag_ID INT NOT NULL DEFAULT nextval('%pre%tag_seq'),
  tag_Name varchar(255) NOT NULL DEFAULT '',
  tag_Order integer NOT NULL DEFAULT '0',
  tag_Count integer NOT NULL DEFAULT '0',
  tag_Alias varchar(255) NOT NULL DEFAULT '', 
  tag_Intro text NOT NULL,  
  tag_Template varchar(50) NOT NULL DEFAULT '',
  tag_Meta text NOT NULL,
  PRIMARY KEY (tag_ID)
) ;
CREATE INDEX %pre%tag_ix_id ON %pre%tag(tag_ID);

CREATE SEQUENCE %pre%upload_seq;
CREATE TABLE %pre%upload (
 ul_ID INT NOT NULL DEFAULT nextval('%pre%upload_seq'),
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
) ;
CREATE INDEX %pre%upload_ix_id ON %pre%upload(ul_ID);