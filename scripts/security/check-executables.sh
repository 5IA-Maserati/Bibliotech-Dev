#!/bin/bash
echo "Checking unauthorized executables..."

failed=false

for file in $(find . -type f \( -iname "*.exe" -o -iname "*.bat" -o -iname "*.sh" \)); do
  # Allow scripts only in scripts/ folder
  if [[ "$file" != ./scripts/* ]]; then
    echo "Unauthorized executable found: $file"
    failed=true
  fi
done

if [ "$failed" = true ]; then
  echo "Executable check failed!"
  exit 1
fi

echo "Executable check passed."