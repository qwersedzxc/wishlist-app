<?php
session_start();

require_once __DIR__ . '/../src/models/User.php';
require_once __DIR__ . '/../src/models/Wishlist.php';
require_once __DIR__ . '/../src/models/WishlistItem.php';
require_once __DIR__ . '/../src/models/GiftIdea.php';

$action = $_GET['action'] ?? 'home';
$user = null;

if (isset($_SESSION['user_id'])) {
    $userModel = new User();
    $user = $userModel->getById($_SESSION['user_id']);
}

// Роутинг
switch ($action) {
    case 'home':
        include __DIR__ . '/../src/views/home.php';
        break;
    case 'login':
        include __DIR__ . '/../src/views/login.php';
        break;
    case 'register':
        include __DIR__ . '/../src/views/register.php';
        break;
    case 'logout':
        session_destroy();
        header('Location: index.php');
        exit;
    case 'my_wishlists':
        if (!$user) {
            header('Location: index.php?action=login');
            exit;
        }
        include __DIR__ . '/../src/views/my_wishlists.php';
        break;
    case 'create_wishlist':
        if (!$user) {
            header('Location: index.php?action=login');
            exit;
        }
        include __DIR__ . '/../src/views/create_wishlist.php';
        break;
    case 'friends':
        if (!$user) {
            header('Location: index.php?action=login');
            exit;
        }
        include __DIR__ . '/../src/views/friends.php';
        break;
    case 'search_users':
        if (!$user) {
            header('Location: index.php?action=login');
            exit;
        }
        include __DIR__ . '/../src/views/search_users.php';
        break;
    case 'user_profile':
        include __DIR__ . '/../src/views/user_profile.php';
        break;
    case 'friends_wishlists':
        if (!$user) {
            header('Location: index.php?action=login');
            exit;
        }
        include __DIR__ . '/../src/views/friends_wishlists.php';
        break;
    case 'view_wishlist':
        include __DIR__ . '/../src/views/view_wishlist.php';
        break;
    case 'share_wishlist':
        if (!$user) {
            header('Location: index.php?action=login');
            exit;
        }
        include __DIR__ . '/../src/views/share_wishlist.php';
        break;
    case 'gift_ideas':
        include __DIR__ . '/../src/views/gift_ideas.php';
        break;
    case 'explore':
        include __DIR__ . '/../src/views/explore.php';
        break;
    default:
        include __DIR__ . '/../src/views/home.php';
}
