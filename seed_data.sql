-- тестовые данные для wishlist_service

--  тестовые пользователи (пароль для всех: password123)
INSERT INTO users (username, email, password_hash, full_name) VALUES
('ivan_petrov', 'ivan@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Иван Петров'),
('maria_ivanova', 'maria@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Мария Иванова'),
('alex_smirnov', 'alex@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Александр Смирнов');

-- вишлисты
INSERT INTO wishlists (user_id, title, description, event_type, event_date, is_public) VALUES
(1, 'Мой день рождения 2024', 'Буду рад любому подарку из списка!', 'birthday', '2024-06-15', TRUE),
(2, 'Новый год 2025', 'Подарки к Новому году для всей семьи', 'new_year', '2024-12-31', TRUE),
(3, 'Свадьба', 'Список подарков к нашей свадьбе', 'wedding', '2024-08-20', TRUE);

--  подарки в вишлисты
INSERT INTO wishlist_items (wishlist_id, title, description, url, price, priority) VALUES
(1, 'Беспроводные наушники', 'Sony WH-1000XM5 с шумоподавлением', 'https://example.com/headphones', 25000.00, 'high'),
(1, 'Книга "Мастер и Маргарита"', 'Коллекционное издание', 'https://example.com/book', 1500.00, 'medium'),
(1, 'Кофемашина', 'Автоматическая кофемашина Delonghi', 'https://example.com/coffee', 35000.00, 'high'),
(1, 'Настольная игра', 'Каркассон или Колонизаторы', NULL, 3000.00, 'low'),

(2, 'Умная колонка', 'Яндекс Станция Макс', 'https://example.com/speaker', 15000.00, 'medium'),
(2, 'Набор для глинтвейна', 'Специи и бокалы', NULL, 2000.00, 'low'),
(2, 'Электрокамин', 'Декоративный электрокамин', 'https://example.com/fireplace', 12000.00, 'high'),

(3, 'Набор посуды', 'Сервиз на 6 персон', 'https://example.com/dishes', 8000.00, 'high'),
(3, 'Постельное белье', 'Премиум сатин', 'https://example.com/bedding', 5000.00, 'medium'),
(3, 'Мультиварка', 'Redmond с большой чашей', 'https://example.com/multicooker', 7000.00, 'medium');

-- идеи подарков с правильной кодировкой 
INSERT INTO gift_ideas (title, description, category, price_range, url) VALUES
('Умные часы', 'Фитнес-трекер с множеством функций для здоровья', 'Электроника', '5000-20000 ₽', 'https://example.com/smartwatch'),
('Сертификат в SPA', 'Расслабляющие процедуры и массаж', 'Услуги', '3000-10000 ₽', 'https://example.com/spa'),
('Настольная лампа', 'Современная LED лампа с регулировкой яркости', 'Для дома', '2000-5000 ₽', 'https://example.com/lamp'),
('Подписка на музыку', 'Годовая подписка на Яндекс.Музыку или Spotify', 'Подписки', '1000-3000 ₽', 'https://example.com/music'),
('Растение в горшке', 'Монстера или фикус для украшения дома', 'Растения', '1500-4000 ₽', 'https://example.com/plant'),
('Набор для рисования', 'Профессиональные краски и кисти', 'Хобби', '3000-8000 ₽', 'https://example.com/art'),
('Термос', 'Качественный термос для горячих напитков', 'Аксессуары', '1500-3500 ₽', 'https://example.com/thermos'),
('Беспроводная зарядка', 'Быстрая зарядка для смартфонов', 'Электроника', '1000-3000 ₽', 'https://example.com/charger'),
('Книга рецептов', 'Кулинарная книга с авторскими рецептами', 'Книги', '800-2000 ₽', 'https://example.com/cookbook'),
('Игровая мышь', 'Эргономичная мышь для геймеров', 'Электроника', '2000-6000 ₽', 'https://example.com/mouse');


UPDATE wishlist_items SET is_reserved = TRUE, reserved_by = 2 WHERE id = 1;
UPDATE wishlist_items SET is_reserved = TRUE, reserved_by = 3 WHERE id = 5;
