# Bibliotech Dev

This repository contains the development codebase for Bibliotech Dev. It includes backend, frontend, database scripts, and supporting tools.

---

## Table of Contents

- [Overview](#overview)
- [Project Structure](#project-structure)
- [Getting Started](#getting-started)
- [Dependencies](#dependencies)
- [Development Guidelines](#development-guidelines)
- [Testing](#testing)
- [Workflows](#workflows)
- [Contributing](#contributing)
- [License](#license)

---

## Overview

This repo provides the foundation for developing and maintaining Bibliotech Dev. The repository is structured to support modular development of backend, frontend, and database components, with clear workflows and coding standards.

---

## Project Structure

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

## Getting Started

### 1. **Clone the repository**
```bash
git clone https://github.com/5IA-Maserati/Bibliotech-Dev
cd Bibliotech-Dev
```

### 2. Install dependencies

- Node.js / npm:
```bash
npm install
```

- Python / pip (if applicable):
```bash
pip install -r requirements.txt
```

### 3. Run the application
- Backend:
```bash
npm start
```

- Frontend:
```bash
npm run dev
```

- Database migrations:
```bash
npm run db:migrate
```

## Dependencies

List main dependencies and tools here (e.g., Node.js version, database, frameworks).

## Development Guidelines

Follow the [style guide](docs/dev-style-guide.md) for coding conventions, commit messages, file naming, and general development best practices.
- Lint code before committing
- Run tests regularly
- Keep code modular and well-documented

## Testing

- Tests are located in the tests/ folder
- Run all tests before merging:
```bash
npm test
```

## Workflows

GitHub Actions workflows are configured for:
- **Linting and formatting**
- **File and asset validation**
- **Security checks**
- **Unit and integration tests**

Refer to `.github/workflows/` for detailed configurations.

## Contributing

Please refer to [CONTRIBUTING.md](CONTRIBUTING.md) for contribution guidelines. All work is done directly on main; no forks or feature branches are required.

## License

This repository is licensed under the [MIT License](LICENSE),