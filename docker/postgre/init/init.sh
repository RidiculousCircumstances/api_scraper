#!/bin/sh

PARAMS=(
    max_connections=50
    hared_buffers=1GB
    effective_cache_size=4GB
    work_mem=16MB
    maintenance_work_mem=512MB
    random_page_cost=1.1
    temp_file_limit=10GB
    log_min_duration_statement=200ms
    idle_in_transaction_session_timeout=10s
    lock_timeout=1s
    statement_timeout=60s
    shared_preload_libraries=pg_stat_statements
    pg_stat_statements.max=10000
    pg_stat_statements.track=all
)

for i in "${PARAMS[@]}"
do
    postgres -c "$i"
done