---
paths:
  - 'app/Policies/**'
---

# Policies

## Resource permission naming
Use resource policies that delegate to the authenticated user's permission check with PascalCase Action:Model names. Preserve existing permission names when extending authorization; policy signature preferences remain unresolved.
