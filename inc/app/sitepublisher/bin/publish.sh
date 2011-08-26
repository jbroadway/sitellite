#!/bin/sh

export DYNAMIC_SITE=$1
export SITE_PATH=$2

echo "Dynamic Site: http://$DYNAMIC_SITE"
echo "Static Site Path: $SITE_PATH"

cd $SITE_PATH

wget -m -w 2 -p -E -k $DYNAMIC_SITE

# fix all references to /index/PAGE
for fl in `find . -name "*.html" -print`; do
	perl -pi -e "s|http://$DYNAMIC_SITE/index/([a-zA-Z0-9_-]+)|/index/\$1.html|g" $fl
done

# fix all forms
for fl in `find . -name "*.html" -print`; do
	perl -pi -e "s|action=\"([^\"]+)\"|action=\"http://$DYNAMIC_SITE\$1\"|g" $fl
done

echo "Web site published."
