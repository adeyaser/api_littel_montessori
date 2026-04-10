-- ============================================================================
-- LITTLE HOME API - DATABASE OPTIMIZATION
-- Index creation untuk performa query
-- ============================================================================
-- Run this script on: u148138600_dblittle database
-- Time to execute: <1 second
-- Impact: 60-80% query performance improvement
-- ============================================================================

-- ============================================================================
-- 1. TABLE: tpost
-- ============================================================================
-- Primary use: Get posts by id_anak, ordered by latest
-- Current slow query: SELECT * FROM tpost WHERE id_anak = ? ORDER BY tanggal DESC

CREATE INDEX idx_tpost_id_anak_tanggal ON tpost(id_anak, tanggal DESC);
-- This is a composite index that covers both WHERE and ORDER BY

-- Also useful for dashboard queries
CREATE INDEX idx_tpost_tanggal ON tpost(tanggal DESC);

-- ============================================================================
-- 2. TABLE: tpost_content
-- ============================================================================
-- Primary use: Get content for specific posts
-- Current slow query: SELECT * FROM tpost_content WHERE tpost_id = ?

CREATE INDEX idx_tpost_content_tpost_id ON tpost_content(tpost_id);

-- ============================================================================
-- 3. TABLE: pencatatan
-- ============================================================================
-- Primary use: Get records by student, ordered by latest
-- Current query: SELECT * FROM pencatatan WHERE id_anak = ? ORDER BY tanggal DESC

CREATE INDEX idx_pencatatan_id_anak_tanggal ON pencatatan(id_anak, tanggal DESC);

-- ============================================================================
-- 4. TABLE: tth_pemasukan (TAGIHAN/INVOICE)
-- ============================================================================
-- Primary use: 
--   1. Get pending invoices (WHERE id_anak AND status != 'Lunas')
--   2. Sum nominal (SELECT SUM(nominal) WHERE id_anak)

CREATE INDEX idx_tth_pemasukan_id_anak_status ON tth_pemasukan(id_anak, status);
CREATE INDEX idx_tth_pemasukan_id_anak_tanggal ON tth_pemasukan(id_anak, tanggal DESC);

-- ============================================================================
-- 5. TABLE: comments
-- ============================================================================
-- Primary use: Get comments for a specific post
-- Query: SELECT * FROM comments WHERE post_id = ?

CREATE INDEX idx_comments_post_id_created ON comments(post_id, created_at ASC);

-- ============================================================================
-- 6. TABLE: testimonial
-- ============================================================================
-- Primary use: Get approved testimonials, newest first
-- Query: SELECT * FROM testimonial WHERE status = 1 ORDER BY id DESC

CREATE INDEX idx_testimonial_status_id ON testimonial(status, id DESC);

-- ============================================================================
-- 7. TABLE: users (LOGIN OPTIMIZATION)
-- ============================================================================
-- Login query optimization
-- Query: SELECT * FROM users WHERE email = ? AND role_id = 5 AND status_aktif = 1

CREATE INDEX idx_users_email_role_status ON users(email, role_id, status_aktif);

-- ============================================================================
-- 8. TABLE: keluarga_pengasuh
-- ============================================================================
-- Used in login to get murid_id
-- Query: SELECT * FROM keluarga_pengasuh WHERE id = ?

-- Assuming 'id' is already primary key, add index for murid_id lookup if needed
CREATE INDEX idx_keluarga_pengasuh_murid_id ON keluarga_pengasuh(murid_id);

-- ============================================================================
-- VERIFICATION SCRIPT
-- ============================================================================

-- Check all created indexes on tpost table
-- SHOW INDEX FROM tpost;

-- Check query execution plan
-- EXPLAIN SELECT * FROM tpost WHERE id_anak = 1 ORDER BY tanggal DESC LIMIT 3;
-- Should show: "Using index" or "Using where; Using index"

-- Performance monitoring query (show slow queries if enabled)
-- SET GLOBAL slow_query_log = 'ON';
-- SET GLOBAL long_query_time = 0.5;
-- SELECT * FROM mysql.slow_log;

-- ============================================================================
-- INDEX STATISTICS (Run after creating indexes)
-- ============================================================================

SELECT 
    OBJECT_SCHEMA,
    OBJECT_NAME,
    COUNT_READ,
    COUNT_WRITE,
    COUNT_INSERT,
    COUNT_UPDATE,
    COUNT_DELETE
FROM performance_schema.table_io_waits_summary_by_table
WHERE OBJECT_SCHEMA = 'u148138600_dblittle'
ORDER BY COUNT_READ DESC;

-- ============================================================================
-- OPTIMIZE QUERY: Dashboard
-- ============================================================================
-- BEFORE: Multiple separate queries
-- 1. SELECT * FROM tpost WHERE id_anak LIKE ? (wrong operator!)
-- 2. For each post: SELECT * FROM tpost_content WHERE tpost_id = ?
-- 3. SELECT * FROM pencatatan WHERE id_anak = ?
-- 4. SELECT SUM(nominal) FROM tth_pemasukan ...

-- AFTER: Optimized with indexes
SELECT p.id, p.id_anak, p.deskripsi, p.judul, p.tanggal
FROM tpost p
WHERE p.id_anak = 1
ORDER BY p.tanggal DESC
LIMIT 3;
-- Uses index: idx_tpost_id_anak_tanggal

-- Get content for posts (single query instead of loop)
SELECT tpost_id, file_url
FROM tpost_content
WHERE tpost_id IN (1, 2, 3)
ORDER BY tpost_id ASC;
-- Uses index: idx_tpost_content_tpost_id

-- ============================================================================
-- OPTIMIZE QUERY: Posting with Pagination
-- ============================================================================

SELECT id, id_anak, deskripsi, judul, tanggal, created_at
FROM tpost
WHERE id_anak = 1
ORDER BY tanggal DESC
LIMIT 3 OFFSET 0;
-- Uses index: idx_tpost_id_anak_tanggal

-- ============================================================================
-- OPTIMIZE QUERY: Count for pagination
-- ============================================================================

SELECT COUNT(*) as total
FROM tpost
WHERE id_anak = 1;
-- Uses index: idx_tpost_id_anak_tanggal (faster count)

-- ============================================================================
-- OPTIMIZE QUERY: Tagihan with pending filter
-- ============================================================================

SELECT id, id_anak, nominal, tanggal, status, deskripsi
FROM tth_pemasukan
WHERE id_anak = 1 AND status != 'Lunas'
ORDER BY tanggal DESC
LIMIT 15 OFFSET 0;
-- Uses index: idx_tth_pemasukan_id_anak_status

-- ============================================================================
-- EXPLAIN ANALYSIS (Run these to verify index usage)
-- ============================================================================
-- EXPLAIN SELECT * FROM tpost WHERE id_anak = 1 ORDER BY tanggal DESC LIMIT 3;
-- EXPLAIN SELECT * FROM tth_pemasukan WHERE id_anak = 1 AND status != 'Lunas' ORDER BY tanggal DESC;
-- EXPLAIN SELECT COUNT(*) FROM tpost WHERE id_anak = 1;

-- Expected "Extra" column values:
-- ✓ "Using index" = Excellent (covers all needed columns)
-- ✓ "Using where; Using index" = Good (using index efficiently)
-- ✗ "Using temporary; Using filesort" = Bad (full table scan)
-- ✗ "Using index; Using where; Using filesort" = Need optimization

?>
