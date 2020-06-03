
## How to dump functions

```sql
SELECT 
    pg_get_functiondef(f.oid)

FROM 
    pg_catalog.pg_proc f
    
    INNER JOIN pg_catalog.pg_namespace n 
        ON f.pronamespace = n.oid

WHERE 
    n.nspname = 'public';
```