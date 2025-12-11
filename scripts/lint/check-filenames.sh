#!/bin/bash

echo "Checking filenames in development folders..."

invalid=false

# Folders to check
DEV_FOLDERS="assets docs src tests"

# Allowed extensions
EXTENSIONS="htm|html|json|css|js|sql|pdf|md|mdf|txt"

for folder in $DEV_FOLDERS; do
  if [ -d "$folder" ]; then
    while IFS= read -r file; do
      basename=$(basename "$file")

      # Ignore .gitkeep
      [[ "$basename" == ".gitkeep" ]] && continue

      # Check filename
      if [[ ! "$basename" =~ ^[a-z0-9]+([\-][a-z0-9]+)*(-v[0-9]+)?\.($EXTENSIONS)$ ]]; then
        echo "Invalid filename: $file"
        invalid=true
      fi
    done < <(find "$folder" -type f)
  fi
done

if [ "$invalid" = true ]; then
  echo "Filename lint failed."
  exit 1
fi

echo "All filenames are valid!"
