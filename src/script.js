// 削除処理
async function deleteTodo(id, element) {
  try {
    const response = await fetch('./admin/delete/index.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `delete-id=${id}`
    });

    if (!response.ok) {
      const errorText = await response.text();
      throw new Error('Error from server: ' + errorText);
    }

    element.remove();
  } catch (error) {
    alert('Error: ' + error.message);
  }
}

// ボタンに削除処理を追加
const deleteButtons = document.querySelectorAll('.js-delete-todo');
deleteButtons.forEach(button => {
  button.addEventListener('click', () => {
    const todoId = button.parentNode.getAttribute('data-id');
    const parentNode = button.parentNode;
    deleteTodo(todoId, parentNode);
  });
});

// ステータス追加処理
const addTodoElement = (text, id) => {
  const template = document.getElementById('js-template').content.cloneNode(true);
  template.getElementById('js-todo-text').textContent = text;

  const todoElement = template.getElementById("js-todo-template");
  todoElement.setAttribute("data-id", id);

  const completeButton = template.getElementById('js-complete-todo-template');
  completeButton.setAttribute('data-id', id);
  completeButton.addEventListener('click', () => {
    updateTodo(id);
  });

  template.getElementById('js-edit-todo-template').href = `edit/index.php?id=${id}&text=${text}`;

  const deleteButton = template.getElementById('js-delete-todo-template');
  deleteButton.setAttribute('data-id', id);
  deleteButton.addEventListener('click', () => {
    deleteTodo(id, deleteButton.parentNode);
  });

  document.getElementById('js-todo-list').appendChild(template);
}

async function createTodo() {
  const todoInput = document.getElementById('todo-text');
  const todoText = todoInput.value;

  try {
    const response = await fetch('./admin/create/index.php',{
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `text=${encodeURIComponent(todoText)}`
    }
    );

    if (!response.ok) {
      throw new Error('Failed to create todo: ');
    }

    const data = await response.json();
    addTodoElement(todoText, data.id);

    todoInput.value = '';
  } catch (error) {
    alert('Error: ' + error.message);
  }
}

document.getElementById('js-create-todo').addEventListener('click', createTodo);

// 更新処理
function updateTodoView(id, complete) {
  const todo = document.querySelector(`[data-id="${id}"]`);
  const btn = todo.querySelector('.js-complete-todo');

  if (complete == 1) {
    btn.textContent = 'Undo';
  } else {
    btn.textContent = 'Complete';
  }
}

async function updateTodo(id) {
  const formData = new FormData();
  formData.append('id', id);

  const res = await fetch('./admin/update/index.php', {
    method: 'POST',
    body: formData
  });

  const data = await res.json();

  updateTodoView(data.id, data.complete);
}

document.addEventListener('click', (e) => {
  if (e.target.classList.contains('js-complete-todo')) {
    const todo = e.target.closest('[data-id]');
    updateTodo(todo.dataset.id);
  }
});

