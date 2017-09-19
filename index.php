<?php
require_once 'init.php';

if (!$link) {
    $error = mysqli_connect_error();
    show_error($content, $error);
}
else {
    // Запрос на получение списка категорий
    $sql = 'SELECT `id`, `name` FROM categories';

    // Выполняем запрос и получаем результат
    $result = mysqli_query($link, $sql);

    // запрос выполнен успешно
    if ($result) {
        // получаем все категории в виде двумерного массива
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    else { // запрос выполнился с ошибкой

        // получить текст последней ошибки
        $error = mysqli_error($link);
        show_error($content, $error);
    }

    $cur_page = $_GET['page'] ?? 1;
    $page_items = 6;

    $result = mysqli_query($link, "SELECT COUNT(*) as cnt FROM gifs");
    $items_count = mysqli_fetch_assoc($result)['cnt'];

    $pages_count = ceil($items_count / $page_items);
    $offset = ($cur_page - 1) * $page_items;

    $pages = range(1, $pages_count);

    // запрос на показ девяти самых популярных гифок
    $sql = 'SELECT gifs.id, title, path, like_count, users.name FROM gifs '
        . 'JOIN users ON gifs.user_id = users.id '
        . 'ORDER BY show_count DESC LIMIT ' . $page_items . ' OFFSET ' . $offset;

    if ($gifs = mysqli_query($link, $sql)) {
        $tpl_data = [
            'gifs' => $gifs,
            'pages' => $pages,
            'pages_count' => $pages_count,
            'cur_page' => $cur_page
        ];

        $content = include_template('main.php', $tpl_data);
    }
    else {
        show_error($content, mysqli_error($link));
    }
}

print include_template('index.php', ['content' => $content, 'categories' => $categories]);