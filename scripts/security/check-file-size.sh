#!/bin/bash
echo "🔍 Checking for large files (>5MB)..."

failed=false

for file in $(find . -type f -size +5M); do
  echo "File too large: $file"
  failed=true
done

if [ "$failed" = true ]; then
  echo "File size check failed!"
  exit 1
fi

echo "File size check passed."