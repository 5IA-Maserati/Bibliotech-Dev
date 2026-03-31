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

def normalize_isbn(isbn: str) -> str:
    return ''.join(ch for ch in isbn if ch.isdigit() or ch.upper() == 'X')


def check_isbn10(isbn: str) -> bool:
    if len(isbn) != 10:
        return False
    total = 0
    for i, ch in enumerate(isbn[:9], 1):
        if not ch.isdigit():
            return False
        total += i * int(ch)
    check = isbn[9].upper()
    check_digit = 10 if check == 'X' else (int(check) if check.isdigit() else -1)
    if check_digit < 0:
        return False
    return (total + 10 * check_digit) % 11 == 0


def check_isbn13(isbn: str) -> bool:
    if len(isbn) != 13 or not isbn.isdigit():
        return False
    total = 0
    for i, ch in enumerate(isbn[:12]):
        total += int(ch) * (1 if i % 2 == 0 else 3)
    check_digit = (10 - (total % 10)) % 10
    return check_digit == int(isbn[12])


def discover_isbn_from_title(title):
    if not title:
        return None, 'No title provided'

    query = requests.utils.requote_uri(title)
    url = f"https://www.googleapis.com/books/v1/volumes?q=intitle:{query}&maxResults=5"

    try:
        response = requests.get(url, timeout=10)
        response.raise_for_status()
        data = response.json()

        items = data.get('items', [])
        if not items:
            return None, 'No title matches found'

        for item in items:
            volume_info = item.get('volumeInfo', {})
            industry = volume_info.get('industryIdentifiers', [])
            for entry in industry:
                isbn_candidate = normalize_isbn(entry.get('identifier', ''))
                if len(isbn_candidate) in (10, 13):
                    if (len(isbn_candidate) == 10 and check_isbn10(isbn_candidate)) or (len(isbn_candidate) == 13 and check_isbn13(isbn_candidate)):
                        return isbn_candidate, volume_info.get('title', '')

        return None, 'No valid ISBN found in title results'

    except requests.exceptions.RequestException as e:
        return None, f'Connection error: {e}'


def main1():
    check_books = 0
    csv_filename = 'src\\db\\Libri_Lista.csv'
    output_filename = 'src\\db\\Libri_Lista_checked.csv'

    print('Starting ISBN verification...\n' + '-'*40)

    output_rows = []

    try:
        with open(csv_filename, mode='r', encoding='utf-8') as file:
            reader = csv.DictReader(file)

            for row in reader:
                isbn = row.get('isbn', '').strip()
                expected_title = row.get('titolo', row.get('title', '')).strip()
                normalized = normalize_isbn(isbn)

                isbn_status = 'invalid'
                authoritative_isbn = normalized
                matched_title = ''
                result = None

                if len(normalized) == 10 and check_isbn10(normalized):
                    isbn_status = 'ISBN-10'
                elif len(normalized) == 13 and check_isbn13(normalized):
                    isbn_status = 'ISBN-13'
                else:
                    isbn_status = 'invalid'

                if isbn_status != 'invalid':
                    result, matched_title = check_book_authenticity(normalized, expected_title)

                if result is True:
                    action = 'ok'
                    check_books += 1
                elif result is False:
                    action = 'title-mismatch'
                    candidate_isbn, candidate_title = discover_isbn_from_title(expected_title)
                    if candidate_isbn:
                        authoritative_isbn = candidate_isbn
                        action = 'corrected-isbn-by-title'
                        isbn_status = 'ISBN-10' if len(candidate_isbn) == 10 else 'ISBN-13'
                else:
                    candidate_isbn, candidate_title = discover_isbn_from_title(expected_title)
                    if candidate_isbn:
                        authoritative_isbn = candidate_isbn
                        isbn_status = 'ISBN-10' if len(candidate_isbn) == 10 else 'ISBN-13'
                        action = 'found-isbn-by-title'
                    else:
                        action = f'not-found ({candidate_title})'

                print(f"Checking ISBN: {isbn} -> {authoritative_isbn} ({isbn_status}) -> {action}; title: '{expected_title}' ; api title: '{matched_title}'")

                row['checked_isbn'] = authoritative_isbn
                row['isbn_type'] = isbn_status
                row['authenticity'] = action
                row['matched_title'] = matched_title
                output_rows.append(row)

                time.sleep(1)

    except FileNotFoundError:
        print(f"Could not find the file '{csv_filename}'. Make sure it's in the same folder as this script.")
        return

    if output_rows:
        with open(output_filename, mode='w', encoding='utf-8', newline='') as out:
            fieldnames = list(output_rows[0].keys())
            writer = csv.DictWriter(out, fieldnames=fieldnames)
            writer.writeheader()
            writer.writerows(output_rows)

        print('Output written to', output_filename)

    print('ISBN Verificati autentici: ', check_books)

def main():
    csv_filename = "src\\db\\Libri_Lista_checked.csv"
    
    print("Starting ISBN verification...\n" + "-"*40)
    matched = 0
    mismatched = 0
    not_found = 0
    
    try:
        # Open the CSV file containing your books
        with open(csv_filename, mode='r', encoding='utf-8') as file:
            reader = csv.DictReader(file)
            
            # Process one book at a time
            for row in reader:
                isbn = row['checked_isbn'].strip()
                expected_title = row['titolo'].strip()
                
                print(f"Checking ISBN: {isbn}...")
                
                # Run the check
                match, fetched_title = check_book_authenticity(isbn, expected_title)
                
                # Output the results
                if match is True:
                    print(f"✅ AUTHENTIC: Matches '{fetched_title}'")
                    matched += 1
                elif match is False:
                    print(f"❌ MISMATCH: Expected '{expected_title}', but found '{fetched_title}'")
                    mismatched += 1
                else:
                    print(f"⚠️ ERROR: {fetched_title}")
                    not_found += 1
                
                print("-" * 40)
                
                # Pause for 1 second before the next request so Google doesn't block us for spamming
                time.sleep(1)
                
    except FileNotFoundError:
        print(f"Could not find the file '{csv_filename}'. Make sure it's in the same folder as this script.")

    print("\nSummary:")
    print(f"Matched: {matched}")
    print(f"Mismatched: {mismatched}")
    print(f"Not found / error: {not_found}")

if __name__ == "__main__":
    main()
    main1()

#TODO Checks for switches ISBN and put them in their correct place, 
# and search for the ISBN using google/books API to check if the book is authentic or not, and if it is not authentic, 
# check if the title is correct or not.