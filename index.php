<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List editable</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <label for="content">Nueva tarea</label>
    <input type="text" id="content" placeholder="Ingresa una tarea">
    <button id="guardar">Guardar</button>
    
    <?php
        require "DB.php";
        require "todo.php";
        try {
            $db = new DB;
            echo "<h2>TODO</h2>";
            echo "<ul id=\"lista\">";
            $todo_list = Todo::DB_selectAll($db->connection);
            foreach ($todo_list as $row) {
                echo "<li id=\"item-{$row->getItem_id()}\">";
                echo "{$row->getItem_id()}. {$row->getContent()} ";
                echo "<div class='button-group'>";
                echo "<button class='edit' onclick='editar({$row->getItem_id()}, \"{$row->getContent()}\")'>Editar</button>";
                echo "<button class='delete' onclick='borrar({$row->getItem_id()})'>X</button>";
                echo "</div>";
                echo "</li>";
            }
            echo "</ul>";
        } 
        catch (PDOException $e) {
            echo "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    ?>
    
    <script>
        function llamada_a_controller(metodo, postData) {
            const url = 'http://desarrollo.cierva/lista/controller.php';
            const lista = document.getElementById('lista');
            lista.innerHTML = ''; // Limpiar la lista para actualizarla

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
                    li.id = "item-" + item.item_id;
                    li.appendChild(document.createTextNode(item.item_id + ". " + item.content + " "));

                    var buttonGroup = document.createElement("div");
                    buttonGroup.className = "button-group";

                    var editButton = document.createElement("button");
                    editButton.className = "edit";
                    editButton.textContent = "Editar";
                    editButton.setAttribute("onclick", "editar(" + item.item_id + ", '" + item.content + "')");
                    buttonGroup.appendChild(editButton);

                    var deleteButton = document.createElement("button");
                    deleteButton.className = "delete";
                    deleteButton.textContent = "X";
                    deleteButton.setAttribute("onclick", "borrar(" + item.item_id + ")");
                    buttonGroup.appendChild(deleteButton);

                    li.appendChild(buttonGroup);
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
                return; // Si el usuario cancela o deja el campo vacío, no hacer nada
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
            document.getElementById('content').value = ""; // Limpiar el campo de entrada
        });
    </script>
</body>
</html>
