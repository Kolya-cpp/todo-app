// MARK TASK AS DONE

function markDone(id, el) {

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

            showToast("Завдання виконано ✔️");

        })

        .catch(error => {

            console.error(error);
            showToast("Помилка ❌");

        });
}


// DELETE TASK

function deleteTask(id, el) {

    if (!confirm("Видалити завдання?")) {
        return;
    }

    fetch("delete.php?id=" + id)

        .then(() => {

            el.closest(".task").remove();

            showToast("Завдання видалено 🗑️");

        });

}

// ADD TASK

const form = document.getElementById("taskForm");

form.addEventListener("submit", function(e) {

    e.preventDefault();

    const input = form.querySelector("input[name='task']");
    const description = form.querySelector("textarea[name='description']");
    const dueDate = form.querySelector("input[name='due_date']");
    const priority = form.querySelector("select[name='priority']");
    const category = form.querySelector("input[name='category']");

    const text = input.value.trim();

    if (!text) return;

    const formData = new URLSearchParams();

    formData.append("task", text);
    formData.append("description", description.value.trim());
    formData.append("due_date", dueDate.value);
    formData.append("priority", priority.value);
    formData.append("category", category.value.trim());

    fetch("add_task.php", {

        method: "POST",

        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },

        body: formData.toString()

    })

    .then(res => res.json())

    .then(data => {

        const container = document.getElementById("activeBlock");

        const div = document.createElement("div");

        div.className = "task";

        div.innerHTML = `
            <div class="task-info">

                <div class="task-title">
                    ${data.title}
                </div>

                ${data.description ? `
                    <div class="task-description">
                        ${data.description}
                    </div>
                ` : ""}

                <div class="task-meta">

                    ${data.due_date ? `
                        <span>📅 ${data.due_date}</span>
                    ` : ""}

                    <span class="priority priority-${data.priority}">
                        🔥 ${data.priority}
                    </span>

                    ${data.category ? `
                        <span>📁 ${data.category}</span>
                    ` : ""}

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

        // Очищуємо форму

        input.value = "";
        description.value = "";
        dueDate.value = "";
        priority.value = "medium";
        category.value = "";

        showToast("Завдання додано 🚀");

    })

    .catch(error => {

        console.error(error);

        showToast("Помилка ❌");

    });

});