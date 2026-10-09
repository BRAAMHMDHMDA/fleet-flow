# Project status and agent onboarding

Updated on 2026-10-08 to reflect the owner's onboarding clarification. The dependency/tooling baseline was verified by the agent on 2026-10-07; owner verification and agent checks are distinguished below.

Context: the local `ONBOARDING_ASSESSMENT.md` dated 2026-10-06 remains untouched and untracked. It is not required to use this committed guide. Its statement that Boost was unavailable describes the earlier assessment and is superseded by the installation below. Its fuller findings remain local context; this document summarizes only the decisions and caveats needed for subsequent work.

## Working features and onboarding scope

The project owner developed this application before adopting AI agents and confirms that **multi-tenancy, roles/permissions, and user management are implemented, tested, and working for current use cases**. Continue development from that working baseline and preserve the existing organization and Shield/Spatie integration. The owner's statement does not specify every tested scenario or environment; do not invent that detail or discount the statement because only starter automated tests are checked in.

| Verification source | What it establishes |
| --- | --- |
| Project owner | Multi-tenancy, roles/permissions, and user management were implemented and tested for the owner's current use cases. |
| Agent tooling checks | Installed versions, Composer/platform checks during Boost installation, live MCP calls, generated guidance, and documentation/diff checks were independently checked. |
| Agent source review | Representative code, connection ownership, authorization paths, UI structure, and potential edge cases were inspected. No application failure was reproduced and no application test suite or browser acceptance flow was executed by the agent. |
| Requirements interview | Individual intended-behavior decisions were confirmed in conversation. They are separate from verified behavior and do not authorize implementation. |

The authorization interview and proposed central-panel admission task are paused at the owner's request. The combined proposed requirements summary was not approved as an implementation plan. Source observations below are context for a relevant future feature, not approved fixes or prerequisites to continuing development. If a concrete correctness or security issue affects the requested feature, explain it and propose the smallest appropriate change within the existing architecture.

Onboarding is complete for continuing development: the conventions, tooling, evidence, and verification limits are documented. No onboarding blocker was identified. The next feature is for the owner to choose; do not select or start one autonomously.

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
- The 2026-10-07 guidance pass successfully called `application-info` again. A documentation query scoped to Boost returned unrelated results, including other package majors; those results were rejected. The installed Boost 2.10.2 source was used to verify customization and regeneration behavior.
- Discoverable tools: `application-info`, `search-docs`, `database-connections`, `database-query`, `database-schema`, `get-absolute-url`, `browser-logs`, `read-log-entries`, `last-error`, and `record-rule`. Discovery does not imply every tool has been exercised.
- Direct `record-rule` calls in the 2026-10-07 guidance session were blocked by its MCP approval policy (`never`). With explicit execution approval, the same tool was successfully invoked through the local Boost MCP server to generate `.ai/rules` and its index. This is a historical session limitation, not a statement of the current client's approval policy. The client configuration was not changed.
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
| Existing Shield/Spatie integration | The panel registers `FilamentShieldPlugin`; `User` uses `HasRoles`; [permission configuration](../config/permission.php) selects the application's central Role/Permission models with teams disabled. Shield registers RolePolicy, and [UserResource](../app/Filament/Resources/Users/UserResource.php) edits the roles relationship. Extend this integration rather than introducing a parallel authorization system or duplicate permissions. |
| Frontend and panel boundary | [AdminPanelProvider](../app/Providers/Filament/AdminPanelProvider.php) configures the panel, Amber color, and switcher render hook; [switcher view](../resources/views/filament/components/tenant-switcher.blade.php) uses native Filament components. [vite.config.js](../vite.config.js) and [app.css](../resources/css/app.css) establish the Vite/Tailwind stack. Preserve this identity without treating starter welcome-page styling as product design. |
| Tenant identification and infrastructure | Session-based selection is in [InitializeTenancyFromSession](../app/Http/Middleware/InitializeTenancyFromSession.php); domain-based tenant routing is in [routes/tenant.php](../routes/tenant.php). [TenancyServiceProvider](../app/Providers/TenancyServiceProvider.php) owns the configured database lifecycle pipelines. These references show current responsibilities, not settled access or lifecycle requirements. |

The only application controller is an empty base class. There are no substantive API controllers, custom application jobs, or fleet-domain modules from which to infer additional patterns. `UniqueDomain` and the custom Livewire switcher are isolated implementations; two starter tests do not establish fixture, isolation, or assertion conventions.

### Git workflow and its evidence

- Use one feature branch for the whole feature and reuse it for follow-up work. Existing names before Boost were `feature/filament-admin-panel` and `feature/multi-tenant`; `feature/<short-kebab-case-topic>` follows that evidence. Do not create a branch for each implementation subtask.
- `main` is the integration branch: `1ac759e` merged `feature/filament-admin-panel`, and `feature/multi-tenant` continues from it. Use `main` for independent work; use the owning feature branch when the work depends on unmerged changes. History supports this baseline, not a universal requirement to rebase or branch from the latest remote commit. Clarify only when the intended base is genuinely unclear.
- This tooling feature continues on `feature/laravel-boost`, branched from `feature/multi-tenant` at `ec499bf`. The two installation commits remain intact. Do not switch this ongoing work to `main` merely because it is documentation.
- Pre-Boost messages include `feat: add filament admin panel` (`27c26dc`), `refactor: ensure User passwords are hashed before saving by using dehydrateStateUsing` (`500be50`), `chore: regenerate policies for existing and new models` (`7562207`), and `fix(filament): register tenancy init as persistent panel middleware` (`ec499bf`). Follow `type: subject`, allowing an existing optional scope. History also contains `enhance:` and an unprefixed initial commit; no new restricted type list, capitalization rule, or mandatory scope is imposed.
- Make focused, coherent commits, stage only task files, and preserve unrelated changes. Push or merge only with authorization. Worktree workflows must respect the same feature-branch continuity.

## Source observations and agent verification limits

The following preserve useful observations from the assessment and subsequent read-only review. They do not contradict the owner's report that the implemented features work for current use cases. No application failure was reproduced by the agent. Their applicability to an actual requested feature or environment must be established before proposing a change; they are not approved implementation tasks.

| Source finding | Evidence / status |
| --- | --- |
| Tenant root contains client-data output with no route authentication | [routes/tenant.php](../routes/tenant.php) contains this output. The owner identified these as temporary development routes in the interview below; no endpoint was exercised or changed by the agent. |
| Panel access outside `local` may be denied | Assessment traced the missing `FilamentUser`/`canAccessPanel` contract in [User](../app/Models/User.php) against installed Filament middleware. No non-local login was exercised. |
| Bulk-delete policy method is absent | All four policies and Shield's generated method list omit `deleteAny`, while resources expose bulk deletion. The assessment traced Filament's authorization behavior; no bulk action was executed. |
| Empty/stale tenant selection has potential edge cases | [TenantSwitcher](../app/Livewire/TenantSwitcher.php), its view, and session middleware contain first-tenant/null dereferences and a stale selection path. No such failure was reproduced. Missing membership restrictions are not themselves a defect: the owner selected global tenant access subject to action permissions. |
| Tenant creation has two migration invocation paths | [TenancyServiceProvider](../app/Providers/TenancyServiceProvider.php) migrates on creation; [ManageTenants](../app/Filament/Resources/Tenants/Pages/ManageTenants.php) calls tenant migrations again. Deletion also triggers database deletion synchronously. Failure recovery was not exercised. |
| Cache/queue defaults depend on the deployment environment | The assessment identified database-cache defaults alongside tenant cache tagging and an environment-dependent database queue connection. [tenancy.php](../config/tenancy.php), [cache.php](../config/cache.php), and [queue.php](../config/queue.php) do not prove effective deployment settings; private environment values were not inspected. |
| Domain queries and switcher query shape | [UniqueDomain](../app/Rules/UniqueDomain.php) and TenantResource query `domains` through the ambient connection; tenant editing updates all domains; the switcher fetches a domain per tenant. No connection error or performance failure was reproduced. |
| User-role assignment and Role operations use different authorization paths | The read-only review traced User form relationship saving separately from RolePolicy checks. UserPolicy delegates to User permissions, while RolePolicy delegates to Role permissions; no protected-target check was found in UserPolicy. These are source observations to compare with the owner's stated intent when relevant, not a claim that the owner's tested scenarios failed. |

The assessment also rejected two false positives: Filament's `unique()` ignores the current record by default, and Laravel's hashed cast recognizes existing hashes. The client `canCreate()` override alone is not evidence of a create-policy bypass. Recheck installed APIs before turning any finding into a change.

Not independently verified by the agent: application/browser behavior, actual database role assignments and tenant access, database connectivity, effective deployment configuration, provisioning/recovery/deletion behavior, queue processing, frontend builds, and application test success. This describes the agent's checks only; the owner's implemented-and-tested status above remains the working baseline.

### Running and testing: prerequisites, not execution results

[composer.json](../composer.json) defines `composer run dev` to start the server, queue listener, Pail, and Vite; `npm run dev` and `npm run build` operate the frontend. A usable environment needs dependencies, an application key, central database, matching central/tenant hostnames, cache/session/queue choices, and initial permission/tenant data. Sail is installed but no Compose configuration was found. Do not run `composer run setup` as a routine check: it generates a key and force-runs migrations.

The checked-in automated suite contains [Unit/ExampleTest](../tests/Unit/ExampleTest.php) and [Feature/ExampleTest](../tests/Feature/ExampleTest.php); this is not an inventory of all testing performed by the owner. `composer test` clears configuration before invoking Artisan tests; `vendor/bin/phpunit` is also available. [phpunit.xml](../phpunit.xml) requests SQLite `:memory:`, array cache/session/mail, and synchronous queues, without forcing environment overrides. No `.env.testing` or cached Laravel configuration was found at inspection time.

Before meaningful application tests, verify isolated effective connections (including inherited variables without exposing secrets), set the central request hostname, provide disposable tenant databases and storage with explicit cleanup, choose a tenancy-compatible reset strategy, and supply panel access plus role/permission fixtures. Synchronous tenant events can create or destroy databases. No application tests are needed or claimed for this documentation-only task.

## Owner-confirmed intent from the paused interview

These individual decisions were explicitly confirmed by the owner. They are retained as intent, not as independently verified implementation behavior, a newly approved implementation plan, or instructions to reopen completed features. Do not repeat the interview unless a future requested feature exposes an actual ambiguity or contradiction.

- Every authenticated central user may enter the central panel without a dedicated panel-access role; tenant-side client accounts may not. Panel entry grants no additional action permissions.
- Central panel users may access any tenant, subject to their resource/action permissions. Assigned-tenant restrictions were not requested.
- `super_admin` is the primary administrator with full authority over central users, roles, permissions, and role assignments, enforced through the existing Shield/Spatie system rather than inferred from the name alone.
- Creating, editing, and deleting central role definitions, changing their permissions, and assigning/replacing/removing users' roles are exclusive to `super_admin`, including assignments during user creation and self-editing. Existing Role permissions alone do not grant these operations to other users. No delegation mechanism is requested; this supersedes the earlier delegable-authority preference and rejects the proposed automatic reuse of `Update:Role` for assignment.
- Other users may manage ordinary user details according to existing User permissions while preserving role assignments. Non-super-admin users must not edit or delete `super_admin` accounts.
- Tenant-facing routes were identified as temporary development routes; the owner selected disabling their client-data output until tenant-facing functionality is defined. No route change was authorized as part of onboarding.
- If a selected tenant becomes unavailable, clear the selection, stop tenant-data operations, show a notice, and require an explicit choice; show an empty state when none are available.
- Hide unauthorized navigation/actions and reject direct attempts server-side without changing data; forbidden page access should show 403 and action requests a clear denial.

The agent's combined acceptance-criteria summary was presented for confirmation but not approved as a whole. Additional inferences in that draft, such as the exact workflow for a non-super-admin creating a roleless user, must not silently become settled requirements. The proposed central-panel admission implementation remains paused.

## Deferred context

These are not onboarding blockers or a mandatory cleanup queue. Revisit only when relevant to the feature the owner requests:

- Initial tenant selection, mutable tenant IDs, multiple-domain semantics, deletion/recovery guarantees, and last-super-administrator protection were not settled by the interview.
- Supported deployment and disposable test infrastructure remain to be clarified for work that depends on them; the installed PHP baseline is an observation, not a new deployment mandate.
- Strict types, policy record-parameter signatures, import order, spacing, and broad Pint normalization remain mixed. Match neighboring files and `.editorconfig`; do not establish a new standard as incidental cleanup.

## Maintaining guidance

Edit project-wide instructions in [.ai/guidelines/project.blade.php](../.ai/guidelines/project.blade.php), then run `php artisan boost:install --guidelines --skills --no-interaction` and inspect the diff. Boost 2.10.2 discovers this directory and preserves the generated block boundaries in AGENTS.md. The existing MCP configuration and runtime settings should remain unchanged by this guidance-only regeneration.

Use Boost's `record-rule` tool for approved durable rules. It writes area files and regenerates [.ai/rules/index.md](../.ai/rules/index.md); preserve those mappings and AGENTS.md's instruction to read matching rules and search for related terms before editing. Repeated rule text in two areas is intentional when both file families need discovery. Keep source evidence and unresolved choices here, rather than adding them to every rule or replacing generated package guidance.
