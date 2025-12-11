#!/bin/bash

echo "Checking for empty files..."

failed=false

for file in $(find . -type f \( -name "*.md" -o -name "*.htm" -o -name "*.html" -o -name "*.json" -o -name "*.css" -o -name "*.js" -o -name "*.sql" -o -name "*.pdf" -o -name "*.mdf" -o -name "*.txt" \)); do
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
