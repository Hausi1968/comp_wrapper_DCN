#!/usr/bin/env bash
# Packs the component into an installable Joomla zip under dist/
set -euo pipefail
cd "$(dirname "$0")"
version=$(grep -oP '(?<=<version>)[^<]+' com_compwrapper/compwrapper.xml)
mkdir -p dist
rm -f "dist/com_compwrapper-$version.zip"
(cd com_compwrapper && zip -qr "../dist/com_compwrapper-$version.zip" . -x '.*')
echo "dist/com_compwrapper-$version.zip"
