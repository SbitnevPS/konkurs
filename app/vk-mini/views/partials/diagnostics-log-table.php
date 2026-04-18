<div class="card">
    <h3>Последние логи</h3>
    <table>
        <tr><th>Время</th><th>Уровень</th><th>Action</th><th>Сообщение</th><th>Контекст</th></tr>
        <?php foreach (($logs ?? []) as $log): ?>
            <tr>
                <td><?= htmlspecialchars((string)$log['created_at']) ?></td>
                <td><?= htmlspecialchars((string)$log['level']) ?></td>
                <td><?= htmlspecialchars((string)$log['action']) ?></td>
                <td><?= htmlspecialchars((string)$log['message']) ?></td>
                <td><pre><?= htmlspecialchars((string)$log['context_json']) ?></pre></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
