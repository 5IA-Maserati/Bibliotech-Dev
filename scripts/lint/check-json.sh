#!/bin/bash

echo "Validating JSON files..."

failed=false

for file in $(find . -type f \( -name "*.json" \)); do
  if ! jq empty "$file" >/dev/null 2>&1; then
    echo "Invalid JSON: $file"
    failed=true
  fi
done

if [ "$failed" = true ]; then
  echo "JSON validation failed."
  exit 1
fi

echo "All JSON files are valid."