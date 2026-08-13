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

<style>

*{
    box-sizing:border-box;
}

body{
    margin:0;
    font-family:Arial,sans-serif;
    background:#0f172a;
    color:white;
}

/* HEADER */

header{
    background:#020617;
    padding:20px;
    position:relative;
    text-align:center;
    font-size:32px;
    font-weight:bold;
    border-bottom:1px solid #1e293b;
}

#themeToggle{
    position:absolute;
    left:20px;
    top:18px;

    width:45px;
    height:45px;

    border:none;
    border-radius:10px;

    background:#22c55e;
    cursor:pointer;
    font-size:18px;
}

.logout-btn{
    position:absolute;
    right:20px;
    top:28px;

    color:#94a3b8;
    text-decoration:none;
    font-size:14px;
}

.logout-btn:hover{
    color:white;
}

/* FORM */

form{
    display:flex;
    gap:10px;

    padding:20px;
}

form input{
    flex:1;

    padding:14px;

    border:none;
    border-radius:12px;

    font-size:16px;
}

form button{
    width:60px;

    border:none;
    border-radius:12px;

    background:#22c55e;

    font-size:28px;
    font-weight:bold;

    cursor:pointer;
}

/* LAYOUT */

.container{
    display:flex;
    gap:20px;

    padding:0 20px 20px;
}

/* SIDEBAR */

.sidebar{
    width:280px;

    display:flex;
    flex-direction:column;
    gap:20px;
}

.sidebar-card{
    background:#020b2d;

    padding:20px;

    border-radius:16px;

    cursor:pointer;

    transition:0.2s;
}

.sidebar-card:hover{
    transform:translateX(5px);
}

.sidebar-card.active{
    border:2px solid #22c55e;
}

/* CONTENT */

.content{
    flex:1;
}

.block{
    background:#020b2d;

    border-radius:20px;

    padding:20px;
}

.block h2{
    margin-top:0;
}

/* TASK */

.task{
    display:flex;
    justify-content:space-between;
    align-items:center;

    background:#0f172a;

    padding:16px;

    border-radius:14px;

    margin-bottom:14px;
}

.task-title{
    font-size:18px;
    margin-bottom:5px;
}

.date{
    font-size:12px;
    color:#94a3b8;
}

.task-actions{
    display:flex;
    gap:10px;
}

.task-actions a{
    text-decoration:none;
    font-size:22px;
}

/* DONE */

.done{
    opacity:0.6;
    text-decoration:line-through;
}

/* DASHBOARD */

.dashboard-link{
    text-decoration:none;
    color:white;
}

/* TOAST */

.toast{
    position:fixed;

    bottom:30px;
    left:50%;

    transform:translateX(-50%);

    background:#22c55e;
    color:black;

    padding:14px 20px;

    border-radius:10px;

    font-weight:bold;

    opacity:0;

    transition:0.3s;

    pointer-events:none;
}

.toast.show{
    opacity:1;
}

/* LIGHT THEME */

body.light{
    background:#f1f5f9;
    color:black;
}

body.light header{
    background:#dbeafe;
    color:black;
}

body.light .block{
    background:white;
}

body.light .sidebar-card{
    background:white;
    color:black;
}

body.light .task{
    background:#e2e8f0;
}

body.light form input{
    background:white;
    color:black;
}

body.light #themeToggle{
    background:#2563eb;
    color:white;
}

</style>

</head>

<body>

<header>

📝 My To-Do

<button id="themeToggle">🌙</button>

<a href="logout.php" class="logout-btn">
    Exit
</a>

</header>

<!-- FORM -->

<form id="taskForm">

<input
    type="text"
    name="task"
    placeholder="What needs to be done?"
    required
>

<button type="submit">
    +
</button>

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

    <div>

        <div class="task-title">
            <?= htmlspecialchars($task['title']) ?>
        </div>

        <div class="date">
            <?= $task['created_at'] ?>
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

<!-- DONE -->

<div class="block" id="doneBlock" style="display:none;">

<h2>Done Tasks</h2>

<?php while($task = $doneTasks->fetch_assoc()): ?>

<div class="task done">

    <?= htmlspecialchars($task['title']) ?>

</div>

<?php endwhile; ?>

</div>

</div>

</div>

<!-- TOAST -->

<div class="toast" id="toast">
    ✔ Done
</div>

<script>

// THEME

const toggleBtn = document.getElementById("themeToggle");

if(localStorage.getItem("theme") === "light"){
    document.body.classList.add("light");
    toggleBtn.innerHTML = "☀️";
}

toggleBtn.addEventListener("click", () => {

    document.body.classList.toggle("light");

    if(document.body.classList.contains("light")){

        localStorage.setItem("theme","light");
        toggleBtn.innerHTML = "☀️";

    }else{

        localStorage.setItem("theme","dark");
        toggleBtn.innerHTML = "🌙";

    }

});

// SWITCH BLOCKS

const btnActive = document.getElementById("btnActive");
const btnDone = document.getElementById("btnDone");

const activeBlock = document.getElementById("activeBlock");
const doneBlock = document.getElementById("doneBlock");

btnActive.addEventListener("click", () => {

    activeBlock.style.display = "block";
    doneBlock.style.display = "none";

    btnActive.classList.add("active");
    btnDone.classList.remove("active");

});

btnDone.addEventListener("click", () => {

    activeBlock.style.display = "none";
    doneBlock.style.display = "block";

    btnDone.classList.add("active");
    btnActive.classList.remove("active");

});

// TOAST

function showToast(text){

    const toast = document.getElementById("toast");

    toast.innerText = text;

    toast.classList.add("show");

    setTimeout(() => {
        toast.classList.remove("show");
    }, 2000);

}

// ADD TASK

const form = document.getElementById("taskForm");

form.addEventListener("submit", function(e){

    e.preventDefault();

    const input = form.querySelector("input[name='task']");
    const text = input.value.trim();

    if(!text) return;

    fetch("add_task.php",{

        method:"POST",

        headers:{
            "Content-Type":"application/x-www-form-urlencoded"
        },

        body:"task=" + encodeURIComponent(text)

    })

    .then(res => res.json())

    .then(data => {

        const container = document.getElementById("activeBlock");

        const div = document.createElement("div");

        div.className = "task";

        div.innerHTML = `
            <div>
                <div class="task-title">
                    ${data.title}
                </div>

                <div class="date">
                    ${data.date}
                </div>
            </div>

            <div class="task-actions">

                <a href="#"
                   onclick="markDone(${data.id}, this)">
                   ✔️
                </a>

                <a href="#"
                   onclick="deleteTask(${data.id}, this)"
                   style="color:red;">
                   🗑️
                </a>

            </div>
        `;

        container.appendChild(div);

        input.value = "";

        showToast("Task added 🚀");

    });

});

// DONE

function markDone(id, el){

    fetch("done.php?id=" + id)

    .then(response => {

        if (!response.ok) {
            throw new Error("Failed to mark task as done");
        }

        const task = el.closest(".task");
        const doneBlock = document.getElementById("doneBlock");

        task.classList.add("done");

        const actions = task.querySelector(".task-actions");

        if (actions) {
            actions.remove();
        }

        doneBlock.appendChild(task);

        showToast("Task done ✔️");

    })
    .catch(error => {

        console.error(error);
        showToast("Error ❌");

    });

}

// DELETE

function deleteTask(id, el){

    if(!confirm("Delete task?")) return;

    fetch("delete.php?id=" + id)

    .then(() => {

        el.closest(".task").remove();

        showToast("Task deleted 🗑️");

    });

}

</script>

</body>
</html>