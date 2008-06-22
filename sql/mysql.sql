#
# $Id: mysql.sql,v 1.2 2008/06/22 08:28:40 nobu Exp $
#

CREATE TABLE shortcut (
  scid    integer PRIMARY KEY,
  cutid   varchar(16) UNIQUE KEY,
  pscref  integer NOT NULL default 0,
  uid     integer NOT NULL default 0,
  mdate   integer default 0,
  title   varchar(80) NOT NULL default '',
  description text NOT NULL default '',
  url     text NOT NULL default '',
  active  int(1) default 1,	-- 0:disable, 1:public, 2:private
  refer   integer default 0,
  weight  integer default 0
);
