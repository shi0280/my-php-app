<?php
require(dirname(__FILE__) . '/../../controllers/TodoController.php');
$todos = TodoController::index();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todos</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>

<body>
    <header>
        <p style="border-bottom: 1px solid #ccc; font-weight:bold;">ToDo</p>
        <button onclick="location.href='new.php'" style="display:block;">新規登録<br></button>
        <form action="index.php" method="get">
            <input type="radio" id="uncompleted" name="status" value="0">
            <label for="uncompleted">未完了</label>
            <input type="radio" id="completed" name="status" value="1">
            <label for="completed">完了</label>
            <input type="text" id="search-word" name="search-word">
            <label for="search-word">検索ワード</label>
            <input type="submit" name="search-submit" value="検索">
        </form>
    </header>
    <?php
    foreach ($todos as $todo) {
        echo '<div id=todo-item' . $todo['id'] . ' style="background-color:#f0f0f0; padding:10px; margin:10px"><a href=' . 'detail.php?todo_id=' . $todo['id'] . '> todo: ' . $todo['title'] . '</a>';
        switch ($todo['status']) {
            case 0:
                echo '<p class="status_str" style="margin:0"><label><input type="checkbox" name="status" value="1" data-todo_id="' . $todo['id'] . '">完了</label></p>';
                // echo 'status: 未完了';
                break;
            case 1:
                echo '<p class="status_str" style="margin:0"> 完了</p>';
                break;
        }
        echo 'deadline_at: ' . $todo['deadline_at'];
        echo '<button class=delete_todo' . ' data-todo_id=' . $todo['id'] . '>削除</button>';
        echo '<br></div>';
    }
    ?>
</body>
<script>
    $(document).on('click', 'input:checkbox[name="status"]', function() {

        let todo_id = $(this).data('todo_id');
        let status = $(this).val();

        // updateStatus.phpファイルへのアクセス
        $.ajax({
                type: "POST",
                url: "../api/updateStatus.php",
                data: {
                    todo_id: $(this).data('todo_id'),
                    status: $(this).val()
                },
                dataType: 'json'
            })
            // 成功
            .done(function(data) {
                console.log(data);
                if (data['result'] === "sccess") {
                    let div_id = "todo-item" + todo_id;
                    $('#' + div_id + ' p').text("完了");
                } else {
                    alert(data['msg']);
                }

            }).fail(function(XMLHttpRequest, status, e) {
                alert(e);
            });
    });

    $(document).on('click', '.delete_todo', function() {

        let todo_id = $(this).data('todo_id');
        alert(todo_id);

        // updateStatus.phpファイルへのアクセス
        $.ajax({
                type: "POST",
                url: "../api/deleteTodo.php",
                data: {
                    todo_id: $(this).data('todo_id'),
                    status: $(this).val()
                },
                dataType: 'json'
            })
            // 成功
            .done(function(data) {
                console.log(data);
                if (data['result'] === "sccess") {
                    let div_id = "todo-item" + todo_id;
                    $('#' + div_id).remove();
                } else {
                    alert(data['msg']);
                }

            }).fail(function(XMLHttpRequest, status, e) {
                alert(e);
            });

    });
</script>

</html>