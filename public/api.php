<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../src/models/User.php';
require_once __DIR__ . '/../src/models/Wishlist.php';
require_once __DIR__ . '/../src/models/WishlistItem.php';
require_once __DIR__ . '/../src/models/GiftIdea.php';
require_once __DIR__ . '/../src/models/Friend.php';
require_once __DIR__ . '/../src/models/Notification.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'register':
            $userModel = new User();
            
            // проверка обязательных полей
            if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
                echo json_encode(['success' => false, 'error' => 'Заполните все обязательные поля']);
                break;
            }
            
            // проверка длины пароля
            if (strlen($_POST['password']) < 6) {
                echo json_encode(['success' => false, 'error' => 'Пароль должен быть не менее 6 символов']);
                break;
            }
            
            try {
                $result = $userModel->register(
                    $_POST['username'],
                    $_POST['email'],
                    $_POST['password'],
                    $_POST['full_name'] ?? null
                );
                echo json_encode(['success' => $result, 'message' => 'Регистрация успешна']);
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'duplicate key') !== false) {
                    echo json_encode(['success' => false, 'error' => 'Пользователь с таким именем или email уже существует']);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Ошибка регистрации']);
                }
            }
            break;

        case 'login':
            $userModel = new User();
            
            if (empty($_POST['username']) || empty($_POST['password'])) {
                echo json_encode(['success' => false, 'error' => 'Заполните все поля']);
                break;
            }
            
            $user = $userModel->login($_POST['username'], $_POST['password']);
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                echo json_encode(['success' => true, 'user' => [
                    'id' => $user['id'],
                    'username' => $user['username']
                ]]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Неверное имя пользователя или пароль']);
            }
            break;

        case 'create_wishlist':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'error' => 'Требуется авторизация']);
                break;
            }
            
            $cover_image = null;
            
            // обработка загрузки изображения
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $filename = $_FILES['cover_image']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowed)) {
                    $new_filename = uniqid() . '.' . $ext;
                    $upload_path = __DIR__ . '/uploads/' . $new_filename;
                    
                    if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $upload_path)) {
                        $cover_image = 'uploads/' . $new_filename;
                    }
                }
            }
            
            $wishlistModel = new Wishlist();
            $id = $wishlistModel->create(
                $_SESSION['user_id'],
                $_POST['title'],
                $_POST['description'] ?? '',
                $_POST['event_type'] ?? 'other',
                $_POST['event_date'] ?? null,
                $_POST['privacy'] ?? 'public',
                $cover_image
            );
            echo json_encode(['success' => true, 'id' => $id]);
            break;

        case 'add_item':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'error' => 'Требуется авторизация']);
                break;
            }
            $itemModel = new WishlistItem();
            $result = $itemModel->create(
                $_POST['wishlist_id'],
                $_POST['title'],
                $_POST['description'] ?? '',
                $_POST['url'] ?? '',
                $_POST['price'] ?? null,
                $_POST['image_url'] ?? '',
                $_POST['priority'] ?? 'medium'
            );
            echo json_encode(['success' => $result]);
            break;

        case 'reserve_item':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'error' => 'Требуется авторизация']);
                break;
            }
            $itemModel = new WishlistItem();
            $result = $itemModel->reserve($_POST['item_id'], $_SESSION['user_id']);
            echo json_encode(['success' => $result]);
            break;

        case 'unreserve_item':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'error' => 'Требуется авторизация']);
                break;
            }
            $itemModel = new WishlistItem();
            $result = $itemModel->unreserve($_POST['item_id'], $_SESSION['user_id']);
            echo json_encode(['success' => $result]);
            break;

        case 'delete_item':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'error' => 'Требуется авторизация']);
                break;
            }
            $itemModel = new WishlistItem();
            $result = $itemModel->delete($_POST['item_id']);
            echo json_encode(['success' => $result]);
            break;

        case 'send_friend_request':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'error' => 'Требуется авторизация']);
                break;
            }
            $friendModel = new Friend();
            $result = $friendModel->sendRequest($_SESSION['user_id'], $_POST['user_id']);
            echo json_encode(['success' => $result]);
            break;

        case 'accept_friend_request':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'error' => 'Требуется авторизация']);
                break;
            }
            $friendModel = new Friend();
            $result = $friendModel->acceptRequest($_POST['request_id'], $_SESSION['user_id']);
            echo json_encode(['success' => $result]);
            break;

        case 'reject_friend_request':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'error' => 'Требуется авторизация']);
                break;
            }
            $friendModel = new Friend();
            $result = $friendModel->rejectRequest($_POST['request_id'], $_SESSION['user_id']);
            echo json_encode(['success' => $result]);
            break;

        case 'remove_friend':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'error' => 'Требуется авторизация']);
                break;
            }
            $friendModel = new Friend();
            $result = $friendModel->removeFriend($_SESSION['user_id'], $_POST['friend_id']);
            echo json_encode(['success' => $result]);
            break;

        case 'get_friends':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'error' => 'Требуется авторизация']);
                break;
            }
            $friendModel = new Friend();
            $friends = $friendModel->getFriends($_SESSION['user_id']);
            echo json_encode(['success' => true, 'friends' => $friends]);
            break;

        case 'share_wishlist':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'error' => 'Требуется авторизация']);
                break;
            }
            
            $wishlist_id = $_POST['wishlist_id'] ?? 0;
            $friend_ids = json_decode($_POST['friend_ids'] ?? '[]', true);
            $message = $_POST['message'] ?? '';
            
            if (empty($friend_ids)) {
                echo json_encode(['success' => false, 'error' => 'Выберите друзей']);
                break;
            }
            
           
            $wishlistModel = new Wishlist();
            $wishlist = $wishlistModel->getById($wishlist_id);
            
            if (!$wishlist || $wishlist['user_id'] != $_SESSION['user_id']) {
                echo json_encode(['success' => false, 'error' => 'Вишлист не найден']);
                break;
            }
            
          
            $notificationModel = new Notification();
            $userModel = new User();
            $sender = $userModel->getById($_SESSION['user_id']);
            
            $link = '/index.php?action=view_wishlist&id=' . $wishlist_id;
            $title = $sender['username'] . ' поделился вишлистом';
            $notificationMessage = $sender['username'] . ' поделился с вами вишлистом "' . $wishlist['title'] . '"';
            
            if (!empty($message)) {
                $notificationMessage .= "\n\nСообщение: " . $message;
            }
            
            $success = true;
            foreach ($friend_ids as $friend_id) {
                $result = $notificationModel->create(
                    $friend_id,
                    'wishlist_share',
                    $title,
                    $notificationMessage,
                    $link,
                    $_SESSION['user_id']
                );
                if (!$result) {
                    $success = false;
                }
            }
            
            echo json_encode(['success' => $success]);
            break;

        case 'get_notifications':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'error' => 'Требуется авторизация']);
                break;
            }
            $notificationModel = new Notification();
            $notifications = $notificationModel->getByUserId($_SESSION['user_id']);
            $unreadCount = $notificationModel->getUnreadCount($_SESSION['user_id']);
            echo json_encode([
                'success' => true, 
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]);
            break;

        case 'mark_notification_read':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'error' => 'Требуется авторизация']);
                break;
            }
            $notificationModel = new Notification();
            $result = $notificationModel->markAsRead($_POST['notification_id'], $_SESSION['user_id']);
            echo json_encode(['success' => $result]);
            break;

        case 'mark_all_notifications_read':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'error' => 'Требуется авторизация']);
                break;
            }
            $notificationModel = new Notification();
            $result = $notificationModel->markAllAsRead($_SESSION['user_id']);
            echo json_encode(['success' => $result]);
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Неизвестное действие']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
