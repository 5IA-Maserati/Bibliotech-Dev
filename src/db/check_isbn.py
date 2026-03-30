import requests
import csv
import time

def check_book_authenticity(isbn, expected_title):
    # Google Books API endpoint for ISBN search
    url = f"https://www.googleapis.com/books/v1/volumes?q=isbn:{isbn}"
    
    try:
        response = requests.get(url)
        data = response.json()

        # Check if the API returned any books for this ISBN
        if "items" in data:
            # Extract the title from the first search result
            fetched_title = data["items"][0]["volumeInfo"].get("title", "")

            # Convert both titles to lowercase and remove extra spaces for a fairer comparison
            expected_clean = expected_title.strip().lower()
            fetched_clean = fetched_title.strip().lower()

            # We check if one is inside the other, because APIs sometimes include 
            # subtitles (e.g., "The Hobbit" vs "The Hobbit: Or There and Back Again")
            if expected_clean in fetched_clean or fetched_clean in expected_clean:
                return True, fetched_title
            else:
                return False, fetched_title
        else:
            return None, "ISBN not found in the database"
            
    except Exception as e:
        return None, f"Connection error: {e}"

def main():
    csv_filename = "src\\db\\Libri_Lista.csv"
    
    print("Starting ISBN verification...\n" + "-"*40)
    
    try:
        # Open the CSV file containing your books
        with open(csv_filename, mode='r', encoding='utf-8') as file:
            reader = csv.DictReader(file)
            
            # Process one book at a time
            for row in reader:
                isbn = row['isbn'].strip()
                expected_title = row['titolo'].strip()
                
                print(f"Checking ISBN: {isbn}...")
                
                # Run the check
                match, fetched_title = check_book_authenticity(isbn, expected_title)
                
                # Output the results
                if match is True:
                    print(f"✅ AUTHENTIC: Matches '{fetched_title}'")
                elif match is False:
                    print(f"❌ MISMATCH: Expected '{expected_title}', but found '{fetched_title}'")
                else:
                    print(f"⚠️ ERROR: {fetched_title}")
                
                print("-" * 40)
                
                # Pause for 1 second before the next request so Google doesn't block us for spamming
                time.sleep(1)
                
    except FileNotFoundError:
        print(f"Could not find the file '{csv_filename}'. Make sure it's in the same folder as this script.")

if __name__ == "__main__":
    main()

#TODO Checks for switches ISBN and put them in their correct place, 
# and search for the ISBN using google/books API to check if the book is authentic or not, and if it is not authentic, 
# check if the title is correct or not.