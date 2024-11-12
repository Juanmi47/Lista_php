```markdown
# 1.0 EDITAR EN LA LISTA TODO:

### //todo.php//
```php
public function DB_update($dbconn) {
    $sql = "UPDATE todo_list SET content = ? WHERE item_id = ?";
    $stmt = $dbconn->prepare($sql);
    $stmt->execute([$this->content, $this->item_id]);
}
```

### //controller.php//
```php
case 'PUT':
    $db = new DB();
    $update_todo = new Todo;
    $update_todo->jsonConstruct($bodyRequest);
    $update_todo->DB_update($db->connection);
    $todo_list = Todo::DB_selectAll($db->connection);
    return_response(200, "OK", $todo_list);
    break;
```

### //index.php//
```html
<script>
    function llamada_a_controller(metodo, postData) {
        const url = 'http://desarrollo.cierva/lista/controller.php';
        const lista = document.getElementById('lista');
        lista.innerHTML = ''; // Eliminar el contenido previo de la tabla

        fetch(url, {
            method: metodo,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(postData)
        })
        .then(response => response.json())
        .then(data => {
            data.forEach(item => {
                var li = document.createElement("li");
                li.appendChild(document.createTextNode(item.item_id + ". " + item.content + " "));

                var editButton = document.createElement("button");
                editButton.textContent = "Editar";
                editButton.setAttribute("onclick", "editar(" + item.item_id + ",'" + item.content + "')");
                li.appendChild(editButton);

                var deleteButton = document.createElement("button");
                deleteButton.textContent = "X";
                deleteButton.setAttribute("onclick", "borrar(" + item.item_id + ")");
                li.appendChild(deleteButton);

                lista.appendChild(li);
            });
        })
        .catch(error => console.error('Error en la solicitud:', error));
    }

    function borrar(item_id) {
        const postData = { item_id: item_id };
        llamada_a_controller("DELETE", postData);
    }

    function editar(item_id, content) {
        const nuevoContenido = prompt("Edita la tarea:", content);
        if (nuevoContenido === null || nuevoContenido.trim() === "") {
            return; // Si el usuario cancela o deja vacío, no hacer nada
        }

        const postData = {
            item_id: item_id,
            content: nuevoContenido
        };
        llamada_a_controller("PUT", postData);
    }

    document.getElementById('guardar').addEventListener('click', function () {
        const contenido = document.getElementById('content').value;
        if (!contenido) {
            alert('Por favor, introduce un valor.');
            return;
        }

        const postData = {
            content: contenido
        };
        llamada_a_controller("POST", postData);
    });
</script>
```
``` 
