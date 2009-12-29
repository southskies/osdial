# Quick access to the mysql database using the connection parameters in /etc/osdial.conf
if [ "`/usr/bin/id -u`" = "0" ]; then
 alias mysqlo="mysql \
  --host=\"\`grep '^VARDB_server' /etc/osdial.conf | awk '{ print \$3 }'\`\" \
  --port=\"\`grep '^VARDB_port' /etc/osdial.conf | awk '{ print \$3 }'\`\" \
  --user=\"\`grep '^VARDB_user' /etc/osdial.conf | awk '{ print \$3 }'\`\" \
  --password=\"\`grep '^VARDB_pass' /etc/osdial.conf | awk '{ print \$3 }'\`\" \
  --database=\"\`grep '^VARDB_database' /etc/osdial.conf | awk '{ print \$3 }'\`\" \
  --prompt=\"mysql.\d> \""
fi
