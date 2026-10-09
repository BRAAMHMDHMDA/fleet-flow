# Project onboarding assessment

Assessment date: 2026-10-06

The project has a clear, compact structure worth preserving: Filament resources handle administration, Eloquent models define the central/tenant database boundary, and Stancl Tenancy manages tenant infrastructure. The main concerns are authorization gaps, tenant lifecycle edge cases, and the lack of application tests.

This assessment was read-only. I changed no files, installed nothing, ran no migrations, and did not execute Laravel, tests, or formatters. The working tree remained clean. I avoided reading `.env` values and redacted identifying seed data. This Markdown export was subsequently created at your request.

I found no project-specific `AGENTS.md`, `CLAUDE.md`, or equivalent instructions in the project or inspected ancestor locations. [README.md](README.md) is the Laravel starter README and does not document this application’s setup or intended behavior.

The dependency baseline is verified against both [composer.lock](composer.lock) and [installed Composer metadata](vendor/composer/installed.json). All 151 installed package versions match the lockfile.

| Component | Installed version |
|---|---|
| Laravel | 12.51.0 |
| Filament and its component packages | 4.7.1 |
| Livewire | 3.7.10 |
| Filament Shield | 4.1.0 |
| Spatie Laravel Permission | 6.24.1 |
| Stancl Tenancy | 3.9.1 |
| Stancl JobPipeline / VirtualColumn | 1.8.1 / 1.5.0 |
| Laravel Tinker | 2.11.1 |
| PHPUnit | 11.5.53 |
| Laravel Pint | 1.27.1 |
| Laravel Sail / Pail | 1.53.0 / 1.2.6 |
| Faker / Mockery / Collision | 1.24.1 / 1.6.12 / 8.8.3 |

**The effective PHP minimum is 8.4**, although [composer.json:9](composer.json#L9) declares `^8.2`. Several locked Symfony 8 packages require PHP 8.4; Composer’s generated [platform check](vendor/composer/platform_check.php#L7) explicitly enforces it.

The local CLI is PHP **8.4.26**, Node **24.5.0**, and npm **11.5.1**. PHP includes SQLite/MySQL PDO, Intl, Mbstring, XML, ZIP, and PCNTL.

Frontend versions are **constraints only**, from [package.json](package.json#L9):

| Dependency | Declared constraint |
|---|---|
| Vite | `^7.0.7` |
| Laravel Vite plugin | `^2.0.0` |
| Tailwind CSS / Tailwind Vite plugin | `^4.0.0` |
| Axios | `^1.11.0` |
| Concurrently | `^9.0.1` |

There is no frontend lockfile, `node_modules`, or built Vite manifest. Exact frontend versions and build success therefore cannot be verified. The installed Node version satisfies [Vite 7’s documented requirement](https://v7.vite.dev/guide/).

The available agent tooling is also verified:

- **Superpowers:** readable skill files exist for `using-superpowers`, `brainstorming`, `writing-plans`, `executing-plans`, `test-driven-development`, `systematic-debugging`, `verification-before-completion`, `requesting-code-review`, `receiving-code-review`, `using-git-worktrees`, `finishing-a-development-branch`, `dispatching-parallel-agents`, and `subagent-driven-development`.
- These exist under both `/home/bmh/.codex/skills` and the Superpowers **6.4.2** plugin directory. The plugin additionally provides `diagnosing-superpowers` and `writing-skills`.
- The two `writing-plans` copies differ: the [local copy](/home/bmh/.codex/skills/writing-plans/SKILL.md) requires fuller implementation code; the [plugin copy](/home/bmh/.codex/plugins/cache/openai-curated-remote/superpowers/6.4.2/skills/writing-plans/SKILL.md) favors interfaces, assertions, and concise implementation guidance. The other 12 shared skill files match.
- **Laravel Boost is unavailable in this session:** no Boost tools are exposed, neither `laravel/boost` nor `laravel/mcp` is installed, and no project Boost/MCP configuration was found. PhpStorm/Laravel Idea inspection tools are exposed; those are separate capabilities.

The architectural boundaries currently look like this:

| Boundary | Observed implementation |
|---|---|
| Central data | `User`, `Role`, `Permission`, and `Tenant` explicitly use `CentralConnection`. See [User.php](app/Models/User.php#L12), [Role.php](app/Models/Role.php#L8), and [Tenant.php](app/Models/Tenant.php#L11). |
| Tenant data | `Client` uses `TenantConnection`; client tables live under `database/migrations/tenant`. See [Client.php](app/Models/Client.php#L8). |
| Administration | One `/admin` Filament panel, central-domain restriction, login/profile, Shield, and a custom tenant switcher. See [AdminPanelProvider.php](app/Providers/Filament/AdminPanelProvider.php#L28). |
| Tenant identification | Client administration uses session selection; tenant-facing routes use domain identification. See [session middleware](app/Http/Middleware/InitializeTenancyFromSession.php#L12) and [tenant routes](routes/tenant.php#L22). |
| Business operations | CRUD callbacks live in resources/pages; database provisioning lives in tenancy event pipelines. There is no established service/repository layer. |
| Authentication and authorization | Separate `web` and `client` guards; central permission policies. No user-to-tenant membership relationship was found. See [auth.php](config/auth.php#L38) and [permission.php](config/permission.php#L134). |

There are no substantive application controllers: [Controller.php](app/Http/Controllers/Controller.php) is an empty base class. No API routes, Form Requests, custom jobs, or broader fleet business modules were found. Their intended requirements cannot be inferred from the project name.

The recurring conventions are stronger at the organizational level than at the formatting level:

| Classification | Convention and evidence |
|---|---|
| **Consistent** | Plural resource directories, singular resource classes, and one `ManageRecords` page using modal CRUD. Repeated across [Users](app/Filament/Resources/Users/UserResource.php), [Clients](app/Filament/Resources/Clients/ClientResource.php), and [Tenants](app/Filament/Resources/Tenants/TenantResource.php). |
| **Consistent** | Filament 4 `Schema`, `components()`, `Filament\Actions`, `recordActions()`, and `toolbarActions()`. Forms, infolists, and tables remain together in resource classes. |
| **Consistent** | User and Client use `$fillable`, `$hidden`, and `casts(): array`, including password hashing. Forms use fluent field validation and closures. |
| **Consistent** | Policies return permission checks named `Action:Model`, matching Shield’s PascalCase/colon configuration. See [ClientPolicy.php](app/Policies/ClientPolicy.php#L12) and [Shield configuration](config/filament-shield.php#L103). |
| **Mixed** | Strict types appear in Tenant/Role policies and tenancy-related files, but not User/Client policies or resources. Record parameters also differ between policies. See [TenantPolicy.php](app/Policies/TenantPolicy.php#L20) versus [UserPolicy.php](app/Policies/UserPolicy.php#L17). |
| **Mixed** | Four-space indentation is configured, but spacing, import order, trailing whitespace, and callback return types vary. Examples include [session middleware:18](app/Http/Middleware/InitializeTenancyFromSession.php#L18) and [TenantResource:38](app/Filament/Resources/Tenants/TenantResource.php#L38). |
| **Isolated exceptions** | `UniqueDomain` is the only custom validation rule; tenant provisioning is the only substantial custom CRUD workflow; the tenant route’s `echo` is not a recurring response convention. |
| **Insufficient evidence** | Two starter tests do not establish a meaningful project testing style. Generated-looking comments and scaffolding should not automatically be treated as personal preferences. |

[.editorconfig](.editorconfig) and [.gitattributes](.gitattributes) establish UTF-8, LF, four spaces, final newlines, and two-space YAML. Pint is installed, but no custom Pint configuration, formatting script, CI workflow, PHPStan/Larastan, ESLint, or Prettier configuration was found. Pint would use its [default Laravel preset](https://laravel.com/docs/12.x/pint); that does not establish that the current project has been formatted with it.

The following problems deserve attention. **These are source findings, not reproduced runtime failures.**

1. **The tenant root route exposes client records without authentication.**  
   [routes/tenant.php:22](routes/tenant.php#L22) applies tenancy middleware but no authentication, then outputs all clients. Passwords are hidden by the model, but names, emails, and other visible attributes remain. Whether any client directory should be public requires your decision.

2. **The current User model cannot access Filament outside `local`.**  
   [User.php:12](app/Models/User.php#L12) lacks `FilamentUser` and `canAccessPanel()`. The installed [Authenticate middleware:32](vendor/filament/filament/src/Http/Middleware/Authenticate.php#L32) returns 403 for such users in non-local environments, including `testing`. This matches [Filament 4’s documented access requirements](https://filamentphp.com/docs/4.x/users/overview).

3. **Bulk deletion lacks the policy method Filament checks.**  
   Resources expose `DeleteBulkAction`, but all four policies omit `deleteAny`; Shield’s generated-method list omits it too. Filament’s [action authorization](vendor/filament/filament/src/Resources/Pages/Page.php#L298) checks `deleteAny`, and its [default authorization helper](vendor/filament/filament/src/helpers.php#L31) permits missing methods unless strict authorization or a denying gate intervenes. Individual-record authorization is not enabled here. This is especially consequential for tenants because deleting one triggers database deletion.

4. **Empty and stale tenant selections are not handled safely.**  
   [TenantSwitcher.php:16](app/Livewire/TenantSwitcher.php#L16) dereferences `Tenant::first()` without handling an empty database. A deleted selection can produce a null model that the [Blade view](resources/views/filament/components/tenant-switcher.blade.php#L4) dereferences. The middleware automatically selects the first tenant when selection is absent, but silently continues without initialization when a stored ID no longer exists.

5. **Tenant provisioning runs migrations twice.**  
   [TenancyServiceProvider.php:26](app/Providers/TenancyServiceProvider.php#L26) synchronously creates and migrates the database on `TenantCreated`; [ManageTenants.php:28](app/Filament/Resources/Tenants/Pages/ManageTenants.php#L28) then invokes tenant migrations again. Provisioning, domain creation, and error recovery have no explicit coordinated recovery flow.

6. **The checked-in cache defaults conflict with tenant cache tagging.**  
   `.env.example` selects database cache, while [tenancy.php:30](config/tenancy.php#L30) enables `CacheTenancyBootstrapper`. The installed [tenant cache manager](vendor/stancl/tenancy/src/CacheManager.php#L18) uses tags, which Laravel’s database cache does not support. Actual `.env` overrides were not inspected.

7. **Queue storage needs an explicit deployment decision.**  
   [queue.php:38](config/queue.php#L38) leaves the database connection dependent on `DB_QUEUE_CONNECTION`. With database tenancy, tenant-dispatched jobs need a deliberately central queue connection, as described in [Stancl’s queue documentation](https://tenancyforlaravel.com/docs/v3/queues/). No custom application jobs currently demonstrate the intended arrangement.

Other concerns are smaller or depend on requirements: the switcher loads every tenant and queries a domain separately for each; tenant editing updates all related domains even though the model supports multiple domains; domain validation checks uniqueness and length without hostname syntax; and central domain queries in [UniqueDomain.php:19](app/Rules/UniqueDomain.php#L19) use the current default connection rather than explicitly selecting the central one.

I checked two potential false positives against the installed versions: Filament’s `unique()` already ignores the current record by default, and Laravel’s `hashed` cast recognizes existing hashes. Also, the standard Filament 4.7.1 Create action checks `getCreateAuthorizationResponse()`, so the custom client `canCreate()` override is **not evidence of a create-policy bypass**.

The project’s defined development command is `composer run dev`. It starts Laravel’s development server, a queue listener, Pail, and Vite together. `npm run dev` starts Vite separately; `npm run build` creates production assets. These commands are defined in [composer.json:37](composer.json#L37).

A working environment also needs:

- PHP compatible with the locked dependencies, Composer dependencies, and frontend dependencies.
- An application key and working central database.
- Hostname resolution matching `CENTRAL_DOMAIN`, whose checked-in fallback is `fleetflow.test`, plus tenant domains. Browsing the development server through an arbitrary localhost hostname will not match these restrictions.
- Deliberate cache, session, and queue configuration.
- Tenant database creation/deletion privileges when exercising provisioning.
- Initial administrative permissions and tenants. [DatabaseSeeder.php](database/seeders/DatabaseSeeder.php#L17) creates one user but no roles, permissions, or tenants.

The `setup` script installs dependencies, generates an application key, and force-runs migrations. It is not a harmless startup command. Sail is installed, but no Compose configuration was found.

Testing currently consists of [a trivial unit assertion](tests/Unit/ExampleTest.php#L12) and [a GET `/` smoke test](tests/Feature/ExampleTest.php#L13). There are no tenancy, permission, validation, resource-action, or provisioning tests.

`composer test` clears configuration and invokes `php artisan test`; direct PHPUnit is also available. [phpunit.xml:20](phpunit.xml#L20) configures SQLite `:memory:`, array cache/session/mail, and synchronous queues. `.env.testing` and a cached Laravel configuration file are absent.

Before safely running meaningful tests:

1. Establish an isolated test environment and verify effective connection settings, including inherited `DB_URL` and shell variables. The XML values do not use `force`.
2. Align the request hostname with the configured central domain for central HTTP tests.
3. Give tenant tests disposable databases, unique names, isolated storage, and explicit teardown. Creating/deleting a tenant invokes infrastructure operations even when queues are synchronous.
4. Design tenant tests around connection switching. Stancl documents limitations with automatic multidatabase tenancy, SQLite `:memory:`, and ordinary `RefreshDatabase` usage. [Tenancy testing guidance](https://tenancyforlaravel.com/docs/v3/testing/).
5. Account for the panel-access issue under `APP_ENV=testing` and supply explicit role/permission fixtures.

Laravel tests can write compiled views, logs, and result caches, which is why I did not run them during this read-only task. Test success, browser behavior, database connectivity, deployed configuration, and actual role assignments remain **unverified**.

For future changes, I propose these conventions:

- Preserve the existing resource directories, modal CRUD pages, and inline resource schemas. Extract shared operations when reuse or lifecycle complexity warrants it.
- Target Laravel 12, Filament 4, Livewire 3, Shield 4, and Tenancy 3 APIs using the installed source and versioned documentation.
- Keep central and tenant connection ownership explicit, including validation queries and background infrastructure.
- Continue policy-based authorization and `Action:Model` naming; cover bulk operations and tenant selection explicitly.
- Follow `.editorconfig` and neighboring code. Use Pint’s Laravel preset as a proposed baseline for touched files, while treating strict types, policy signatures, and broader formatting changes as unsettled preferences.
- Continue PHPUnit class-based tests, adding focused coverage for authorization, tenant isolation, tenant selection, and provisioning before expanding those behaviors.

The questions that materially affect the next step are:

1. **Who may access which tenants?** Are administrators global, or should users have tenant memberships? Who may assign privileged roles through User management?
2. **What is the tenant-facing application supposed to expose?** Is the current client output temporary development code, and should clients authenticate through the configured `client` guard?
3. **What are the tenant lifecycle rules?** Should an empty installation work without tenants, may tenant IDs change, may tenants have multiple domains, and should deletion immediately destroy their databases?
4. **What is the supported runtime?** Is PHP 8.4 intended, and which database, cache, queue, and hosting arrangement should development, tests, and production use?
5. **Which style differences are intentional?** In particular, strict types and formatting around existing custom code—these need your preference before establishing an enforced project standard.
