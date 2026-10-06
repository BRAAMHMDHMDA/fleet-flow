# Project status and agent onboarding

Verified on 2026-10-07. This is a tooling and source-inspection baseline, not application acceptance testing.

Context: the local `ONBOARDING_ASSESSMENT.md` dated 2026-10-06 remains untouched and untracked. It is not required to use this committed guide. Its statement that Boost was unavailable describes the earlier assessment and is superseded by the installation below. Its fuller findings remain local context; this document summarizes only the decisions and caveats needed for subsequent work.

## Verified dependency and tooling baseline

| Component | Verified version or state |
| --- | --- |
| PHP CLI | 8.4.26; manifest allows `^8.2`, but the current lockfile requires PHP 8.4 through Symfony 8 dependencies |
| Laravel | 12.51.0 |
| Filament / Livewire | 4.7.1 / 3.7.10 |
| Filament Shield / Spatie Permission | 4.1.0 / 6.24.1 |
| Stancl Tenancy | 3.9.1 |
| PHPUnit / Pint | 11.5.53 / 1.27.1 |
| Boost / Laravel MCP / Roster | 2.10.2 / 1.0.1 / 1.0.0 |
| Composer Semver | 3.5.1, added transitively with Boost |
| Codex CLI | 0.160.1; project MCP command is `php artisan boost:mcp` |
| Frontend | Manifest constraints: Tailwind and its Vite plugin `^4.0.0`, Vite `^7.0.7`, Laravel Vite plugin `^2.0.0`, Axios `^1.11.0`, Concurrently `^9.0.1` |

Sources: [composer.lock](../composer.lock), installed Composer metadata, successful Boost `application-info`, [package.json](../package.json), and local CLI version output. No frontend lockfile, `node_modules`, or built Vite manifest was found; exact frontend versions and asset-build success remain unverified. The assessment observed Node 24.5.0 and npm 11.5.1; these are local observations, not a committed runtime policy.

Boost was added as a development dependency in `fd89599`; Codex integration and skills were added in `21efe85`. Exactly four development packages were added, with no changes to existing locked packages. Installation verification passed Composer validation, platform checks, PHP syntax, Pint, and diff checks. Composer reported 60 advisories affecting 18 existing packages at installation; their applicability and remediation were not investigated in this tooling task.

### MCP and skills

- The installation step successfully called `application-info` and `search-docs` from the actual Codex session; the latter returned Filament 4 authorization documentation. This establishes live connectivity, not application correctness. No further restart was required at that point.
- This guidance pass successfully called `application-info` again. A documentation query scoped to Boost returned unrelated results, including other package majors; those results were rejected. The installed Boost 2.10.2 source was used to verify customization and regeneration behavior.
- Discoverable tools: `application-info`, `search-docs`, `database-connections`, `database-query`, `database-schema`, `get-absolute-url`, `browser-logs`, `read-log-entries`, `last-error`, and `record-rule`. Discovery does not imply every tool has been exercised.
- Direct `record-rule` calls in this guidance session were blocked by its MCP approval policy (`never`). With explicit execution approval, the same tool was successfully invoked through the local Boost MCP server to generate `.ai/rules` and its index. The client configuration was not changed.
- Project skills: `infer-conventions`, `laravel-best-practices`, `testing-best-practices`, `tailwindcss-development`, and `livewire-development`. Filament guidance is generated into AGENTS.md. No Shield or Stancl-specific skill was installed.
- The Livewire skill is an explicit project copy of Boost 2.10.2's Livewire 3 template because Livewire is a transitive dependency. Revisit `.ai/skills/livewire-development/SKILL.blade.php` when Boost or Livewire changes.
- Superpowers workflows are available through local skills and the 6.4.2 plugin. The differing `writing-plans` copies remain unresolved; global installations are unchanged. `frontend-design` was not available in the inspected catalog/skill roots; use it for custom UI work if it becomes available.

[.codex/config.toml](../.codex/config.toml) is the portable project MCP configuration; [boost.json](../boost.json) records selected guidance and skills. [config/boost.php](../config/boost.php) disables browser-log instrumentation, so the presence of `browser-logs` does not mean new browser logs are being captured. The generated Laravel Cloud paragraph is generic package guidance, not a hosting decision.

## Supported conventions and representative files

The `infer-conventions` sweep covered HTTP/validation, authorization, models, architecture, frontend, migrations, testing, responses, and utility idioms. Rules record repeated structural choices and explicit existing ownership. Framework scaffolding, sparse examples, and known concerns were excluded. Rule notes stay short; the evidence belongs here.

| Convention or boundary | Evidence and files to consult |
| --- | --- |
| Plural resource folders, singular resource classes, one modal CRUD page | All three resources follow this: [UserResource](../app/Filament/Resources/Users/UserResource.php), [ClientResource](../app/Filament/Resources/Clients/ClientResource.php), [TenantResource](../app/Filament/Resources/Tenants/TenantResource.php). Their `Pages/ManageUsers`, `ManageClients`, and `ManageTenants` extend `ManageRecords` and expose only the index page. Consult [ManageUsers](../app/Filament/Resources/Users/Pages/ManageUsers.php) for the smallest example. |
| Inline schemas and field validation | All three resources contain `form`, `infolist`, and `table` definitions; field validation is on Filament components. This does not establish an HTTP Form Request policy. |
| UI orchestration uses direct models in existing callbacks/components | [TenantResource](../app/Filament/Resources/Tenants/TenantResource.php), [ManageTenants](../app/Filament/Resources/Tenants/Pages/ManageTenants.php), and [TenantSwitcher](../app/Livewire/TenantSwitcher.php). No established Actions/Services/Repositories/DTO layer exists; add an abstraction only when the requested work warrants it. |
| Explicit central ownership | [User](../app/Models/User.php), [Role](../app/Models/Role.php), [Permission](../app/Models/Permission.php), and [Tenant](../app/Models/Tenant.php) use `CentralConnection`. The configured Stancl Domain model also uses the central connection. |
| Explicit tenant ownership and migration split | [Client](../app/Models/Client.php) uses `TenantConnection`. Six central migrations live in [database/migrations](../database/migrations); three tenant migrations live in [database/migrations/tenant](../database/migrations/tenant), selected by [config/tenancy.php](../config/tenancy.php). This is an existing ownership boundary, not a rule assigning every future entity to a database. |
| Policy permission names | Four policies contain 44 `can('Action:Model')` checks. Consult [ClientPolicy](../app/Policies/ClientPolicy.php), [UserPolicy](../app/Policies/UserPolicy.php), [RolePolicy](../app/Policies/RolePolicy.php), [TenantPolicy](../app/Policies/TenantPolicy.php), and [Shield configuration](../config/filament-shield.php). PascalCase and colon separation are configured explicitly. Missing authorization checks are not conventions. |
| Frontend and panel boundary | [AdminPanelProvider](../app/Providers/Filament/AdminPanelProvider.php) configures the panel, Amber color, and switcher render hook; [switcher view](../resources/views/filament/components/tenant-switcher.blade.php) uses native Filament components. [vite.config.js](../vite.config.js) and [app.css](../resources/css/app.css) establish the Vite/Tailwind stack. Preserve this identity without treating starter welcome-page styling as product design. |
| Tenant identification and infrastructure | Session-based selection is in [InitializeTenancyFromSession](../app/Http/Middleware/InitializeTenancyFromSession.php); domain-based tenant routing is in [routes/tenant.php](../routes/tenant.php). [TenancyServiceProvider](../app/Providers/TenancyServiceProvider.php) owns the configured database lifecycle pipelines. These references show current responsibilities, not settled access or lifecycle requirements. |

The only application controller is an empty base class. There are no substantive API controllers, custom application jobs, or fleet-domain modules from which to infer additional patterns. `UniqueDomain` and the custom Livewire switcher are isolated implementations; two starter tests do not establish fixture, isolation, or assertion conventions.

### Git workflow and its evidence

- Use one feature branch for the whole feature and reuse it for follow-up work. Existing names before Boost were `feature/filament-admin-panel` and `feature/multi-tenant`; `feature/<short-kebab-case-topic>` follows that evidence. Do not create a branch for each implementation subtask.
- `main` is the integration branch: `1ac759e` merged `feature/filament-admin-panel`, and `feature/multi-tenant` continues from it. Use `main` for independent work; use the owning feature branch when the work depends on unmerged changes. History supports this baseline, not a universal requirement to rebase or branch from the latest remote commit. Clarify only when the intended base is genuinely unclear.
- This tooling feature continues on `feature/laravel-boost`, branched from `feature/multi-tenant` at `ec499bf`. The two installation commits remain intact. Do not switch this ongoing work to `main` merely because it is documentation.
- Pre-Boost messages include `feat: add filament admin panel` (`27c26dc`), `refactor: ensure User passwords are hashed before saving by using dehydrateStateUsing` (`500be50`), `chore: regenerate policies for existing and new models` (`7562207`), and `fix(filament): register tenancy init as persistent panel middleware` (`ec499bf`). Follow `type: subject`, allowing an existing optional scope. History also contains `enhance:` and an unprefixed initial commit; no new restricted type list, capitalization rule, or mandatory scope is imposed.
- Make focused, coherent commits, stage only task files, and preserve unrelated changes. Push or merge only with authorization. Worktree workflows must respect the same feature-branch continuity.

## Findings and verification limits

The following are compact reminders of the assessment's source findings. No application failure was reproduced during onboarding, Boost installation, or this documentation task. Source inspection establishes what code is present; it does not establish production symptoms, effective credentials, or product intent.

| Source finding | Evidence / status |
| --- | --- |
| Tenant root outputs client records with no route authentication | Re-read [routes/tenant.php](../routes/tenant.php). Whether any public client listing is intended is unresolved. |
| Panel access outside `local` may be denied | Assessment traced the missing `FilamentUser`/`canAccessPanel` contract in [User](../app/Models/User.php) against installed Filament middleware. No non-local login was exercised. |
| Bulk-delete permission coverage is incomplete | All four policies and Shield's generated method list omit `deleteAny`, while resources expose bulk deletion. The assessment traced Filament's authorization behavior; no bulk action was executed. |
| Empty/stale tenant selection has unsafe paths | Re-read [TenantSwitcher](../app/Livewire/TenantSwitcher.php), its view, and session middleware: first-tenant/null dereferences and a stale selection path remain. Membership restrictions are not demonstrated. |
| Tenant creation has two migration invocation paths | [TenancyServiceProvider](../app/Providers/TenancyServiceProvider.php) migrates on creation; [ManageTenants](../app/Filament/Resources/Tenants/Pages/ManageTenants.php) calls tenant migrations again. Deletion also triggers database deletion synchronously. Failure recovery was not exercised. |
| Cache/queue deployment choices need validation | The assessment identified database-cache defaults alongside tenant cache tagging and an environment-dependent database queue connection. [tenancy.php](../config/tenancy.php), [cache.php](../config/cache.php), and [queue.php](../config/queue.php) do not prove effective deployment settings; private environment values were not inspected. |
| Domain validation/editing and switcher scale need review | [UniqueDomain](../app/Rules/UniqueDomain.php) and TenantResource query `domains` through the ambient connection; tenant editing updates all domains; the switcher fetches a domain per tenant. These are caveats, not approved conventions. |

The assessment also rejected two false positives: Filament's `unique()` ignores the current record by default, and Laravel's hashed cast recognizes existing hashes. The client `canCreate()` override alone is not evidence of a create-policy bypass. Recheck installed APIs before turning any finding into a change.

Still unverified: application and browser behavior, actual role assignments and tenant access, database connectivity, effective deployment configuration, provisioning/recovery/deletion behavior, queue processing, frontend builds, and application test success.

### Running and testing: prerequisites, not execution results

[composer.json](../composer.json) defines `composer run dev` to start the server, queue listener, Pail, and Vite; `npm run dev` and `npm run build` operate the frontend. A usable environment needs dependencies, an application key, central database, matching central/tenant hostnames, cache/session/queue choices, and initial permission/tenant data. Sail is installed but no Compose configuration was found. Do not run `composer run setup` as a routine check: it generates a key and force-runs migrations.

The suite is only [Unit/ExampleTest](../tests/Unit/ExampleTest.php) and [Feature/ExampleTest](../tests/Feature/ExampleTest.php). `composer test` clears configuration before invoking Artisan tests; `vendor/bin/phpunit` is also available. [phpunit.xml](../phpunit.xml) requests SQLite `:memory:`, array cache/session/mail, and synchronous queues, without forcing environment overrides. No `.env.testing` or cached Laravel configuration was found at inspection time.

Before meaningful application tests, verify isolated effective connections (including inherited variables without exposing secrets), set the central request hostname, provide disposable tenant databases and storage with explicit cleanup, choose a tenancy-compatible reset strategy, and supply panel access plus role/permission fixtures. Synchronous tenant events can create or destroy databases. No application tests are needed or claimed for this documentation-only task.

## Open decisions

These require product or maintainer input; existing behavior is not an answer.

1. **Tenant access and privileged roles:** are administrators global or restricted by tenant membership, who may switch tenants, and who may assign privileged roles?
2. **Public routes:** is tenant-root client output temporary, should any client directory be public, and should tenant-facing users authenticate through the configured `client` guard?
3. **Tenant lifecycle:** what should an empty installation or stale selection do; may tenant IDs change; are multiple domains supported; should deletion immediately destroy the database; what recovery/provisioning guarantees are required?
4. **Deployment and tests:** is PHP 8.4 the supported baseline, and which database/cache/session/queue/hosting arrangement and disposable test infrastructure should be supported?
5. **Inconsistent style:** Role/Tenant policies declare strict types and accept record parameters; User/Client policies do neither (two versus two). Is there an intended context boundary? Otherwise, which preference should a later scoped reconciliation follow? Import order, spacing, and broader Pint normalization also remain undecided. `.editorconfig` is the existing settings baseline; no custom Pint/Rector configuration establishes an additional personal standard.

## Maintaining guidance

Edit project-wide instructions in [.ai/guidelines/project.blade.php](../.ai/guidelines/project.blade.php), then run `php artisan boost:install --guidelines --skills --no-interaction` and inspect the diff. Boost 2.10.2 discovers this directory and preserves the generated block boundaries in AGENTS.md. The existing MCP configuration and runtime settings should remain unchanged by this guidance-only regeneration.

Use Boost's `record-rule` tool for approved durable rules. It writes area files and regenerates [.ai/rules/index.md](../.ai/rules/index.md); preserve those mappings and AGENTS.md's instruction to read matching rules and search for related terms before editing. Repeated rule text in two areas is intentional when both file families need discovery. Keep source evidence and unresolved choices here, rather than adding them to every rule or replacing generated package guidance.
