
DELETE FROM gift_ideas;


INSERT INTO gift_ideas (title, description, category, price_range, image_url, url) VALUES
-- Аксессуары
('Кожаный кошелек', 'Стильный кошелек из натуральной кожи', 'Аксессуары', '1500-3000 ₽', 'https://via.placeholder.com/300x200?text=Кошелек', 'https://example.com/wallet'),
('Солнцезащитные очки', 'Модные очки с UV защитой', 'Аксессуары', '2000-5000 ₽', 'https://via.placeholder.com/300x200?text=Очки', 'https://example.com/sunglasses'),
('Шарф', 'Теплый шарф из шерсти', 'Аксессуары', '1000-3000 ₽', 'https://via.placeholder.com/300x200?text=Шарф', 'https://example.com/scarf'),
('Ремень', 'Классический кожаный ремень', 'Аксессуары', '1500-3500 ₽', 'https://via.placeholder.com/300x200?text=Ремень', 'https://example.com/belt'),
('Зонт', 'Автоматический складной зонт', 'Аксессуары', '800-2000 ₽', 'https://via.placeholder.com/300x200?text=Зонт', 'https://example.com/umbrella'),
('Рюкзак', 'Городской рюкзак для ноутбука', 'Аксессуары', '3000-7000 ₽', 'https://via.placeholder.com/300x200?text=Рюкзак', 'https://example.com/backpack'),

-- Для дома
('Набор посуды', 'Керамический набор тарелок и чашек', 'Для дома', '2000-5000 ₽', 'https://via.placeholder.com/300x200?text=Посуда', 'https://example.com/dishes'),
('Плед', 'Мягкий плед из флиса', 'Для дома', '1500-3000 ₽', 'https://via.placeholder.com/300x200?text=Плед', 'https://example.com/blanket'),
('Ароматические свечи', 'Набор свечей с разными ароматами', 'Для дома', '1000-2500 ₽', 'https://via.placeholder.com/300x200?text=Свечи', 'https://example.com/candles'),
('Картина', 'Современная картина для интерьера', 'Для дома', '2000-6000 ₽', 'https://via.placeholder.com/300x200?text=Картина', 'https://example.com/painting'),
('Подушки декоративные', 'Набор декоративных подушек', 'Для дома', '1500-4000 ₽', 'https://via.placeholder.com/300x200?text=Подушки', 'https://example.com/pillows'),
('Органайзер', 'Органайзер для хранения мелочей', 'Для дома', '800-2000 ₽', 'https://via.placeholder.com/300x200?text=Органайзер', 'https://example.com/organizer'),
('Ваза', 'Стеклянная ваза для цветов', 'Для дома', '1000-3000 ₽', 'https://via.placeholder.com/300x200?text=Ваза', 'https://example.com/vase'),

-- Книги
('Художественная литература', 'Бестселлер современной прозы', 'Книги', '500-1500 ₽', 'https://via.placeholder.com/300x200?text=Роман', 'https://example.com/novel'),
('Книга по саморазвитию', 'Мотивирующая книга о личностном росте', 'Книги', '600-1500 ₽', 'https://via.placeholder.com/300x200?text=Саморазвитие', 'https://example.com/selfhelp'),
('Детектив', 'Захватывающий детективный роман', 'Книги', '500-1200 ₽', 'https://via.placeholder.com/300x200?text=Детектив', 'https://example.com/detective'),
('Энциклопедия', 'Иллюстрированная энциклопедия', 'Книги', '1000-3000 ₽', 'https://via.placeholder.com/300x200?text=Энциклопедия', 'https://example.com/encyclopedia'),
('Книга по психологии', 'Практическое руководство по психологии', 'Книги', '700-1800 ₽', 'https://via.placeholder.com/300x200?text=Психология', 'https://example.com/psychology'),
('Комикс', 'Графический роман', 'Книги', '800-2000 ₽', 'https://via.placeholder.com/300x200?text=Комикс', 'https://example.com/comic'),

-- Подписки
('Подписка на кино', 'Годовая подписка на онлайн-кинотеатр', 'Подписки', '1000-3000 ₽', 'https://via.placeholder.com/300x200?text=Кино', 'https://example.com/cinema'),
('Подписка на фитнес', 'Доступ к онлайн тренировкам', 'Подписки', '500-2000 ₽', 'https://via.placeholder.com/300x200?text=Фитнес', 'https://example.com/fitness'),
('Подписка на журналы', 'Электронная подписка на журналы', 'Подписки', '300-1000 ₽', 'https://via.placeholder.com/300x200?text=Журналы', 'https://example.com/magazines'),
('Подписка на аудиокниги', 'Безлимитный доступ к аудиокнигам', 'Подписки', '500-1500 ₽', 'https://via.placeholder.com/300x200?text=Аудиокниги', 'https://example.com/audiobooks'),
('Подписка на обучение', 'Доступ к онлайн-курсам', 'Подписки', '1000-5000 ₽', 'https://via.placeholder.com/300x200?text=Обучение', 'https://example.com/courses'),
('Подписка на игры', 'Игровая подписка с библиотекой игр', 'Подписки', '500-2000 ₽', 'https://via.placeholder.com/300x200?text=Игры', 'https://example.com/games'),

-- Растения
('Суккуленты', 'Набор мини-суккулентов', 'Растения', '1000-2500 ₽', 'https://via.placeholder.com/300x200?text=Суккуленты', 'https://example.com/succulents'),
('Орхидея', 'Цветущая орхидея в горшке', 'Растения', '1500-4000 ₽', 'https://via.placeholder.com/300x200?text=Орхидея', 'https://example.com/orchid'),
('Бонсай', 'Миниатюрное дерево бонсай', 'Растения', '2000-5000 ₽', 'https://via.placeholder.com/300x200?text=Бонсай', 'https://example.com/bonsai'),
('Кактус', 'Декоративный кактус', 'Растения', '500-1500 ₽', 'https://via.placeholder.com/300x200?text=Кактус', 'https://example.com/cactus'),
('Фикус', 'Комнатное растение фикус', 'Растения', '1000-3000 ₽', 'https://via.placeholder.com/300x200?text=Фикус', 'https://example.com/ficus'),
('Набор для выращивания', 'Набор для выращивания трав', 'Растения', '800-2000 ₽', 'https://via.placeholder.com/300x200?text=Травы', 'https://example.com/herbs'),

-- Услуги
('Массаж', 'Сертификат на расслабляющий массаж', 'Услуги', '2000-5000 ₽', 'https://via.placeholder.com/300x200?text=Массаж', 'https://example.com/massage'),
('Фотосессия', 'Профессиональная фотосессия', 'Услуги', '3000-8000 ₽', 'https://via.placeholder.com/300x200?text=Фото', 'https://example.com/photoshoot'),
('Мастер-класс', 'Кулинарный мастер-класс', 'Услуги', '2000-4000 ₽', 'https://via.placeholder.com/300x200?text=Мастер-класс', 'https://example.com/masterclass'),
('Экскурсия', 'Экскурсия по городу', 'Услуги', '1000-3000 ₽', 'https://via.placeholder.com/300x200?text=Экскурсия', 'https://example.com/tour'),
('Стрижка и укладка', 'Сертификат в салон красоты', 'Услуги', '1500-3500 ₽', 'https://via.placeholder.com/300x200?text=Салон', 'https://example.com/salon'),
('Урок танцев', 'Индивидуальный урок танцев', 'Услуги', '1000-2500 ₽', 'https://via.placeholder.com/300x200?text=Танцы', 'https://example.com/dance'),

-- Хобби
('Набор для вязания', 'Пряжа и спицы для вязания', 'Хобби', '1000-2500 ₽', 'https://via.placeholder.com/300x200?text=Вязание', 'https://example.com/knitting'),
('Пазл', 'Пазл на 1000 элементов', 'Хобби', '500-1500 ₽', 'https://via.placeholder.com/300x200?text=Пазл', 'https://example.com/puzzle'),
('Набор для вышивания', 'Комплект для вышивки крестиком', 'Хобби', '800-2000 ₽', 'https://via.placeholder.com/300x200?text=Вышивка', 'https://example.com/embroidery'),
('Настольная игра', 'Стратегическая настольная игра', 'Хобби', '1500-3500 ₽', 'https://via.placeholder.com/300x200?text=Игра', 'https://example.com/boardgame'),
('Набор для лепки', 'Полимерная глина и инструменты', 'Хобби', '800-2000 ₽', 'https://via.placeholder.com/300x200?text=Лепка', 'https://example.com/clay'),
('Музыкальный инструмент', 'Укулеле для начинающих', 'Хобби', '2000-4000 ₽', 'https://via.placeholder.com/300x200?text=Укулеле', 'https://example.com/ukulele'),
('Набор для каллиграфии', 'Перья и чернила для каллиграфии', 'Хобби', '1000-2500 ₽', 'https://via.placeholder.com/300x200?text=Каллиграфия', 'https://example.com/calligraphy'),

-- Электроника
('Наушники', 'Беспроводные наушники с шумоподавлением', 'Электроника', '3000-8000 ₽', 'https://via.placeholder.com/300x200?text=Наушники', 'https://example.com/headphones'),
('Портативная колонка', 'Bluetooth колонка с мощным звуком', 'Электроника', '2000-6000 ₽', 'https://via.placeholder.com/300x200?text=Колонка', 'https://example.com/speaker'),
('Электронная книга', 'E-reader с подсветкой экрана', 'Электроника', '5000-12000 ₽', 'https://via.placeholder.com/300x200?text=E-reader', 'https://example.com/ereader'),
('Фитнес-браслет', 'Трекер активности и сна', 'Электроника', '2000-5000 ₽', 'https://via.placeholder.com/300x200?text=Браслет', 'https://example.com/fitnesstracker'),
('Веб-камера', 'HD веб-камера для видеозвонков', 'Электроника', '1500-4000 ₽', 'https://via.placeholder.com/300x200?text=Камера', 'https://example.com/webcam'),
('Клавиатура', 'Механическая клавиатура с подсветкой', 'Электроника', '3000-8000 ₽', 'https://via.placeholder.com/300x200?text=Клавиатура', 'https://example.com/keyboard'),
('Внешний аккумулятор', 'Power bank на 20000 mAh', 'Электроника', '1500-3500 ₽', 'https://via.placeholder.com/300x200?text=Powerbank', 'https://example.com/powerbank'),
('Умная лампа', 'Лампа с управлением через приложение', 'Электроника', '2000-5000 ₽', 'https://via.placeholder.com/300x200?text=Лампа', 'https://example.com/smartlamp');
