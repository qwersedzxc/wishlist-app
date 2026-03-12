-- Migration: 003_add_friends_system
-- Description: Add friends system and enhanced privacy settings
-- Created: 2024-01-03

-- Create friend status enum
CREATE TYPE friend_status_enum AS ENUM ('pending', 'accepted', 'rejected');

-- Create friend_requests table
CREATE TABLE IF NOT EXISTS friend_requests (
    id SERIAL PRIMARY KEY,
    sender_id INTEGER NOT NULL,
    receiver_id INTEGER NOT NULL,
    status friend_status_enum DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE(sender_id, receiver_id)
);

-- Create indexes for friend_requests
CREATE INDEX IF NOT EXISTS idx_friend_requests_sender ON friend_requests(sender_id);
CREATE INDEX IF NOT EXISTS idx_friend_requests_receiver ON friend_requests(receiver_id);
CREATE INDEX IF NOT EXISTS idx_friend_requests_status ON friend_requests(status);

-- Create privacy enum
CREATE TYPE privacy_enum AS ENUM ('public', 'friends', 'link');

-- Add privacy columns to wishlists
ALTER TABLE wishlists ADD COLUMN IF NOT EXISTS privacy privacy_enum DEFAULT 'public';
ALTER TABLE wishlists ADD COLUMN IF NOT EXISTS share_token VARCHAR(64) UNIQUE;

-- Migrate existing data
UPDATE wishlists SET privacy = CASE 
    WHEN is_public = TRUE THEN 'public'::privacy_enum 
    ELSE 'friends'::privacy_enum 
END
WHERE privacy IS NULL;

-- Insert migration record
INSERT INTO migrations (version, description) 
VALUES ('003', 'Add friends system and enhanced privacy settings')
ON CONFLICT (version) DO NOTHING;
