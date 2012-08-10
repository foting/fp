#!/bin/bash

# Default install directory
INSTALL_DIR="$HOME/public_html/fp/"

if [ $# -ge 1 ]; then
    INSTALL_DIR="$1"
fi

function install_file {
    src="$1"; shift
    dst="$1"; shift

    echo "Installing $src in $dst"
    install -m 0644 $src $dst
}

install_file "index.html" $INSTALL_DIR
install_file "login.php" $INSTALL_DIR
install_file "user_header.php" $INSTALL_DIR
install_file "user_purchase.php" $INSTALL_DIR
install_file "user_iou.php" $INSTALL_DIR
install_file "admin_header.php" $INSTALL_DIR
install_file "admin_dashboard.php" $INSTALL_DIR

