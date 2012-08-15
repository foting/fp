#!/bin/bash

URL="http://www.systembolaget.se/Assortment.aspx?Format=Xml"
XML_FILE="sbl-`date '+20%y-%m-%d'`.xml"
XML_LINK="sbl-latest.xml"

function print_and_exit {
    msg="$1"; shift

    echo "Error: $msg"
    exit 1;
}

wget $URL -O $XML_FILE
[ "$?" == "0" ] || print_and_exit "wget"

rm sbl-latest.xml
ln -s $XML_FILE sbl-latest.xml

sed -e "s#\(<[^>]*>\)#\1\n#g" -i $XML_FILE
[ "$?" == "0" ] || print_and_exit "sed"

