#!/bin/bash

if [ "$#" -ne 1 ]; then
    echo "Usage: $0 <version>"
    exit 1
fi

version=$1

git clone git@github.com:vthwang/wp-json-exporter.git
cd wp-json-exporter || exit

# Remove unnecessary files
rm -rf .git .idea .github .gitignore phpcs.xml Release.md release.sh

cd ..
zip -r "wp-json-exporter-$version.zip" wp-json-exporter
rm -rf "wp-json-exporter"