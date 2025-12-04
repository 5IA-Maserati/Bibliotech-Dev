#!/bin/bash

echo "Checking for duplicate files..."

failed=false

# Create hash list
declare -A filehashes

while IFS= read -r file; do
  hash=$(sha1sum "$file" | awk '{print $1}')
  if [[ -n "${filehashes[$hash]}" ]]; then
    echo "Duplicate file found: $file (same as ${filehashes[$hash]})"
    failed=true
  else
    filehashes[$hash]=$file
  fi
done < <(find . -type f \( -name "*.md" -o -name "*.svg" -o -name "*.png" -o -name "*.fig" -o -name "*.xd" \))

if [ "$failed" = true ]; then
  echo "Duplicate file check failed."
  exit 1
fi

echo "No duplicate files found."