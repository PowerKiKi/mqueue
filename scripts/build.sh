#!/usr/bin/env bash

php scripts/update_database.php

echo "Compiling CSS..."
compass compile -s compressed --force

echo "Compiling JavaScript..."
cd public/js
mkdir -p min
for file in *.js ; do
	
	# Discard warnings for third party code
	if [[ $file =~ jquery|jcarousellite ]]
	then
		thirdparty="--third_party --warning_level QUIET"
	else
		thirdparty=""
	fi

	echo "$file"
	java -jar ../../library/closure-compiler/compiler.jar --compilation_level SIMPLE_OPTIMIZATIONS  --js "$file" --js_output_file "min/$file" $thirdparty
done


echo "Concatenate JavaScript..."
cd min/

# CAUTION: This must be the exact same files in reverse order than in application/layout/layout.phtml
cat jquery-1.8.3.js \
both.js \
local.js \
jquery.timeago.js \
> application.js
