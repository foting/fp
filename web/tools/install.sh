#!/bin/bash

# Default install directory
INSTALL_DIR="$HOME/public_html/"

if [ $# -ge 1 ]; then
    INSTALL_DIR="$1"
fi

function install_file {
    src="$1"; shift
    dst="$1"; shift

    echo "Installing $src in $dst"
    install -m 0644 $src $dst
}

install_file "index.html"                   "$INSTALL_DIR"

install_file "./common/login.php"           "$INSTALL_DIR/common/"
install_file "./common/logout.php"          "$INSTALL_DIR/common/"
install_file "./common/fpdb.php"            "$INSTALL_DIR/common/"
install_file "./common/snapshot_hack.php"   "$INSTALL_DIR/common/"

install_file "./include/credentials.php"    "$INSTALL_DIR/include/"

install_file "./user/header.php"            "$INSTALL_DIR/user/"
install_file "./user/buy_beers.php"         "$INSTALL_DIR/user/"
install_file "./user/iou.php"               "$INSTALL_DIR/user/"
install_file "./user/welcome.php"           "$INSTALL_DIR/user/"

install_file "./admin/header.php"           "$INSTALL_DIR/admin/"
install_file "./admin/add_user.php"         "$INSTALL_DIR/admin/"
install_file "./admin/inventory.php"        "$INSTALL_DIR/admin/"
install_file "./admin/iou.php"              "$INSTALL_DIR/admin/"
install_file "./admin/payment.php"          "$INSTALL_DIR/admin/"
install_file "./admin/purchase.php"         "$INSTALL_DIR/admin/"
install_file "./admin/reg_beers.php"        "$INSTALL_DIR/admin/"
install_file "./admin/welcome.php"          "$INSTALL_DIR/admin/"
