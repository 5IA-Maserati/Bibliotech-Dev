#!/bin/bash
echo "Checking for potential secrets..."

failed=false

# Loop through all files, excluding .git and scripts/security
while IFS= read -r file; do
  if grep -E "API_KEY|TOKEN|SECRET|PASSWORD" "$file" > /dev/null; then
    echo "Potential secret found in: $file"
    failed=true
  fi
done < <(find . -type f \
           -not -path "./.git/*" \
           -not -path "./scripts/security/*")

if [ "$failed" = true ]; then
  echo "Secrets check failed!"
  exit 1
fi

echo "Secrets check passed."