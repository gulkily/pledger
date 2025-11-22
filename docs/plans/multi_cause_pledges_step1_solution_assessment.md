# Multi-cause Pledges · Step 1 Solution Assessment

**Problem statement**: The pledger app only supports a single funding cause, but Ilya needs one deployment that can accept, display, and manage pledges for multiple concurrent causes without cloning the project.

**Option 1 – Clone-and-configure deployments per cause**
- Pros: Zero schema changes; lowest engineering lift by copying existing config and DB per cause; easy rollback because each cause is isolated.
- Cons: Operational overhead explodes as causes grow; pledger UI can’t surface all causes in one experience; analytics and supporter management stay fragmented; updating UI/features requires touching every clone.

**Option 2 – Multi-DB routing (one SQLite file per cause selected by slug) – recommended for now**
- Pros: Keeps pledges isolated per cause to match "one directory per cause" expectations while still sharing the codebase; schema changes are limited to wiring a cause slug/identifier into routing; rollback stays simple by dropping or archiving a single DB file; future merge to a shared DB stays possible once we’re ready.
- Cons: Requires new routing/auth plumbing to safely select DB per request and avoid leakage; aggregate analytics still need extra logic to read every DB; ops overhead includes keeping each cause directory’s config and assets in sync.

**Option 3 – Single DB with cause metadata**
- Pros: Introduces true multi-tenancy with shared UI that lists causes and filters pledges by `cause_slug`; enables cross-cause reporting and future automation (e.g., highlight funding gaps); only one deployment to maintain; supporters keep one session for all pledges.
- Cons: Requires a modest schema change (cause definitions + column on pledges) and more front-end work (cause picker, cause-specific stats); must ensure legacy pledges map to a default cause during migration.

**Recommendation**: Option 2. It satisfies the near-term requirement of keeping one directory per cause while reusing the same code and leaves the door open to a future merge into a single-db design once operational needs stabilize.
