#!/bin/sh

DB='pbxmon'
USER='root'
PASS='123456'
BEGIN=$(date +'%Y-%m-%d 08:00:00')
END=$(date +'%Y-%m-%d 20:00:00')
SQL="INSERT INTO report(value, create_time) SELECT count(id),now() FROM cdr WHERE create_time BETWEEN '${BEGIN}' AND '${END}'"

/usr/bin/mysql -u ${USER} -p${PASS} ${DB} -e "${SQL}" &>> /tmp/report.log
