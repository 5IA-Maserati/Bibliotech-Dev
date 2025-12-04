#!/bin/bash
echo "Checking for potential secrets..."

failed=false

for file in $(find . -type f -not -path "./.git/*"); do
  if grep -E "API_KEY|TOKEN|SECRET|PASSWORD" "$file" > /dev/null; then
    echo "Potential secret found in: $file"
    failed=true
  fi
done

if [ "$failed" = true ]; then
  echo "Secrets check failed!"
  exit 1
fi

echo "Secrets check passed."