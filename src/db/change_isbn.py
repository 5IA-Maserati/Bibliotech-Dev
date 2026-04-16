import pandas as pd

# Load the CSV file
file_path = "src//db//Libri_Lista_checked_EXAMPLE.csv"
df = pd.read_csv(file_path)

# Replace empty or missing checked_isbn values with '-'
df['checked_isbn'] = df['checked_isbn'].fillna('-')
df.loc[df['checked_isbn'] == '', 'checked_isbn'] = '-'

# Save to a new CSV file
output_path = "src//db//Libri_Lista_checked_AURA.csv"
df.to_csv(output_path, index=False)

print(f"Updated file saved as {output_path}")
