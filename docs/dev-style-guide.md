# Developer Style Guide

This guide describes coding conventions, project structure, and best practices for contributions to this repository. Following these guidelines ensures consistency, readability, and maintainability across the project.

---

## 1. Project Structure

Organize the repository into clear folders:

```bash
├── src/ # Source code
│ ├── api/ # API endpoints
│ ├── db/ # Database schema, migrations, seeds
│ └── frontend/ # Frontend components or pages
├── scripts/ # Utility scripts
├── tests/ # Automated tests
├── docs/ # Documentation, style guides
├── assets/ # Images, icons, static files
├── .github/workflows/ # GitHub Actions workflows
├── package.json # Project dependencies (if Node.js)
└── README.md
```

---

## 2. Naming Conventions

### Files & Folders
- Use **lowercase** letters.
- Separate words with **dashes (`-`)**.
- Examples:  
  `user-controller.js`, `database-migration.sql`

### Variables & Functions
- **JavaScript / TypeScript**: `camelCase` for variables and functions, `PascalCase` for classes.
- **Python**: `snake_case` for variables and functions, `PascalCase` for classes.
- Constants: `UPPER_CASE_WITH_UNDERSCORES`

---

## 3. Coding Standards

- Use **2 spaces** for indentation (or 4 spaces depending on the language, but be consistent).
- Always add meaningful **comments** for complex logic.
- Avoid large functions; break code into smaller, reusable modules.
- Follow the **linting rules** configured for the project.
- Remove commented-out or unused code before committing.

---

## 4. Commits & Git

- Keep commit messages clear and concise.
- Examples:
  - `Add user authentication endpoint`
  - `Correct null pointer exception in db migration`
  - `Update dependencies`

- Always **pull latest changes** before pushing.
- Run tests and linting before committing.

---

## 5. Testing

- Place tests in the `tests/` folder corresponding to the code location.
- Use descriptive names for test files:
  - `api/user.test.js`
  - `db/migration.test.sql`
- Write tests for all new features and bug fixes.
- Run all tests before creating a PR or merging to `main`.

---

## 6. Linting & Formatting

- Lint code according to the project’s `.eslintrc`, `.stylelintrc`, or equivalent.
- Run linters before committing.
- Check Markdown files using `markdownlint`.
- Validate JSON files using `jsonlint` or equivalent.

---

## 7. Security & Quality

- Sanitize user inputs.
- Avoid committing sensitive information (API keys, passwords, credentials).
- Validate SVGs and other assets to ensure they are safe and optimized.
- Keep file sizes reasonable for assets and resources.

---

## 8. Documentation

- Update `README.md` and `docs/` when adding or changing features.
- Include code examples, usage instructions, and explanations.
- Keep documentation clear and concise.

---

Following this guide will help maintain a clean, readable, and secure codebase. Thank you for contributing!