<?php
require_once __DIR__ . '/../../config/db.php';

class GiftIdea {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($category = null, $limit = 20) {
        if ($category) {
            $sql = "SELECT * FROM gift_ideas WHERE category = ? ORDER BY created_at DESC LIMIT ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$category, $limit]);
        } else {
            $sql = "SELECT * FROM gift_ideas ORDER BY created_at DESC LIMIT ?";
            $stmt = $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit]);
        }
        return $stmt->fetchAll();
    }

    public function create($title, $description, $category, $price_range, $image_url, $url) {
        $sql = "INSERT INTO gift_ideas (title, description, category, price_range, image_url, url) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$title, $description, $category, $price_range, $image_url, $url]);
    }

    public function getCategories() {
        $sql = "SELECT DISTINCT category FROM gift_ideas WHERE category IS NOT NULL ORDER BY category";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
