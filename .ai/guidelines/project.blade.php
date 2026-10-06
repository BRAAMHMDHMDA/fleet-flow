## Existing project conventions

- Preserve the existing Laravel 12, Filament 4, and Livewire 3 organization. Check installed versions and use version-matched documentation before making changes.
- Keep simple Filament resources in `app/Filament/Resources/{PluralName}` with their existing `Pages/Manage{PluralName}` modal CRUD pages. Forms, infolists, and tables currently live in the resource class; extract them only when the task warrants it.
- Central users, roles, permissions, tenants, and domains are separate from tenant clients. Preserve explicit central/tenant connection boundaries, including validation and background jobs.
- Retain the project's policy-based `Action:Model` permission naming. Tenant selection and role-assignment requirements must be confirmed rather than inferred from the current implementation.
- Follow `.editorconfig` and neighboring code. Strict types and some formatting choices are inconsistent; do not normalize unrelated files or introduce architectural layers as incidental cleanup.
- The test suite uses PHPUnit classes. Adapt any generated Pest-style examples to PHPUnit and `Livewire::test()`; do not install Pest just to use an example.
- Before running database or tenant tests, verify an isolated test environment. Tenant lifecycle events create, migrate, and delete databases; SQLite `:memory:` does not establish safe isolation for automatic multidatabase tenancy.
- Do not run `composer run setup` as routine onboarding: it generates an application key and force-runs migrations. Do not read or report secrets from environment files.
- Keep changes scoped to the user's request. Preserve unrelated work and existing custom guidance. Do not modify global Superpowers installations as part of project tooling maintenance.
