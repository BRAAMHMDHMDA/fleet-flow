---
paths:
  - 'app/Models/**'
---

# Models

## Central and tenant model ownership
Keep users, roles, permissions, tenants, and domains on the central connection, and clients on the tenant connection. Make connection ownership explicit when extending these models and preserve it in related validation and background work.
