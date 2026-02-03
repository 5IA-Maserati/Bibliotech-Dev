# SQLFluff

## Introduction

To ensure consistent formatting and linting of SQL files in Bibliotech‑Dev, we are introducing **SQLFluff** as a mandatory development tool.

## Installation

SQLFluff can be installed in several ways:

### Using pip

```bash
pip install sqlfluff
```

### Using pipx (recommended for isolated environments)

```bash
pipx install sqlfluff
```

### Adding to the project (optional)

If you want to include it in `requirements-dev.txt`:

```markdown
sqlfluff>=2.4.0
```

## Project Configuration

Create a `.sqlfluff` file in the project root with the following basic settings:

```ini
[sqlfluff]
dialect = postgres  # or the SQL dialect used in the project
templater = jinja
exclude_rules = L009,L016  # rules to disable if necessary
```

## Usage

### Linting SQL files

To check SQL files for formatting issues:

```bash
sqlfluff lint path/to/sql/file.sql
```

### Autofixing issues

To automatically fix detected issues:

```bash
sqlfluff fix path/to/sql/file.sql
```

### Run before committing

It’s recommended to run SQLFluff before every commit to avoid formatting issues:

```bash
sqlfluff lint path/to/sql/files/
```

### Integration with CI / pre-commit

If the project uses **pre-commit**, add the following hook to `.pre-commit-config.yaml`:

```yaml
- repo: https://github.com/sqlfluff/sqlfluff
  rev: 2.4.0
  hooks:
    - id: sqlfluff-lint
```

## Additional Resources

* [Official SQLFluff Documentation](https://docs.sqlfluff.com/)
* [SQL Dialects Configuration](https://docs.sqlfluff.com/en/stable/dialects.html)
