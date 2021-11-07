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
    <p style="border-bottom: 1px solid #ccc; font-weight:bold;">ToDo</p>
    <button onclick="location.href='new.php'" style="display:block;">新規登録<br></button>
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
                if (data === true) {
                    let div_id = "todo-item" + todo_id;
                    $('#' + div_id + ' p').text("完了");
                } else {
                    alert(data);
                }

            }).fail(function(XMLHttpRequest, status, e) {
                alert(e);
            });
    });
</script>

</html>