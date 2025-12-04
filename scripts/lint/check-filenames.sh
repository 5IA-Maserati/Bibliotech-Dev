#!/bin/bash

echo "Checking filenames in design folders..."

invalid=false

# Folders to check
DESIGN_FOLDERS="assets components branding mockups prototypes templates"

# Allowed extensions
EXTENSIONS="png|jpg|jpeg|svg|fig|xd|pdf|md"

for folder in $DESIGN_FOLDERS; do
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