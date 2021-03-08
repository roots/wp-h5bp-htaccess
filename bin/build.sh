#!/bin/bash

declare H5BP_CONFIGS_VERSION
H5BP_CONFIGS_VERSION=3.0.0

declare temp_dir
temp_dir="$(dirname "$(dirname "$0")")/temp"

mkdir -p "$temp_dir"

curl "https://github.com/h5bp/server-configs-apache/archive/${H5BP_CONFIGS_VERSION}.zip" -L -o "$temp_dir/h5bp.zip"
unzip -qo "$temp_dir/h5bp.zip" -d "$temp_dir"

command "$temp_dir/server-configs-apache-${H5BP_CONFIGS_VERSION}/bin/build.sh" h5bp-htaccess.conf bin/wp-h5bp-htaccess.conf
