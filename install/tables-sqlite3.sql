/* exported with .dump from SQLite
*  Added tables
*          sftphostkeys REMOVED: Functionality is hardly if ever used thus not implemented 
*          sftpuserkeys REMOVED: Functionality already conrtained in 'users' table 
*          login_history
*          user_groups   
* (Not sure if they will be used right now, but when already present easier to do )
* removed MySQL type backticks from filednames
* When exporting or copying pasting make sure to use Linux type line endings
*/
PRAGMA foreign_keys=OFF;
BEGIN TRANSACTION;
CREATE TABLE groups (
  groups_pk INTEGER PRIMARY KEY,
  groupname VARCHAR(32) UNIQUE NOT NULL default '',
  gid UNSIGNED SMALLINT(6) UNIQUE NOT NULL,
  members VARCHAR(255) NOT NULL default ''
);
CREATE TABLE users (
  users_pk INTEGER PRIMARY KEY,
  userid VARCHAR(64) UNIQUE NOT NULL default '',
  uid UNSIGNED SMALLINT(6) default NULL,
  ugid UNSIGNED SMALLINT(6) default NULL,
  passwd VARCHAR(265) NOT NULL default '',
  homedir VARCHAR(255) NOT NULL default '',
  comment VARCHAR(255) NOT NULL default '',
  disabled UNSIGNED SMALLINT(2) NOT NULL default '0',
  shell VARCHAR(32) NOT NULL default '/bin/false',
  sshpubkey VARCHAR(1023) NOT NULL default '',
  email VARCHAR(255) NOT NULL default '',
  name VARCHAR(255) NOT NULL default '',
  title VARCHAR(5) NOT NULL default '',
  company VARCHAR(255) NOT NULL default '',
  bytes_in_used UNSIGNED BIGINT(20) NOT NULL default '0',
  bytes_out_used UNSIGNED BIGINT(20) NOT NULL default '0',
  files_in_used UNSIGNED BIGINT(20) NOT NULL default '0',
  files_out_used UNSIGNED BIGINT(20) NOT NULL default '0',
  login_count UNSIGNED INT(11) NOT NULL default '0',
  last_login DATETIME NOT NULL default '0000-00-00 00:00:00',
  last_modified DATETIME NOT NULL default '0000-00-00 00:00:00',
  expiration DATETIME NOT NULL default '0000-00-00 00:00:00'
);
CREATE TABLE login_history (
    username TEXT NOT NULL,
    client_ip TEXT NOT NULL,
    server_ip TEXT NOT NULL,
    protocol TEXT NOT NULL,
    login_time DATETIME
);
CREATE TABLE user_groups (
	gkid INTEGER NOT NULL,
	groupid INTEGER NOT NULL,
	userid INTEGER NOT NULL,
	groups_pk INTEGER,
	users_pk INTEGER NOT NULL,
	CONSTRAINT user_groups_pk PRIMARY KEY (gkid),
	CONSTRAINT user_groups_groups_FK FOREIGN KEY (groups_pk) REFERENCES groups(groups_pk),
	CONSTRAINT user_groups_users_FK FOREIGN KEY (users_pk) REFERENCES users(users_pk)
);
CREATE TABLE tmpLinuxUsers (
  users_pk INTEGER PRIMARY KEY,
  userid VARCHAR(64) UNIQUE NOT NULL,
  passwd VARCHAR(265) NOT NULL default '',
  uid UNSIGNED SMALLINT(6) NOT NULL,
  ugid UNSIGNED SMALLINT(6) default NULL,
  comment VARCHAR(255) NOT NULL default '',  
  homedir VARCHAR(255) NOT NULL default '',
  shell VARCHAR(32) NOT NULL default '/bin/false'
);
CREATE UNIQUE INDEX groupname ON groups (groupname);
CREATE UNIQUE INDEX userid ON users (userid);
CREATE VIEW
check_users
AS
SELECT userid, uid, users_pk  FROM 
users u 
UNION SELECT userid, uid, null
FROM
tmpLinuxUsers
CREATE VIEW New_Linux_Users AS
SELECT u.userid as 'name', u.passwd,u.uid, u.gid,u.comment as gecos,u.homedir,u.shell 
FROM users u
LEFT JOIN tmpLinuxUsers tlu
ON tlu.uid = u.uid 
WHERE tlu.uid IS NULL;
CREATE VIEW check_groups
AS
SELECT groupspk, groupname, gid, members
FROM groups;
CREATE UNIQUE INDEX user_groups_users_pk_IDX ON user_groups (users_pk,groups_pk);
CREATE UNIQUE INDEX user_groups_userid_IDX ON user_groups (userid,groupid);
COMMIT;
