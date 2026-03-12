<?php
require_once __DIR__ . '/../../config/db.php';

class Wishlist {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($user_id, $title, $description, $event_type, $event_date, $privacy = 'public', $cover_image = null) {
        $share_token = null;
        if ($privacy === 'link') {
            $share_token = bin2hex(random_bytes(32));
        }
        
        $sql = "INSERT INTO wishlists (user_id, title, description, event_type, event_date, privacy, cover_image, share_token) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id, $title, $description, $event_type, $event_date, $privacy, $cover_image, $share_token]);
        return $this->db->lastInsertId();
    }

    public function getById($id) {
        $sql = "SELECT w.*, u.username, u.full_name 
                FROM wishlists w 
                JOIN users u ON w.user_id = u.id 
                WHERE w.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByUserId($user_id) {
        $sql = "SELECT * FROM wishlists WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function getAccessibleByUserId($user_id, $viewer_id = null) {
        // Если смотрит владелец - показываем все
        if ($viewer_id && $user_id == $viewer_id) {
            return $this->getByUserId($user_id);
        }
        
        // Проверяем, являются ли пользователи друзьями
        $areFriends = false;
        if ($viewer_id) {
            require_once __DIR__ . '/Friend.php';
            $friendModel = new Friend();
            $areFriends = $friendModel->areFriends($user_id, $viewer_id);
        }
        
        // Формируем запрос в зависимости от статуса дружбы
        if ($areFriends) {
            $sql = "SELECT * FROM wishlists WHERE user_id = ? AND privacy IN ('public', 'friends') ORDER BY created_at DESC";
        } else {
            $sql = "SELECT * FROM wishlists WHERE user_id = ? AND privacy = 'public' ORDER BY created_at DESC";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function getFriendsWishlists($user_id, $limit = 20) {
        require_once __DIR__ . '/Friend.php';
        $friendModel = new Friend();
        $friends = $friendModel->getFriends($user_id);
        
        if (empty($friends)) {
            return [];
        }
        
        $friendIds = array_column($friends, 'id');
        $placeholders = str_repeat('?,', count($friendIds) - 1) . '?';
        
        $sql = "SELECT w.*, u.username, u.full_name, u.avatar,
                (SELECT COUNT(*) FROM wishlist_items WHERE wishlist_id = w.id) as items_count
                FROM wishlists w 
                JOIN users u ON w.user_id = u.id 
                WHERE w.user_id IN ($placeholders) AND w.privacy IN ('public', 'friends')
                ORDER BY w.created_at DESC 
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $params = array_merge($friendIds, [$limit]);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getPublic($limit = 20, $offset = 0) {
        $sql = "SELECT w.*, u.username, u.full_name, u.avatar,
                (SELECT COUNT(*) FROM wishlist_items WHERE wishlist_id = w.id) as items_count
                FROM wishlists w 
                JOIN users u ON w.user_id = u.id 
                WHERE w.privacy = 'public' 
                ORDER BY w.created_at DESC 
                LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    public function getByShareToken($token) {
        $sql = "SELECT w.*, u.username, u.full_name 
                FROM wishlists w 
                JOIN users u ON w.user_id = u.id 
                WHERE w.share_token = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    public function canAccess($wishlist_id, $user_id) {
        $wishlist = $this->getById($wishlist_id);
        if (!$wishlist) return false;
        
        // Владелец всегда имеет доступ
        if ($wishlist['user_id'] == $user_id) return true;
        
        // Публичный вишлист
        if ($wishlist['privacy'] == 'public') return true;
        
        // Доступ по ссылке (проверяется отдельно через токен)
        if ($wishlist['privacy'] == 'link') return false;
        
        // Доступ только для друзей
        if ($wishlist['privacy'] == 'friends' && $user_id) {
            require_once __DIR__ . '/Friend.php';
            $friendModel = new Friend();
            return $friendModel->areFriends($wishlist['user_id'], $user_id);
        }
        
        return false;
    }

    public function update($id, $title, $description, $event_type, $event_date, $privacy, $cover_image = null) {
        // Генерируем токен если privacy = 'link' и токена еще нет
        $wishlist = $this->getById($id);
        $share_token = $wishlist['share_token'];
        
        if ($privacy === 'link' && !$share_token) {
            $share_token = bin2hex(random_bytes(32));
        }
        
        if ($cover_image) {
            $sql = "UPDATE wishlists SET title = ?, description = ?, event_type = ?, event_date = ?, privacy = ?, cover_image = ?, share_token = ? 
                    WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$title, $description, $event_type, $event_date, $privacy, $cover_image, $share_token, $id]);
        } else {
            $sql = "UPDATE wishlists SET title = ?, description = ?, event_type = ?, event_date = ?, privacy = ?, share_token = ? 
                    WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$title, $description, $event_type, $event_date, $privacy, $share_token, $id]);
        }
    }

    public function delete($id) {
        $sql = "DELETE FROM wishlists WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
