#!/bin/bash
set -ev

if [ $# -lt 3 ]; then
	echo "usage: $0 <FTP-USER> <FTP-PW> <FTP-DIR>"
	exit 1
fi

FTP_USER=$1
FTP_PW=$2
FTP_DIR=$3

if [ "${DEPLOY_AFTER}" = "false" ]; then
    curl --ftp-create-dirs -T ./dist/muneco.zip -u $FTP_USER:$FTP_PW ftp://$FTP_DIR/muneco.zip;
fi;