#
# $Id: mysql.sql,v 1.1 2006/02/23 12:38:43 nobu Exp $
#

CREATE TABLE shortcut (
  cutid varchar(16) PRIMARY KEY,
  mdate   integer default 0,
  url varchar(255) NOT NULL default '',
  active int(1) default 1,
  refer   integer default 0
);
