#!/bin/bash

echo "Checking for unauthorized binary files..."

failed=false

for file in $(find . -type f \( -name "*.dll" -o -name "*.so" -o -name "*.o" \)); do
  echo "Unauthorized binary file found: $file"
  failed=true
done

if [ "$failed" = true ]; then
  echo "Binary file check failed."
  exit 1
fi

echo "No unauthorized binaries found."