#!/bin/bash

echo "🚀 Starting the script..."

if [ "$#" -ne 1 ]; then
    echo "Usage: $0 <version>"
    exit 1
fi

version=$1
echo "✅ Version specified: $version"

echo "📥 Cloning the repository..."
git clone git@github.com:vthwang/wp-json-exporter.git
cd wp-json-exporter || exit

echo "🧹 Removing unnecessary files..."
# Remove unnecessary files
rm -rf .git .idea .github .gitignore phpcs.xml Release.md release.sh

cd ..
echo "📦 Creating zip file..."
zip -r "wp-json-exporter-$version.zip" wp-json-exporter -x "*.DS_Store" -x "MACOSX"

echo "🧽 Cleaning up..."
rm -rf "wp-json-exporter"

echo "🎉 Script completed successfully."