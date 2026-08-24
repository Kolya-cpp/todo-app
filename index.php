<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Активні задачі
$stmt = $conn->prepare("
    SELECT * FROM tasks
    WHERE user_id = ? AND is_done = 0
    ORDER BY created_at DESC
");

$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$activeTasks = $stmt->get_result();

// Завдання на сьогодні

$stmtToday = $conn->prepare("
    SELECT * FROM tasks
    WHERE user_id = ?
    AND is_done = 0
    AND due_date = CURDATE()
    ORDER BY created_at DESC
");

$stmtToday->bind_param("i", $_SESSION['user_id']);
$stmtToday->execute();
$todayTasks = $stmtToday->get_result();

// Завтра

$stmtTomorrow = $conn->prepare("
    SELECT * FROM tasks
    WHERE user_id = ?
    AND is_done = 0
    AND due_date = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
    ORDER BY created_at DESC
");

$stmtTomorrow->bind_param("i", $_SESSION['user_id']);
$stmtTomorrow->execute();
$tomorrowTasks = $stmtTomorrow->get_result();

// Завдання на цей тиждень

$stmtWeek = $conn->prepare("
    SELECT * FROM tasks
    WHERE user_id = ?
    AND is_done = 0
    AND due_date BETWEEN
        DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
        AND
        DATE_ADD(CURDATE(), INTERVAL (6 - WEEKDAY(CURDATE())) DAY)
    ORDER BY due_date ASC, created_at DESC
");

$stmtWeek->bind_param("i", $_SESSION['user_id']);
$stmtWeek->execute();
$weekTasks = $stmtWeek->get_result();

// Наступного тижня

$stmtNextWeek = $conn->prepare("
    SELECT * FROM tasks
    WHERE user_id = ?
    AND is_done = 0
    AND due_date BETWEEN
        DATE_ADD(CURDATE(), INTERVAL (7 - WEEKDAY(CURDATE())) DAY)
        AND
        DATE_ADD(CURDATE(), INTERVAL (13 - WEEKDAY(CURDATE())) DAY)
    ORDER BY due_date ASC, created_at DESC
");

$stmtNextWeek->bind_param("i", $_SESSION['user_id']);
$stmtNextWeek->execute();
$nextWeekTasks = $stmtNextWeek->get_result();

// Цього місяця

$stmtMonth = $conn->prepare("
    SELECT * FROM tasks
    WHERE user_id = ?
    AND is_done = 0
    AND due_date BETWEEN
        DATE_FORMAT(CURDATE(), '%Y-%m-01')
        AND
        LAST_DAY(CURDATE())
    ORDER BY due_date ASC, created_at DESC
");

$stmtMonth->bind_param("i", $_SESSION['user_id']);
$stmtMonth->execute();
$monthTasks = $stmtMonth->get_result();

// Наступного місяця

$stmtNextMonth = $conn->prepare("
    SELECT * FROM tasks
    WHERE user_id = ?
    AND is_done = 0
    AND due_date BETWEEN
        DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), '%Y-%m-01')
        AND
        LAST_DAY(DATE_ADD(CURDATE(), INTERVAL 1 MONTH))
    ORDER BY due_date ASC, created_at DESC
");

$stmtNextMonth->bind_param("i", $_SESSION['user_id']);
$stmtNextMonth->execute();
$nextMonthTasks = $stmtNextMonth->get_result();

// 3 місяці

$stmt3Months = $conn->prepare("
    SELECT * FROM tasks
    WHERE user_id = ?
    AND is_done = 0
    AND due_date BETWEEN
        DATE_ADD(CURDATE(), INTERVAL 2 MONTH)
        AND
        LAST_DAY(DATE_ADD(CURDATE(), INTERVAL 3 MONTH))
    ORDER BY due_date ASC, created_at DESC
");

$stmt3Months->bind_param("i", $_SESSION['user_id']);
$stmt3Months->execute();
$months3Tasks = $stmt3Months->get_result();

// Виконані задачі
$stmtDone = $conn->prepare("
    SELECT * FROM tasks
    WHERE user_id = ? AND is_done = 1
    ORDER BY created_at DESC
");

$stmtDone->bind_param("i", $_SESSION['user_id']);
$stmtDone->execute();
$doneTasks = $stmtDone->get_result();
?>

<!DOCTYPE html>
<html lang="uk">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>My To-Do</title>

<link rel="stylesheet" href="css/styles.css">
<link rel="stylesheet" href="css/tasks.css">

</head>

<body>

<header>

My To-Do

<button id="themeToggle">🌙</button>

<a href="logout.php" class="logout-btn">
    Exit
</a>

</header>

<!-- FORM -->

<form id="taskForm" class="task-form">

    <div class="main-task-input">
        <input
            type="text"
            name="task"
            placeholder="Що потрібно зробити?"
            required
        >

        <button type="submit">
            +
        </button>
    </div>

    <div class="task-options">

        <textarea
            name="description"
            placeholder="Опис завдання..."
        ></textarea>

        <div class="option-row">

            <div class="option">
                <label for="due_date">
                    📅 Дата виконання
                </label>

                <input
                    type="date"
                    id="due_date"
                    name="due_date"
                >
            </div>

            <div class="option">
                <label for="priority">
                    🔥 Пріоритет
                </label>

                <select id="priority" name="priority">

                    <option value="low">
                        Низький
                    </option>

                    <option value="medium" selected>
                        Середній
                    </option>

                    <option value="high">
                        Високий
                    </option>

                </select>
            </div>

            <div class="option">
                <label for="category">
                    📁 Категорія
                </label>

                <input
                    type="text"
                    id="category"
                    name="category"
                    placeholder="Навчання"
                >
            </div>

        </div>

    </div>

</form>


<!-- MAIN -->

<div class="container">

<!-- SIDEBAR -->

<div class="sidebar">

    <div class="sidebar-card active" id="btnActive">
        ⏳ Active
    </div>

    <div class="sidebar-card" id="btnDone">
        ✅ Done
    </div>

    <div class="sidebar-divider"></div>

    <div class="sidebar-card" id="btnToday">
        📅 Сьогодні
    </div>

    <div class="sidebar-card" id="btnTomorrow">
        📌 Завтра
    </div>

    <div class="sidebar-card" id="btnWeek">
        📆 Цього тижня
    </div>

    <div class="sidebar-card" id="btnNextWeek">
        📆 Наступного тижня
    </div>

    <div class="sidebar-card" id="btnMonth">
        🗓️ Цього місяця
    </div>
    <div class="sidebar-card" id="btnNextMonth">
        🗓️ Наступного місяця
    </div>

    <div class="sidebar-card" id="btn3Months">
        🗓️ Через 3 місяці
    </div>

    <div class="sidebar-divider"></div>

    <a href="dashboard.php" class="dashboard-link">
        
    <div class="sidebar-card">
        📊 Dashboard
    </div>
    
    </a>

</div>

<!-- CONTENT -->

<div class="content">

<!-- ACTIVE -->

<div class="block" id="activeBlock">

<h2>Active Tasks</h2>

<?php while($task = $activeTasks->fetch_assoc()): ?>

<div class="task">

    <div class="task-info">

        <div class="task-title">
            <?= htmlspecialchars($task['title']) ?>
        </div>

        <?php if (!empty($task['description'])): ?>

            <div class="task-description">
                <?= htmlspecialchars($task['description']) ?>
            </div>

        <?php endif; ?>

        <div class="task-meta">

            <?php if (!empty($task['due_date'])): ?>

                <span>
                    📅
                    <?= date("d.m.Y", strtotime($task['due_date'])) ?>
                </span>

            <?php endif; ?>


            <?php

            $priorityNames = [
                'low' => 'Низький',
                'medium' => 'Середній',
                'high' => 'Високий'
            ];

            ?>

            <span class="priority priority-<?= htmlspecialchars($task['priority']) ?>">
                🔥
                <?= $priorityNames[$task['priority']] ?? 'Середній' ?>
            </span>


            <?php if (!empty($task['category'])): ?>

                <span>
                    📁
                    <?= htmlspecialchars($task['category']) ?>
                </span>

            <?php endif; ?>

        </div>

    </div>

    <div class="task-actions">

        <a href="#"
           onclick="markDone(<?= $task['id'] ?>, this)">
            ✔️
        </a>

        <a href="#"
           onclick="deleteTask(<?= $task['id'] ?>, this)"
           style="color:red;">
            🗑️
        </a>

    </div>

</div>

<?php endwhile; ?>

</div>

<!-- TODAY -->

<div class="block" id="todayBlock" style="display:none;">

    <h2>Сьогодні</h2>

    <?php if ($todayTasks->num_rows === 0): ?>

        <p>На сьогодні завдань немає 🎉</p>

    <?php else: ?>

        <?php while($task = $todayTasks->fetch_assoc()): ?>

            <div class="task">

                <div class="task-info">

                    <div class="task-title">
                        <?= htmlspecialchars($task['title']) ?>
                    </div>

                    <?php if (!empty($task['description'])): ?>

                        <div class="task-description">
                            <?= htmlspecialchars($task['description']) ?>
                        </div>

                    <?php endif; ?>

                    <div class="task-meta">

                        <span>
                            📅 Сьогодні
                        </span>

                        <?php
                        $priorityNames = [
                            'low' => 'Низький',
                            'medium' => 'Середній',
                            'high' => 'Високий'
                        ];
                        ?>

                        <span class="priority priority-<?= htmlspecialchars($task['priority']) ?>">
                            🔥
                            <?= $priorityNames[$task['priority']] ?? 'Середній' ?>
                        </span>

                        <?php if (!empty($task['category'])): ?>

                            <span>
                                📁
                                <?= htmlspecialchars($task['category']) ?>
                            </span>

                        <?php endif; ?>

                    </div>

                </div>

                <div class="task-actions">

                    <a href="#"
                       onclick="markDone(<?= $task['id'] ?>, this)">
                        ✔️
                    </a>

                    <a href="#"
                       onclick="deleteTask(<?= $task['id'] ?>, this)"
                       style="color:red;">
                        🗑️
                    </a>

                </div>

            </div>

        <?php endwhile; ?>

    <?php endif; ?>

</div>

<!-- TOMORROW -->

<div class="block" id="tomorrowBlock" style="display:none;">

    <h2>Завтра</h2>

    <?php if ($tomorrowTasks->num_rows === 0): ?>

        <p>На завтра завдань немає 🎉</p>

    <?php else: ?>

        <?php while($task = $tomorrowTasks->fetch_assoc()): ?>

            <div class="task">

                <div class="task-info">

                    <div class="task-title">
                        <?= htmlspecialchars($task['title']) ?>
                    </div>

                    <?php if (!empty($task['description'])): ?>

                        <div class="task-description">
                            <?= htmlspecialchars($task['description']) ?>
                        </div>

                    <?php endif; ?>

                    <div class="task-meta">

                        <span>
                            📅 Завтра
                        </span>

                        <?php
                        $priorityNames = [
                            'low' => 'Низький',
                            'medium' => 'Середній',
                            'high' => 'Високий'
                        ];
                        ?>

                        <span class="priority priority-<?= htmlspecialchars($task['priority']) ?>">
                            🔥
                            <?= $priorityNames[$task['priority']] ?? 'Середній' ?>
                        </span>

                        <?php if (!empty($task['category'])): ?>

                            <span>
                                📁
                                <?= htmlspecialchars($task['category']) ?>
                            </span>

                        <?php endif; ?>

                    </div>

                </div>

                <div class="task-actions">

                    <a href="#"
                       onclick="markDone(<?= $task['id'] ?>, this)">
                        ✔️
                    </a>

                    <a href="#"
                       onclick="deleteTask(<?= $task['id'] ?>, this)"
                       style="color:red;">
                        🗑️
                    </a>

                </div>

            </div>

        <?php endwhile; ?>

    <?php endif; ?>

</div>

<!-- WEEK -->

<div class="block" id="weekBlock" style="display:none;">

    <h2>Цього тижня</h2>

    <?php if ($weekTasks->num_rows === 0): ?>

        <p>На цей тиждень завдань немає 🎉</p>

    <?php else: ?>

        <?php while($task = $weekTasks->fetch_assoc()): ?>

            <div class="task">

                <div class="task-info">

                    <div class="task-title">
                        <?= htmlspecialchars($task['title']) ?>
                    </div>

                    <?php if (!empty($task['description'])): ?>

                        <div class="task-description">
                            <?= htmlspecialchars($task['description']) ?>
                        </div>

                    <?php endif; ?>

                    <div class="task-meta">

                        <?php if (!empty($task['due_date'])): ?>

                            <span>
                                📅
                                <?= date("d.m.Y", strtotime($task['due_date'])) ?>
                            </span>

                        <?php endif; ?>

                        <?php
                        $priorityNames = [
                            'low' => 'Низький',
                            'medium' => 'Середній',
                            'high' => 'Високий'
                        ];
                        ?>

                        <span class="priority priority-<?= htmlspecialchars($task['priority']) ?>">
                            🔥
                            <?= $priorityNames[$task['priority']] ?? 'Середній' ?>
                        </span>

                        <?php if (!empty($task['category'])): ?>

                            <span>
                                📁
                                <?= htmlspecialchars($task['category']) ?>
                            </span>

                        <?php endif; ?>

                    </div>

                </div>

                <div class="task-actions">

                    <a href="#"
                       onclick="markDone(<?= $task['id'] ?>, this)">
                        ✔️
                    </a>

                    <a href="#"
                       onclick="deleteTask(<?= $task['id'] ?>, this)"
                       style="color:red;">
                        🗑️
                    </a>

                </div>

            </div>

        <?php endwhile; ?>

    <?php endif; ?>

</div>

<!-- NEXT WEEK -->

<div class="block" id="nextWeekBlock" style="display:none;">

    <h2>Наступного тижня</h2>

    <?php if ($nextWeekTasks->num_rows === 0): ?>

        <p>На наступний тиждень завдань немає 🎉</p>

    <?php else: ?>

        <?php while($task = $nextWeekTasks->fetch_assoc()): ?>

            <div class="task">

                <div class="task-info">

                    <div class="task-title">
                        <?= htmlspecialchars($task['title']) ?>
                    </div>

                    <?php if (!empty($task['description'])): ?>

                        <div class="task-description">
                            <?= htmlspecialchars($task['description']) ?>
                        </div>

                    <?php endif; ?>

                    <div class="task-meta">

                        <?php if (!empty($task['due_date'])): ?>

                            <span>
                                📅
                                <?= date("d.m.Y", strtotime($task['due_date'])) ?>
                            </span>

                        <?php endif; ?>

                        <?php
                        $priorityNames = [
                            'low' => 'Низький',
                            'medium' => 'Середній',
                            'high' => 'Високий'
                        ];
                        ?>

                        <span class="priority priority-<?= htmlspecialchars($task['priority']) ?>">
                            🔥
                            <?= $priorityNames[$task['priority']] ?? 'Середній' ?>
                        </span>

                        <?php if (!empty($task['category'])): ?>

                            <span>
                                📁
                                <?= htmlspecialchars($task['category']) ?>
                            </span>

                        <?php endif; ?>

                    </div>

                </div>

                <div class="task-actions">

                    <a href="#"
                       onclick="markDone(<?= $task['id'] ?>, this)">
                        ✔️
                    </a>

                    <a href="#"
                       onclick="deleteTask(<?= $task['id'] ?>, this)"
                       style="color:red;">
                        🗑️
                    </a>

                </div>

            </div>

        <?php endwhile; ?>

    <?php endif; ?>

</div>

<!-- MONTH -->

<div class="block" id="monthBlock" style="display:none;">

    <h2>Цього місяця</h2>

    <?php if ($monthTasks->num_rows === 0): ?>

        <p>На цей місяць завдань немає 🎉</p>

    <?php else: ?>

        <?php while($task = $monthTasks->fetch_assoc()): ?>

            <div class="task">

                <div class="task-info">

                    <div class="task-title">
                        <?= htmlspecialchars($task['title']) ?>
                    </div>

                    <?php if (!empty($task['description'])): ?>

                        <div class="task-description">
                            <?= htmlspecialchars($task['description']) ?>
                        </div>

                    <?php endif; ?>

                    <div class="task-meta">

                        <?php if (!empty($task['due_date'])): ?>

                            <span>
                                📅
                                <?= date("d.m.Y", strtotime($task['due_date'])) ?>
                            </span>

                        <?php endif; ?>

                        <?php
                        $priorityNames = [
                            'low' => 'Низький',
                            'medium' => 'Середній',
                            'high' => 'Високий'
                        ];
                        ?>

                        <span class="priority priority-<?= htmlspecialchars($task['priority']) ?>">
                            🔥
                            <?= $priorityNames[$task['priority']] ?? 'Середній' ?>
                        </span>

                        <?php if (!empty($task['category'])): ?>

                            <span>
                                📁
                                <?= htmlspecialchars($task['category']) ?>
                            </span>

                        <?php endif; ?>

                    </div>

                </div>

                <div class="task-actions">

                    <a href="#"
                       onclick="markDone(<?= $task['id'] ?>, this)">
                        ✔️
                    </a>

                    <a href="#"
                       onclick="deleteTask(<?= $task['id'] ?>, this)"
                       style="color:red;">
                        🗑️
                    </a>

                </div>

            </div>

        <?php endwhile; ?>

    <?php endif; ?>

</div>

<!-- NEXT MONTH -->

 <div class="block" id="nextMonthBlock" style="display:none;">

    <h2>Наступного місяця</h2>

    <?php if ($nextMonthTasks->num_rows === 0): ?>

        <p>На наступний місяць завдань немає 🎉</p>

    <?php else: ?>

        <?php while($task = $nextMonthTasks->fetch_assoc()): ?>

            <div class="task">

                <div class="task-info">

                    <div class="task-title">
                        <?= htmlspecialchars($task['title']) ?>
                    </div>

                    <?php if (!empty($task['description'])): ?>

                        <div class="task-description">
                            <?= htmlspecialchars($task['description']) ?>
                        </div>

                    <?php endif; ?>

                    <div class="task-meta">

                        <?php if (!empty($task['due_date'])): ?>

                            <span>
                                📅
                                <?= date("d.m.Y", strtotime($task['due_date'])) ?>
                            </span>

                        <?php endif; ?>

                        <?php
                        $priorityNames = [
                            'low' => 'Низький',
                            'medium' => 'Середній',
                            'high' => 'Високий'
                        ];
                        ?>

                        <span class="priority priority-<?= htmlspecialchars($task['priority']) ?>">
                            🔥
                            <?= $priorityNames[$task['priority']] ?? 'Середній' ?>
                        </span>

                        <?php if (!empty($task['category'])): ?>

                            <span>
                                📁
                                <?= htmlspecialchars($task['category']) ?>
                            </span>

                        <?php endif; ?>

                    </div>

                </div>

                <div class="task-actions">

                    <a href="#"
                       onclick="markDone(<?= $task['id'] ?>, this)">
                        ✔️
                    </a>

                    <a href="#"
                       onclick="deleteTask(<?= $task['id'] ?>, this)"
                       style="color:red;">
                        🗑️
                    </a>

                </div>

            </div>

        <?php endwhile; ?>

    <?php endif; ?>
</div>

<!-- 3 MONTHS -->
<div class="block" id="3MonthsBlock" style="display:none;">

    <h2>Через 3 місяці</h2>

    <?php if ($months3Tasks->num_rows === 0): ?>

        <p>Через 3 місяці завдань немає 🎉</p>

    <?php else: ?>

        <?php while($task = $months3Tasks->fetch_assoc()): ?>

            <div class="task">

                <div class="task-info">

                    <div class="task-title">
                        <?= htmlspecialchars($task['title']) ?>
                    </div>

                    <?php if (!empty($task['description'])): ?>

                        <div class="task-description">
                            <?= htmlspecialchars($task['description']) ?>
                        </div>

                    <?php endif; ?>

                    <div class="task-meta">

                        <?php if (!empty($task['due_date'])): ?>

                            <span>
                                📅
                                <?= date("d.m.Y", strtotime($task['due_date'])) ?>
                            </span>

                        <?php endif; ?>

                        <?php
                        $priorityNames = [
                            'low' => 'Низький',
                            'medium' => 'Середній',
                            'high' => 'Високий'
                        ];
                        ?>

                        <span class="priority priority-<?= htmlspecialchars($task['priority']) ?>">
                            🔥
                            <?= $priorityNames[$task['priority']] ?? 'Середній' ?>
                        </span>

                        <?php if (!empty($task['category'])): ?>

                            <span>
                                📁
                                <?= htmlspecialchars($task['category']) ?>
                            </span>

                        <?php endif; ?>

                    </div>

                </div>

                <div class="task-actions">

                    <a href="#"
                       onclick="markDone(<?= $task['id'] ?>, this)">
                        ✔️
                    </a>

                    <a href="#"
                       onclick="deleteTask(<?= $task['id'] ?>, this)"
                       style="color:red;">
                        🗑️
                    </a>

                </div>

            </div>

        <?php endwhile; ?>

    <?php endif; ?>
</div>

<!-- DONE -->

<div class="block" id="doneBlock" style="display:none;">

    <h2>Виконані завдання</h2>

    <?php while($task = $doneTasks->fetch_assoc()): ?>

        <div class="task done">

            <div class="task-info">

                <div class="task-title">
                    <?= htmlspecialchars($task['title']) ?>
                </div>

                <?php if (!empty($task['description'])): ?>

                    <div class="task-description">
                        <?= htmlspecialchars($task['description']) ?>
                    </div>

                <?php endif; ?>

                <div class="task-meta">

                    <?php if (!empty($task['due_date'])): ?>

                        <span>
                            📅
                            <?= date("d.m.Y", strtotime($task['due_date'])) ?>
                        </span>

                    <?php endif; ?>

                    <?php
                    $priorityNames = [
                        'low' => 'Низький',
                        'medium' => 'Середній',
                        'high' => 'Високий'
                    ];
                    ?>

                    <span class="priority priority-<?= htmlspecialchars($task['priority']) ?>">
                        🔥
                        <?= $priorityNames[$task['priority']] ?? 'Середній' ?>
                    </span>

                    <?php if (!empty($task['category'])): ?>

                        <span>
                            📁
                            <?= htmlspecialchars($task['category']) ?>
                        </span>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    <?php endwhile; ?>
    </div> <!-- content -->
    </div> <!-- container -->

<script src="js/theme.js"></script>
<script src="js/ui.js"></script>
<script src="js/tasks.js"></script>

</body>
</html>