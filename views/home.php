<?php
$title = 'Лента постов';

?>

<?php ob_start(); ?>
    <div class="max-w-4xl mx-auto px-4 py-6">

        <form id="userForm" method="get" action="" class="mb-6 flex items-center gap-3">
            <label for="user_id" class="font-medium text-gray-700">Введите user_id:</label>
            <input
                    type="text"
                    id="user_id"
                    name="user_id"
                    value="<?= htmlspecialchars($_GET['user_id'] ?? 1) ?>"
                    class="border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
        </form>

        <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Лента постов</h1>

        <div class="flex justify-center gap-4 mb-8">
            <button
                    onclick="fillDatabase()"
                    title="+3000 записей"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow transition"
            >Заполнить базу
            </button>

            <button
                    onclick="clearDatabase()"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow transition"
            >Очистить базу
            </button>
        </div>

        <div id="post-feed" class="space-y-6">
            <?php if (!empty($posts['data'])): ?>
                <ul class="space-y-6">
                    <?php foreach ($posts['data'] as $post): ?>
                        <li class="p-6 border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition bg-white">
                            <h2 class="text-xl font-semibold text-gray-900 mb-2"><?= htmlspecialchars($post['title']) ?></h2>
                            <p class="text-gray-700 whitespace-pre-line mb-4"><?= htmlspecialchars($post['content']) ?></p>
                            <div class="text-sm text-gray-500 flex justify-between">
                                <span>Дата: <?= htmlspecialchars($post['created_at']) ?></span>
                                <span>Hotness: <span class="font-semibold"><?= (int)$post['hotness'] ?></span></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="flex justify-center items-center gap-4 mt-10 text-gray-700">
                    <?php if ($posts['prev_page']): ?>
                        <a href="?page=<?= $posts['prev_page'] ?>&user_id=<?= urlencode($_GET['user_id'] ?? 1) ?>"
                           class="px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-100 transition"
                        >« Назад</a>
                    <?php endif; ?>

                    <span class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow">
        Страница <?= $posts['current_page'] ?> из <?= $posts['last_page'] ?>
    </span>

                    <?php if ($posts['next_page']): ?>
                        <a href="?page=<?= $posts['next_page'] ?>&user_id=<?= urlencode($_GET['user_id'] ?? 1) ?>"
                           class="px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-100 transition"
                        >Вперед »</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p class="text-center text-gray-500 text-lg mt-10">Посты не найдены.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.getElementById('user_id').addEventListener('input', function () {
            clearTimeout(window.userIdTimeout);
            window.userIdTimeout = setTimeout(() => {
                document.getElementById('userForm').submit();
            }, 500);
        });

        function fillDatabase() {
            fetch('/api/posts/seed', {method: 'POST'})
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'ok') {
                        location.reload();
                    }
                })
                .catch(() => alert('Ошибка запроса'));
        }

        function clearDatabase() {
            fetch('/api/posts/clear', {method: 'POST'})
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'ok') {
                        location.reload();
                    }
                })
                .catch(() => alert('Ошибка запроса'));
        }
    </script>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
