<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <style type="text/css">
    * {
    color: #abb2bf;
    font-family: Consolas, "Courier New", Courier, Monaco, monospace;
    font-size: 14px;
    line-height: 1.2;
    }
    body {
        padding: 10px;
        background-color: #282c34;
        color: #abb2bf;
    }
    textarea {
        width: 100%;
        height: 70vh;
        border: solid 1px #666;
        outline: none;
        background-color: transparent;
    }
    </style>

</head>
<body>
    <h1>編集：<?php echo $path;?></h1>
    <form action="?update" method="POST">
        <textarea name="data" autofocus><?php echo $data;?></textarea>
        <input type="hidden" name="path" value="<?php echo $path;?>">
        <br>
        <lavel for="touch"><input type="checkbox" name="touch" id="touch" value="1"> 最終更新日時を更新しない</lavel>
        <input type="submit" value="保存">
    </form>
</body>
</html>

