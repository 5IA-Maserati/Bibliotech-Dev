# Contributing to Bibliotech Dev

Thank you for contributing to this project! Your contributions help improve it for everyone.

## How to Contribute

### 1. Pull the latest changes

Before making any changes, make sure you have the latest version of the repository:

```bash
git checkout main
git pull origin main
```

### 2. Code Style & Linting

- Follow the existing code style; CI workflows will enforce linting rules.
- Run linting and tests before committing:

```bash
npm install        # install dependencies
npm run lint       # run code linter
npm test           # run tests
```

### 3. Commit Messages
- Use clear and descriptive commit messages.
- Prefer to use the present tense for consistency:

```markdown
Update .gitignore
```
Examples:
- `Add config file`
- `Delete /res`
- `Resolve connection issue`

### 4. Making Changes
- Make changes directly on `main`.
- Test your changes thoroughly.
- Document any new scripts or features.

### 5. Pull Requests
- Even though all work is on main, it is recommended to use Pull Requests for review before merging significant changes.
- Ensure that your PR passes all CI checks.

### 6. Reporting Issues
- If you find a bug or have a feature request, open an issue with as much detail as possible.