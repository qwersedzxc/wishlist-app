<?php
require_once __DIR__ . '/../../config/db.php';

class WishlistItem {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($wishlist_id, $title, $description, $url, $price, $image_url, $priority) {
        $sql = "INSERT INTO wishlist_items (wishlist_id, title, description, url, price, image_url, priority) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$wishlist_id, $title, $description, $url, $price, $image_url, $priority]);
    }

    public function getByWishlistId($wishlist_id) {
        $sql = "SELECT wi.*, u.username as reserved_by_username 
                FROM wishlist_items wi 
                LEFT JOIN users u ON wi.reserved_by = u.id 
                WHERE wi.wishlist_id = ? 
                ORDER BY 
                    CASE wi.priority 
                        WHEN 'high' THEN 1 
                        WHEN 'medium' THEN 2 
                        WHEN 'low' THEN 3 
                    END,
                    wi.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$wishlist_id]);
        return $stmt->fetchAll();
    }

    public function reserve($item_id, $user_id) {
        $sql = "UPDATE wishlist_items SET is_reserved = TRUE, reserved_by = ? WHERE id = ? AND is_reserved = FALSE";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$user_id, $item_id]);
    }

    public function unreserve($item_id, $user_id) {
        $sql = "UPDATE wishlist_items SET is_reserved = FALSE, reserved_by = NULL WHERE id = ? AND reserved_by = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$item_id, $user_id]);
    }

    public function delete($id) {
        $sql = "DELETE FROM wishlist_items WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
