-- Migration: 002_add_cover_image
-- Description: Add cover image support for wishlists
-- Created: 2024-01-02

-- Add cover_image column to wishlists
ALTER TABLE wishlists ADD COLUMN IF NOT EXISTS cover_image VARCHAR(255);

-- Insert migration record
INSERT INTO migrations (version, description) 
VALUES ('002', 'Add cover image support for wishlists')
ON CONFLICT (version) DO NOTHING;
