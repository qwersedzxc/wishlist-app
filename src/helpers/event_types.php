<?php
/**
 * Функция для перевода типов событий на русский язык
 */
function translateEventType($eventType) {
    $translations = [
        'birthday' => 'День рождения',
        'new_year' => 'Новый год',
        'wedding' => 'Свадьба',
        'anniversary' => 'Годовщина',
        'graduation' => 'Выпускной',
        'housewarming' => 'Новоселье',
        'baby_shower' => 'Рождение ребенка',
        'valentine' => 'День святого Валентина',
        'christmas' => 'Рождество',
        'easter' => 'Пасха',
        'other' => 'Другое'
    ];
    
    return $translations[$eventType] ?? $eventType;
}

/**
 * Получить все доступные типы событий
 */
function getEventTypes() {
    return [
        'birthday' => 'День рождения',
        'new_year' => 'Новый год',
        'wedding' => 'Свадьба',
        'anniversary' => 'Годовщина',
        'graduation' => 'Выпускной',
        'housewarming' => 'Новоселье',
        'baby_shower' => 'Рождение ребенка',
        'valentine' => 'День святого Валентина',
        'christmas' => 'Рождество',
        'easter' => 'Пасха',
        'other' => 'Другое'
    ];
}


/**
 * Функция для перевода приоритетов на русский язык
 */
function translatePriority($priority) {
    $translations = [
        'low' => 'Низкий',
        'medium' => 'Средний',
        'high' => 'Высокий'
    ];
    
    return $translations[$priority] ?? $priority;
}

/**
 * Получить все доступные приоритеты
 */
function getPriorities() {
    return [
        'low' => 'Низкий приоритет',
        'medium' => 'Средний приоритет',
        'high' => 'Высокий приоритет'
    ];
}
