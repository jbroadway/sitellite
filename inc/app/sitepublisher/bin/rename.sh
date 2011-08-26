#!/bin/sh

export SITE_PATH=$1
export NEW_EXT=$2

echo "Setting File Extensions: $NEW_EXT"

cd $SITE_PATH

# fix all references to /index/PAGE
for fl in `find . -name "*.html" -print`; do
	perl -pi -e "s|\.html|.$NEW_EXT|g" $fl
	NEW_NAME=`perl -e "\\$foo = '$fl'; \\$foo =~ s|\\.html\\$|.$NEW_EXT|; print \\$foo"`
	mv $fl $NEW_NAME
done

echo "Renaming completed."
