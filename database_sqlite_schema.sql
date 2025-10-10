-- =====================================================
-- MEETING ROOM BOOKING SYSTEM - SQLITE SCHEMA
-- =====================================================
-- Script untuk SQLite database
-- Sesuai dengan struktur Laravel migration

-- =====================================================
-- 1. CACHE TABLES
-- =====================================================

CREATE TABLE IF NOT EXISTS cache (
    key TEXT PRIMARY KEY,
    value TEXT NOT NULL,
    expiration INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS cache_locks (
    key TEXT PRIMARY KEY,
    owner TEXT NOT NULL,
    expiration INTEGER NOT NULL
);

-- =====================================================
-- 2. JOB QUEUE TABLES
-- =====================================================

CREATE TABLE IF NOT EXISTS jobs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    queue TEXT NOT NULL,
    payload TEXT NOT NULL,
    attempts INTEGER NOT NULL,
    reserved_at INTEGER,
    available_at INTEGER NOT NULL,
    created_at INTEGER NOT NULL
);

CREATE INDEX IF NOT EXISTS jobs_queue_index ON jobs(queue);

CREATE TABLE IF NOT EXISTS job_batches (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    total_jobs INTEGER NOT NULL,
    pending_jobs INTEGER NOT NULL,
    failed_jobs INTEGER NOT NULL,
    failed_job_ids TEXT NOT NULL,
    options TEXT,
    cancelled_at INTEGER,
    created_at INTEGER NOT NULL,
    finished_at INTEGER
);

CREATE TABLE IF NOT EXISTS failed_jobs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    uuid TEXT UNIQUE NOT NULL,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload TEXT NOT NULL,
    exception TEXT NOT NULL,
    failed_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- 3. USER MANAGEMENT TABLES
-- =====================================================

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    name TEXT NOT NULL,
    full_name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    email_verified_at DATETIME,
    email_verification_token TEXT,
    password TEXT NOT NULL,
    phone TEXT,
    department TEXT,
    role TEXT DEFAULT 'user' CHECK (role IN ('admin', 'user')),
    avatar TEXT,
    last_login_at DATETIME,
    remember_token TEXT,
    created_at DATETIME,
    updated_at DATETIME
);

CREATE INDEX IF NOT EXISTS users_email_index ON users(email);
CREATE INDEX IF NOT EXISTS users_username_index ON users(username);
CREATE INDEX IF NOT EXISTS users_role_index ON users(role);

CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email TEXT PRIMARY KEY,
    token TEXT NOT NULL,
    created_at DATETIME
);

CREATE TABLE IF NOT EXISTS sessions (
    id TEXT PRIMARY KEY,
    user_id INTEGER,
    ip_address TEXT,
    user_agent TEXT,
    payload TEXT NOT NULL,
    last_activity INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS sessions_user_id_index ON sessions(user_id);
CREATE INDEX IF NOT EXISTS sessions_last_activity_index ON sessions(last_activity);

CREATE TABLE IF NOT EXISTS admin_sessions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    session_id TEXT UNIQUE NOT NULL,
    last_activity DATETIME,
    created_at DATETIME,
    updated_at DATETIME
);

-- =====================================================
-- 4. MEETING ROOM TABLES
-- =====================================================

CREATE TABLE IF NOT EXISTS meeting_rooms (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    capacity INTEGER NOT NULL,
    amenities TEXT, -- JSON string
    location TEXT NOT NULL,
    hourly_rate REAL DEFAULT 0.00,
    images TEXT, -- JSON string
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME,
    updated_at DATETIME
);

CREATE INDEX IF NOT EXISTS meeting_rooms_is_active_index ON meeting_rooms(is_active);
CREATE INDEX IF NOT EXISTS meeting_rooms_capacity_index ON meeting_rooms(capacity);

-- =====================================================
-- 5. BOOKING TABLES
-- =====================================================

CREATE TABLE IF NOT EXISTS bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    meeting_room_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    description TEXT,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'confirmed', 'cancelled', 'completed')),
    attendees_count INTEGER DEFAULT 1,
    attendees TEXT, -- JSON string
    attachments TEXT, -- JSON string
    special_requirements TEXT,
    total_cost REAL DEFAULT 0.00,
    cancelled_at DATETIME,
    cancellation_reason TEXT,
    created_at DATETIME,
    updated_at DATETIME,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (meeting_room_id) REFERENCES meeting_rooms(id) ON DELETE CASCADE
);

-- Indexes for optimization
CREATE INDEX IF NOT EXISTS bookings_meeting_room_time_index ON bookings(meeting_room_id, start_time, end_time);
CREATE INDEX IF NOT EXISTS bookings_user_time_index ON bookings(user_id, start_time);
CREATE INDEX IF NOT EXISTS bookings_status_index ON bookings(status);
CREATE INDEX IF NOT EXISTS bookings_start_time_index ON bookings(start_time);
CREATE INDEX IF NOT EXISTS bookings_end_time_index ON bookings(end_time);

-- =====================================================
-- 6. SAMPLE DATA
-- =====================================================

-- Insert sample admin user
INSERT OR IGNORE INTO users (username, name, full_name, email, email_verified_at, password, phone, department, role, created_at, updated_at) VALUES
('admin', 'Admin User', 'Administrator', 'admin@example.com', datetime('now'), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+6281234567890', 'IT', 'admin', datetime('now'), datetime('now'));

-- Insert sample regular user
INSERT OR IGNORE INTO users (username, name, full_name, email, email_verified_at, password, phone, department, role, created_at, updated_at) VALUES
('john.doe', 'John Doe', 'John Doe', 'john.doe@example.com', datetime('now'), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+6281234567891', 'Marketing', 'user', datetime('now'), datetime('now'));

-- Insert sample meeting rooms
INSERT OR IGNORE INTO meeting_rooms (name, description, capacity, amenities, location, hourly_rate, images, is_active, created_at, updated_at) VALUES
('Conference Room A', 'Large conference room with modern facilities', 20, '["projector", "whiteboard", "wifi", "ac", "video_conference"]', 'Floor 1, Building A', 150000.00, '["room_a_1.jpg", "room_a_2.jpg"]', 1, datetime('now'), datetime('now')),
('Meeting Room B', 'Medium-sized meeting room for small groups', 8, '["whiteboard", "wifi", "ac"]', 'Floor 2, Building A', 75000.00, '["room_b_1.jpg"]', 1, datetime('now'), datetime('now')),
('Executive Boardroom', 'Premium boardroom for executive meetings', 12, '["projector", "whiteboard", "wifi", "ac", "video_conference", "catering"]', 'Floor 3, Building A', 250000.00, '["boardroom_1.jpg", "boardroom_2.jpg"]', 1, datetime('now'), datetime('now')),
('Training Room', 'Large space for training and workshops', 50, '["projector", "whiteboard", "wifi", "ac", "sound_system"]', 'Floor 1, Building B', 200000.00, '["training_room_1.jpg"]', 1, datetime('now'), datetime('now')),
('Small Meeting Room', 'Intimate space for 1-on-1 meetings', 4, '["wifi", "ac"]', 'Floor 2, Building B', 50000.00, '["small_room_1.jpg"]', 1, datetime('now'), datetime('now'));

-- Insert sample bookings
INSERT OR IGNORE INTO bookings (user_id, meeting_room_id, title, description, start_time, end_time, status, attendees_count, attendees, total_cost, created_at, updated_at) VALUES
(2, 1, 'Weekly Team Meeting', 'Regular weekly team sync meeting', datetime('now', '+1 day'), datetime('now', '+1 day', '+2 hours'), 'confirmed', 5, '["team1@example.com", "team2@example.com", "team3@example.com"]', 300000.00, datetime('now'), datetime('now')),
(2, 2, 'Client Presentation', 'Presentation for new client proposal', datetime('now', '+3 days'), datetime('now', '+3 days', '+1 hour'), 'pending', 3, '["client@example.com", "manager@example.com"]', 75000.00, datetime('now'), datetime('now')),
(2, 3, 'Board Meeting', 'Monthly board of directors meeting', datetime('now', '+7 days'), datetime('now', '+7 days', '+3 hours'), 'confirmed', 8, '["director1@example.com", "director2@example.com", "ceo@example.com"]', 750000.00, datetime('now'), datetime('now'));

-- =====================================================
-- 7. VIEWS FOR COMMON QUERIES
-- =====================================================

-- View for active bookings with user and room details
CREATE VIEW IF NOT EXISTS active_bookings_view AS
SELECT 
    b.id,
    b.title,
    b.description,
    b.start_time,
    b.end_time,
    b.status,
    b.attendees_count,
    b.total_cost,
    u.username,
    u.full_name as user_name,
    u.email as user_email,
    mr.name as room_name,
    mr.location,
    mr.capacity
FROM bookings b
JOIN users u ON b.user_id = u.id
JOIN meeting_rooms mr ON b.meeting_room_id = mr.id
WHERE b.status IN ('pending', 'confirmed')
ORDER BY b.start_time;

-- View for room availability
CREATE VIEW IF NOT EXISTS room_availability_view AS
SELECT 
    mr.id,
    mr.name,
    mr.capacity,
    mr.location,
    mr.hourly_rate,
    mr.is_active,
    COUNT(b.id) as active_bookings_count,
    CASE 
        WHEN mr.is_active = 1 THEN 'Available'
        ELSE 'Inactive'
    END as availability_status
FROM meeting_rooms mr
LEFT JOIN bookings b ON mr.id = b.meeting_room_id 
    AND b.status IN ('pending', 'confirmed')
    AND b.start_time >= datetime('now')
GROUP BY mr.id, mr.name, mr.capacity, mr.location, mr.hourly_rate, mr.is_active;

-- =====================================================
-- 8. TRIGGERS FOR AUTO-CALCULATION
-- =====================================================

-- Trigger to calculate total cost when booking is created/updated
CREATE TRIGGER IF NOT EXISTS calculate_booking_cost
AFTER INSERT ON bookings
FOR EACH ROW
BEGIN
    UPDATE bookings 
    SET total_cost = (
        SELECT hourly_rate * (julianday(NEW.end_time) - julianday(NEW.start_time)) * 24
        FROM meeting_rooms 
        WHERE id = NEW.meeting_room_id
    )
    WHERE id = NEW.id;
END;

-- =====================================================
-- 9. USEFUL QUERIES
-- =====================================================

-- Query to check room availability
-- SELECT 
--     CASE 
--         WHEN COUNT(*) = 0 THEN 'Available'
--         ELSE 'Not Available'
--     END as availability_status,
--     COUNT(*) as conflicting_bookings
-- FROM bookings 
-- WHERE meeting_room_id = ? 
--     AND status IN ('pending', 'confirmed')
--     AND (start_time < ? AND end_time > ?);

-- Query to get user booking history
-- SELECT 
--     b.id,
--     b.title,
--     b.start_time,
--     b.end_time,
--     b.status,
--     b.total_cost,
--     mr.name as room_name,
--     mr.location
-- FROM bookings b
-- JOIN meeting_rooms mr ON b.meeting_room_id = mr.id
-- WHERE b.user_id = ?
-- ORDER BY b.start_time DESC;

-- =====================================================
-- COMPLETION MESSAGE
-- =====================================================
SELECT 'SQLite database schema created successfully!' as message;
SELECT 'Tables created: cache, cache_locks, jobs, job_batches, failed_jobs, users, password_reset_tokens, sessions, admin_sessions, meeting_rooms, bookings' as tables_created;
SELECT 'Views created: active_bookings_view, room_availability_view' as views_created;
SELECT 'Trigger created: calculate_booking_cost' as trigger_created;
SELECT 'Sample data inserted for testing' as sample_data;
