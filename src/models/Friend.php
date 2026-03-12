<?php
require_once __DIR__ . '/../../config/db.php';

class Friend {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Отправить запрос в друзья
    public function sendRequest($sender_id, $receiver_id) {
        // Проверяем, не отправлен ли уже запрос
        $sql = "SELECT * FROM friend_requests WHERE 
                (sender_id = ? AND receiver_id = ?) OR 
                (sender_id = ? AND receiver_id = ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sender_id, $receiver_id, $receiver_id, $sender_id]);
        
        if ($stmt->fetch()) {
            return false; // Запрос уже существует
        }
        
        $sql = "INSERT INTO friend_requests (sender_id, receiver_id, status) VALUES (?, ?, 'pending')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$sender_id, $receiver_id]);
    }

    // Принять запрос в друзья
    public function acceptRequest($request_id, $user_id) {
        $sql = "UPDATE friend_requests SET status = 'accepted', updated_at = CURRENT_TIMESTAMP 
                WHERE id = ? AND receiver_id = ? AND status = 'pending'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$request_id, $user_id]);
    }

    // Отклонить запрос в друзья
    public function rejectRequest($request_id, $user_id) {
        $sql = "UPDATE friend_requests SET status = 'rejected', updated_at = CURRENT_TIMESTAMP 
                WHERE id = ? AND receiver_id = ? AND status = 'pending'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$request_id, $user_id]);
    }

    // Удалить из друзей
    public function removeFriend($user_id, $friend_id) {
        $sql = "DELETE FROM friend_requests WHERE 
                ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)) 
                AND status = 'accepted'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$user_id, $friend_id, $friend_id, $user_id]);
    }

    // Получить список друзей
    public function getFriends($user_id) {
        $sql = "SELECT u.id, u.username, u.full_name, u.avatar, fr.created_at as friends_since
                FROM friend_requests fr
                JOIN users u ON (
                    CASE 
                        WHEN fr.sender_id = ? THEN u.id = fr.receiver_id
                        ELSE u.id = fr.sender_id
                    END
                )
                WHERE (fr.sender_id = ? OR fr.receiver_id = ?) 
                AND fr.status = 'accepted'
                ORDER BY u.username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id, $user_id, $user_id]);
        return $stmt->fetchAll();
    }

    // Получить входящие запросы
    public function getIncomingRequests($user_id) {
        $sql = "SELECT fr.id, fr.sender_id, fr.created_at, u.username, u.full_name, u.avatar
                FROM friend_requests fr
                JOIN users u ON fr.sender_id = u.id
                WHERE fr.receiver_id = ? AND fr.status = 'pending'
                ORDER BY fr.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    // Получить исходящие запросы
    public function getOutgoingRequests($user_id) {
        $sql = "SELECT fr.id, fr.receiver_id, fr.created_at, u.username, u.full_name, u.avatar
                FROM friend_requests fr
                JOIN users u ON fr.receiver_id = u.id
                WHERE fr.sender_id = ? AND fr.status = 'pending'
                ORDER BY fr.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    // Проверить, являются ли пользователи друзьями
    public function areFriends($user_id, $friend_id) {
        $sql = "SELECT COUNT(*) FROM friend_requests 
                WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?))
                AND status = 'accepted'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id, $friend_id, $friend_id, $user_id]);
        return $stmt->fetchColumn() > 0;
    }

    // Получить статус дружбы
    public function getFriendshipStatus($user_id, $other_user_id) {
        $sql = "SELECT status, sender_id, receiver_id, id FROM friend_requests 
                WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id, $other_user_id, $other_user_id, $user_id]);
        return $stmt->fetch();
    }
}
