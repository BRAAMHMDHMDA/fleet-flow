---
paths:
  - 'app/Filament/Resources/**'
---

# Resources

## Resource organization and modal CRUD
Keep simple resources in plural resource folders with a singular resource class and a Pages/Manage{PluralName} page extending ManageRecords. Use that page's index route and modal actions for CRUD.

## Inline resource schemas
Keep form, infolist, and table definitions inline in the resource class, with field validation on the form components. Reuse existing implementations before extracting separate schema or table classes.

## UI orchestration
Keep existing UI orchestration in resource/page callbacks and Livewire components, using models directly. Introduce an additional action, service, or repository layer only when the task warrants it; preserve the separate tenancy lifecycle pipelines.
