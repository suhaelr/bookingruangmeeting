-- =====================================================
-- MEETING ROOM BOOKING SYSTEM - POSTGRESQL SCHEMA
-- =====================================================
-- Script untuk PostgreSQL database
-- Sesuai dengan struktur Laravel migration

-- Drop existing tables if they exist
DROP TABLE IF EXISTS bookings CASCADE;
DROP TABLE IF EXISTS meeting_rooms CASCADE;
DROP TABLE IF EXISTS admin_sessions CASCADE;
DROP TABLE IF EXISTS sessions CASCADE;
DROP TABLE IF EXISTS password_reset_tokens CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS failed_jobs CASCADE;
DROP TABLE IF EXISTS job_batches CASCADE;
DROP TABLE IF EXISTS jobs CASCADE;
DROP TABLE IF EXISTS cache_locks CASCADE;
DROP TABLE IF EXISTS cache CASCADE;

-- =====================================================
-- 1. CACHE TABLES
-- =====================================================

CREATE TABLE cache (
    key VARCHAR(255) PRIMARY KEY,
    value TEXT NOT NULL,
    expiration INTEGER NOT NULL
);

CREATE TABLE cache_locks (
    key VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INTEGER NOT NULL
);

-- =====================================================
-- 2. JOB QUEUE TABLES
-- =====================================================

CREATE TABLE jobs (
    id BIGSERIAL PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload TEXT NOT NULL,
    attempts SMALLINT NOT NULL,
    reserved_at INTEGER,
    available_at INTEGER NOT NULL,
    created_at INTEGER NOT NULL
);

CREATE INDEX jobs_queue_index ON jobs(queue);

CREATE TABLE job_batches (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INTEGER NOT NULL,
    pending_jobs INTEGER NOT NULL,
    failed_jobs INTEGER NOT NULL,
    failed_job_ids TEXT NOT NULL,
    options TEXT,
    cancelled_at INTEGER,
    created_at INTEGER NOT NULL,
    finished_at INTEGER
);

CREATE TABLE failed_jobs (
    id BIGSERIAL PRIMARY KEY,
    uuid VARCHAR(255) UNIQUE NOT NULL,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload TEXT NOT NULL,
    exception TEXT NOT NULL,
    failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- 3. USER MANAGEMENT TABLES
-- =====================================================

CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP,
    email_verification_token VARCHAR(255),
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(255),
    department VARCHAR(255),
    role VARCHAR(20) DEFAULT 'user' CHECK (role IN ('admin', 'user')),
    avatar VARCHAR(255),
    last_login_at TIMESTAMP,
    remember_token VARCHAR(100),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE INDEX users_email_index ON users(email);
CREATE INDEX users_username_index ON users(username);
CREATE INDEX users_role_index ON users(role);

CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP
);

CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT REFERENCES users(id) ON DELETE CASCADE,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload TEXT NOT NULL,
    last_activity INTEGER NOT NULL
);

CREATE INDEX sessions_user_id_index ON sessions(user_id);
CREATE INDEX sessions_last_activity_index ON sessions(last_activity);

CREATE TABLE admin_sessions (
    id BIGSERIAL PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    session_id VARCHAR(255) UNIQUE NOT NULL,
    last_activity TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- =====================================================
-- 4. MEETING ROOM TABLES
-- =====================================================

CREATE TABLE meeting_rooms (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    capacity INTEGER NOT NULL,
    amenities JSONB, -- PostgreSQL JSONB for better performance
    location VARCHAR(255) NOT NULL,
    hourly_rate DECIMAL(8,2) DEFAULT 0.00,
    images JSONB,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE INDEX meeting_rooms_is_active_index ON meeting_rooms(is_active);
CREATE INDEX meeting_rooms_capacity_index ON meeting_rooms(capacity);

-- =====================================================
-- 5. BOOKING TABLES
-- =====================================================

CREATE TABLE bookings (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    meeting_room_id BIGINT NOT NULL REFERENCES meeting_rooms(id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP NOT NULL,
    status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'confirmed', 'cancelled', 'completed')),
    attendees_count INTEGER DEFAULT 1,
    attendees JSONB,
    attachments JSONB,
    special_requirements TEXT,
    total_cost DECIMAL(8,2) DEFAULT 0.00,
    cancelled_at TIMESTAMP,
    cancellation_reason TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Indexes for optimization
CREATE INDEX bookings_meeting_room_time_index ON bookings(meeting_room_id, start_time, end_time);
CREATE INDEX bookings_user_time_index ON bookings(user_id, start_time);
CREATE INDEX bookings_status_index ON bookings(status);
CREATE INDEX bookings_start_time_index ON bookings(start_time);
CREATE INDEX bookings_end_time_index ON bookings(end_time);

-- =====================================================
-- 6. SAMPLE DATA
-- =====================================================

-- Insert sample admin user
INSERT INTO users (username, name, full_name, email, email_verified_at, password, phone, department, role, created_at, updated_at) VALUES
('admin', 'Admin User', 'Administrator', 'admin@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+6281234567890', 'IT', 'admin', NOW(), NOW());

-- Insert sample regular user
INSERT INTO users (username, name, full_name, email, email_verified_at, password, phone, department, role, created_at, updated_at) VALUES
('john.doe', 'John Doe', 'John Doe', 'john.doe@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+6281234567891', 'Marketing', 'user', NOW(), NOW());

-- Insert sample meeting rooms
INSERT INTO meeting_rooms (name, description, capacity, amenities, location, hourly_rate, images, is_active, created_at, updated_at) VALUES
('Conference Room A', 'Large conference room with modern facilities', 20, '["projector", "whiteboard", "wifi", "ac", "video_conference"]', 'Floor 1, Building A', 150000.00, '["room_a_1.jpg", "room_a_2.jpg"]', TRUE, NOW(), NOW()),
('Meeting Room B', 'Medium-sized meeting room for small groups', 8, '["whiteboard", "wifi", "ac"]', 'Floor 2, Building A', 75000.00, '["room_b_1.jpg"]', TRUE, NOW(), NOW()),
('Executive Boardroom', 'Premium boardroom for executive meetings', 12, '["projector", "whiteboard", "wifi", "ac", "video_conference", "catering"]', 'Floor 3, Building A', 250000.00, '["boardroom_1.jpg", "boardroom_2.jpg"]', TRUE, NOW(), NOW()),
('Training Room', 'Large space for training and workshops', 50, '["projector", "whiteboard", "wifi", "ac", "sound_system"]', 'Floor 1, Building B', 200000.00, '["training_room_1.jpg"]', TRUE, NOW(), NOW()),
('Small Meeting Room', 'Intimate space for 1-on-1 meetings', 4, '["wifi", "ac"]', 'Floor 2, Building B', 50000.00, '["small_room_1.jpg"]', TRUE, NOW(), NOW());

-- Insert sample bookings
INSERT INTO bookings (user_id, meeting_room_id, title, description, start_time, end_time, status, attendees_count, attendees, total_cost, created_at, updated_at) VALUES
(2, 1, 'Weekly Team Meeting', 'Regular weekly team sync meeting', NOW() + INTERVAL '1 day', NOW() + INTERVAL '1 day' + INTERVAL '2 hours', 'confirmed', 5, '["team1@example.com", "team2@example.com", "team3@example.com"]', 300000.00, NOW(), NOW()),
(2, 2, 'Client Presentation', 'Presentation for new client proposal', NOW() + INTERVAL '3 days', NOW() + INTERVAL '3 days' + INTERVAL '1 hour', 'pending', 3, '["client@example.com", "manager@example.com"]', 75000.00, NOW(), NOW()),
(2, 3, 'Board Meeting', 'Monthly board of directors meeting', NOW() + INTERVAL '7 days', NOW() + INTERVAL '7 days' + INTERVAL '3 hours', 'confirmed', 8, '["director1@example.com", "director2@example.com", "ceo@example.com"]', 750000.00, NOW(), NOW());

-- =====================================================
-- 7. VIEWS FOR COMMON QUERIES
-- =====================================================

-- View for active bookings with user and room details
CREATE VIEW active_bookings_view AS
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
CREATE VIEW room_availability_view AS
SELECT 
    mr.id,
    mr.name,
    mr.capacity,
    mr.location,
    mr.hourly_rate,
    mr.is_active,
    COUNT(b.id) as active_bookings_count,
    CASE 
        WHEN mr.is_active = TRUE THEN 'Available'
        ELSE 'Inactive'
    END as availability_status
FROM meeting_rooms mr
LEFT JOIN bookings b ON mr.id = b.meeting_room_id 
    AND b.status IN ('pending', 'confirmed')
    AND b.start_time >= NOW()
GROUP BY mr.id, mr.name, mr.capacity, mr.location, mr.hourly_rate, mr.is_active;

-- =====================================================
-- 8. FUNCTIONS FOR COMMON OPERATIONS
-- =====================================================

-- Function to check room availability
CREATE OR REPLACE FUNCTION check_room_availability(
    room_id INTEGER,
    start_datetime TIMESTAMP,
    end_datetime TIMESTAMP
)
RETURNS TABLE(availability_status TEXT, conflicting_bookings BIGINT) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        CASE 
            WHEN COUNT(*) = 0 THEN 'Available'
            ELSE 'Not Available'
        END as availability_status,
        COUNT(*) as conflicting_bookings
    FROM bookings 
    WHERE meeting_room_id = room_id 
        AND status IN ('pending', 'confirmed')
        AND (start_time < end_datetime AND end_time > start_datetime);
END;
$$ LANGUAGE plpgsql;

-- Function to get user booking history
CREATE OR REPLACE FUNCTION get_user_booking_history(user_id INTEGER)
RETURNS TABLE(
    booking_id BIGINT,
    title VARCHAR(255),
    start_time TIMESTAMP,
    end_time TIMESTAMP,
    status VARCHAR(20),
    total_cost DECIMAL(8,2),
    room_name VARCHAR(255),
    location VARCHAR(255)
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        b.id,
        b.title,
        b.start_time,
        b.end_time,
        b.status,
        b.total_cost,
        mr.name,
        mr.location
    FROM bookings b
    JOIN meeting_rooms mr ON b.meeting_room_id = mr.id
    WHERE b.user_id = get_user_booking_history.user_id
    ORDER BY b.start_time DESC;
END;
$$ LANGUAGE plpgsql;

-- =====================================================
-- 9. TRIGGERS
-- =====================================================

-- Function to calculate booking cost
CREATE OR REPLACE FUNCTION calculate_booking_cost()
RETURNS TRIGGER AS $$
DECLARE
    room_rate DECIMAL(8,2);
    duration_hours DECIMAL(8,2);
BEGIN
    -- Get room hourly rate
    SELECT hourly_rate INTO room_rate 
    FROM meeting_rooms 
    WHERE id = NEW.meeting_room_id;
    
    -- Calculate duration in hours
    duration_hours := EXTRACT(EPOCH FROM (NEW.end_time - NEW.start_time)) / 3600.0;
    
    -- Calculate total cost
    NEW.total_cost := room_rate * duration_hours;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Create trigger for cost calculation
CREATE TRIGGER calculate_booking_cost_trigger
    BEFORE INSERT ON bookings
    FOR EACH ROW
    EXECUTE FUNCTION calculate_booking_cost();

-- =====================================================
-- COMPLETION MESSAGE
-- =====================================================
SELECT 'PostgreSQL database schema created successfully!' as message;
SELECT 'Tables created: cache, cache_locks, jobs, job_batches, failed_jobs, users, password_reset_tokens, sessions, admin_sessions, meeting_rooms, bookings' as tables_created;
SELECT 'Views created: active_bookings_view, room_availability_view' as views_created;
SELECT 'Functions created: check_room_availability, get_user_booking_history' as functions_created;
SELECT 'Sample data inserted for testing' as sample_data;
