#!/bin/sh
find public_html -name '*.php' -exec chmod 640 {} \;
find internal name '*.php' -exec chmod 600 {} \;
