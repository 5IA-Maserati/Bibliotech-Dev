#!/bin/bash

echo "Checking for empty files..."

failed=false

for file in $(find . -type f \( -name "*.md" -o -name "*.svg" -o -name "*.png" -o -name "*.fig" -o -name "*.xd" \)); do
  if [ ! -s "$file" ]; then
    echo "Empty file found: $file"
    failed=true
  fi
done

if [ "$failed" = true ]; then
  echo "Empty file check failed."
  exit 1
fi

echo "No empty files found."