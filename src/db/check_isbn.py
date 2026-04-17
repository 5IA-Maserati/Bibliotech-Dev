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


def discover_isbn_from_title(title, publisher=None, year=None, author=None):
    if not title:
        return None, 'No title provided'

    # Build query components
    query_parts = [f"intitle:{requests.utils.requote_uri(title)}"]

    if publisher:
        query_parts.append(f"inpublisher:{requests.utils.requote_uri(publisher)}")

    if author:
        query_parts.append(f"inauthor:{requests.utils.requote_uri(author)}")

    # For year, create a range (year ± 2 years) to account for reprints/editions
    if year and year.isdigit():
        year_int = int(year)
        before_year = year_int + 2
        after_year = max(1800, year_int - 2)  # Don't go before 1800
        query_parts.append(f"before:{before_year}")
        query_parts.append(f"after:{after_year}")

    query = "&".join(query_parts)
    url = f"https://www.googleapis.com/books/v1/volumes?q={query}&maxResults=10"

    try:
        response = requests.get(url, timeout=10)
        response.raise_for_status()
        data = response.json()

        items = data.get('items', [])
        if not items:
            return None, 'No matches found with the provided criteria'

        for item in items:
            volume_info = item.get('volumeInfo', {})
            industry = volume_info.get('industryIdentifiers', [])
            for entry in industry:
                isbn_candidate = normalize_isbn(entry.get('identifier', ''))
                if len(isbn_candidate) in (10, 13):
                    if (len(isbn_candidate) == 10 and check_isbn10(isbn_candidate)) or (len(isbn_candidate) == 13 and check_isbn13(isbn_candidate)):
                        return isbn_candidate, volume_info.get('title', '')

        return None, 'No valid ISBN found in search results'

    except requests.exceptions.RequestException as e:
        return None, f'Connection error: {e}'


def persistent_isbn_search(title, publisher=None, year=None, author=None):
    """
    Keep searching for ISBN using progressively broader criteria until a result is found.
    Returns the first valid ISBN found and the search strategy that worked.
    """
    search_strategies = [
        # Strategy 1: All criteria (most specific)
        {
            'name': 'all_criteria',
            'title': title,
            'publisher': publisher,
            'year': year,
            'author': author
        },
        # Strategy 2: Title + Author + Year
        {
            'name': 'title_author_year',
            'title': title,
            'publisher': None,
            'year': year,
            'author': author
        },
        # Strategy 3: Title + Author
        {
            'name': 'title_author',
            'title': title,
            'publisher': None,
            'year': None,
            'author': author
        },
        # Strategy 4: Title + Publisher
        {
            'name': 'title_publisher',
            'title': title,
            'publisher': publisher,
            'year': None,
            'author': None
        },
        # Strategy 5: Title + Year
        {
            'name': 'title_year',
            'title': title,
            'publisher': None,
            'year': year,
            'author': None
        },
        # Strategy 6: Title only (least specific)
        {
            'name': 'title_only',
            'title': title,
            'publisher': None,
            'year': None,
            'author': None
        }
    ]

    for strategy in search_strategies:
        print(f"  Trying strategy: {strategy['name']}")
        candidate_isbn, candidate_title = discover_isbn_from_title(
            strategy['title'],
            strategy['publisher'],
            strategy['year'],
            strategy['author']
        )

        if candidate_isbn:
            print(f"  ✅ Found ISBN using {strategy['name']}: {candidate_isbn}")
            return candidate_isbn, candidate_title, strategy['name']

        # Add small delay between attempts to be respectful to the API
        time.sleep(0.5)

    print("  ❌ No ISBN found with any search strategy")
    return None, 'No ISBN found with any search strategy', 'failed_all'


def search_ISBN():
    check_books = 0
    csv_filename = 'src\\db\\Libri_Lista.csv'
    output_filename = 'src\\db\\Libri_Lista_checked_EXAMPLE.csv'

    print('Starting ISBN verification...\n' + '-'*40)

    output_rows = []

    try:
        with open(csv_filename, mode='r', encoding='utf-8') as file:
            reader = csv.DictReader(file)

            for row in reader:
                isbn = row.get('isbn', '').strip()
                expected_title = row.get('titolo', row.get('title', '')).strip()
                publisher = row.get('casa_editrice', '').strip()
                year = row.get('anno', '').strip()
                author = row.get('autore', '').strip()
                normalized = normalize_isbn(isbn)

                authoritative_isbn = normalized
                matched_title = ''
                result = None

                if len(normalized) == 13 and check_isbn13(normalized):
                    isbn_status = 'ISBN-13'
                elif len(normalized) == 10 and check_isbn10(normalized):
                    isbn_status = 'ISBN-10'
                else:
                    isbn_status = 'invalid'

                if isbn_status != 'invalid':
                    result, matched_title = check_book_authenticity(normalized, expected_title)

                if result is True:
                    action = 'ok'
                    check_books += 1
                elif result is False:
                    action = 'title-mismatch'
                    print(f"  Title mismatch detected, searching for correct ISBN...")
                    candidate_isbn, candidate_title, search_strategy = persistent_isbn_search(expected_title, publisher, year, author)
                    if candidate_isbn:
                        authoritative_isbn = candidate_isbn
                        action = f'corrected-isbn-by-{search_strategy}'
                        isbn_status = 'ISBN-10' if len(candidate_isbn) == 10 else 'ISBN-13'
                else:
                    print(f"  No valid ISBN found, searching for ISBN...")
                    candidate_isbn, candidate_title, search_strategy = persistent_isbn_search(expected_title, publisher, year, author)
                    if candidate_isbn:
                        authoritative_isbn = candidate_isbn
                        isbn_status = 'ISBN-10' if len(candidate_isbn) == 10 else 'ISBN-13'
                        action = f'found-isbn-by-{search_strategy}'
                    else:
                        action = f'not-found ({candidate_title})'

                print(f"Checking ISBN: {isbn} -> {authoritative_isbn} ({isbn_status}) -> {action}; title: '{expected_title}' ; publisher: '{publisher}' ; year: '{year}' ; author: '{author}' ; api title: '{matched_title}'")

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

def check_ISBN():
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
    search_ISBN()
    check_ISBN()

#TODO Checks for switches ISBN and put them in their correct place, 
# and search for the ISBN using google/books API to check if the book is authentic or not, and if it is not authentic, 
# check if the title is correct or not.