---
paths:
  - 'database/migrations/**'
---

# Migrations

## Central and tenant migration separation
Keep central schema changes in database/migrations and tenant schema changes in database/migrations/tenant; preserve their separate migration execution contexts.
