<?php
require(dirname(__FILE__) . '/../../controllers/TodoController.php');
list($todos, $pagenation_items) = TodoController::index();
$page = $pagenation_items['page'];
$max_page = $pagenation_items['max_page'];
$from_record = $pagenation_items['from_record'];
$to_record = $pagenation_items['to_record'];
$count = $pagenation_items['count'];

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todos</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link rel="stylesheet" href="../../css/styles.css">
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
            <input type="radio" name="sort" value="created_at,asc">
            <label for="sort">作成日昇順</label>
            <input type="radio" name="sort" value="created_at,desc">
            <label for="sort">作成日降順</label>
            <input type="radio" name="sort" value="title,asc">
            <label for="sort">タイトル昇順</label>
            <input type="radio" name="sort" value="title,desc">
            <label for="sort">タイトル降順</label>
        </form>

        <div style="display:flex; height:30px">
            <input type="button" id="create-csv" value="CSV作成">
            <div id="csv-output-area" style="display:none; align-items:center">
                <p id="filename"></p>
                <p id="csv_created"></p>
                <input type="button" id="btn_download" value="CSV出力">
            </div>
        </div>

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
    <p class="from_to"><?php echo $count; ?>件中 <?php echo $from_record; ?> - <?php echo $to_record; ?> 件目を表示</p>
    <div class="pagination">
        <?php if ($page >= 2) : ?>
            <a href="index.php?page=<?php echo ($page - 1); ?>" class="page_feed">&laquo;</a>
        <?php else :; ?>
            <span class="first_last_page">&laquo;</span>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $max_page; $i++) : ?>
            <?php if ($i == $page) : ?>
                <span class="now_page_number"><?php echo $i; ?></span>
            <?php else : ?>
                <a href="?page=<?php echo $i; ?>" class="page_number"><?php echo $i; ?></a>
            <?php endif; ?>
        <?php endfor; ?>
        <?php if ($page < $max_page) : ?>
            <a href="index.php?page=<?php echo ($page + 1); ?>" class="page_feed">&raquo;</a>
        <?php else : ?>
            <span class="first_last_page">&raquo;</span>
        <?php endif; ?>
    </div>
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

    $(document).on('click', '#create-csv', function() {

        // 処理が終わるまでボタンを非活性にする
        $('#create-csv').prop("disabled", true);

        let status = $('input[name=status]:checked').val();
        let search_word = $.trim($('#search-word').val());
        let sort = $('input[name=sort]:checked').val();

        // createCsv.phpファイルへのアクセス
        $.ajax({
                type: "POST",
                url: "../api/createCsv.php",
                data: {
                    status: status,
                    search_word: search_word,
                    sort: sort
                },
                dataType: 'json'
            })
            // 成功
            .done(function(data) {
                console.log(data);
                if (data['result'] === "sccess") {
                    // ダウンロードエリア表示する
                    $('#csv-output-area').css('display', 'flex');
                    $('#filename').html(data['filename']);
                    $('#csv_created').html(data['created_at']);
                    $('#btn_download').attr("onclick", `location.href='../../var/tmp/${data['filename']}'`)
                } else {
                    alert(data['msg']);
                }

            }).fail(function(XMLHttpRequest, status, e) {
                alert(e);
            }).always(() => {

                // ボタンを活性に戻す
                $('#create-csv').prop("disabled", false);
            });



    });
</script>

</html>