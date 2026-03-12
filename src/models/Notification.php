<?php
require_once __DIR__ . '/../../config/db.php';

class Notification {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($user_id, $type, $title, $message, $link = null, $from_user_id = null) {
        $sql = "INSERT INTO notifications (user_id, type, title, message, link, from_user_id) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$user_id, $type, $title, $message, $link, $from_user_id]);
    }

    public function getByUserId($user_id, $limit = 20) {
        $sql = "SELECT n.*, u.username as from_username 
                FROM notifications n 
                LEFT JOIN users u ON n.from_user_id = u.id 
                WHERE n.user_id = ? 
                ORDER BY n.created_at DESC 
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id, $limit]);
        return $stmt->fetchAll();
    }

    public function getUnreadCount($user_id) {
        $sql = "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = FALSE";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }

    public function markAsRead($notification_id, $user_id) {
        $sql = "UPDATE notifications SET is_read = TRUE WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$notification_id, $user_id]);
    }

    public function markAllAsRead($user_id) {
        $sql = "UPDATE notifications SET is_read = TRUE WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$user_id]);
    }

    public function delete($notification_id, $user_id) {
        $sql = "DELETE FROM notifications WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$notification_id, $user_id]);
    }
}
