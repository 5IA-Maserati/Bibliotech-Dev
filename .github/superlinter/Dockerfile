FROM github/super-linter:latest

# Update package list and install PHP PDO extensions
RUN apt-get update && \
    apt-get install -y php-pdo php-mysql && \
    apt-get clean