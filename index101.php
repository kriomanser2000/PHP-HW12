<?php
session_start();
if (!isset($_COOKIE['page1_visits']))
{
    setcookie('page1_visits', 0, time() + 3600);
}
if (!isset($_COOKIE['page2_visits']))
{
    setcookie('page2_visits', 0, time() + 3600);
}
$page1_visits = isset($_COOKIE['page1_visits']) ? $_COOKIE['page1_visits'] : 0;
$page2_visits = isset($_COOKIE['page2_visits']) ? $_COOKIE['page2_visits'] : 0;
$folder_selected = isset($_COOKIE['selected_folder']) ? $_COOKIE['selected_folder'] : null;
if (isset($_POST['download']))
{
    $folder_name = $folder_selected;
    if ($folder_name && is_dir($folder_name))
    {
        $zip = new ZipArchive();
        $zip_file = $folder_name . ".zip";
        if ($zip->open($zip_file, ZipArchive::CREATE) === TRUE)
        {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($folder_name),
                RecursiveIteratorIterator::LEAVES_ONLY
            );
            foreach ($files as $name => $file)
            {
                if (!$file->isDir())
                {
                    $file_path = $file->getRealPath();
                    $relative_path = substr($file_path, strlen($folder_name) + 1);
                    $zip->addFile($file_path, $relative_path);
                }
            }
            $zip->close();
            header('Content-Type: application/zip');
            header('Content-disposition: attachment; filename=' . $zip_file);
            header('Content-Length: ' . filesize($zip_file));
            readfile($zip_file);
            unlink($zip_file);
            exit;
        }
        else
        {
            error_log("Archive failed: $zip_file", 3, "CrashLogUser.txt");
        }
    }
    elseif ($folder_name)
    {
        error_log("Folder isn’t created: $folder_name", 3, "CrashLogUser.txt");
    }
    else
    {
        error_log("Download failed", 3, "CrashLogUser.txt");
    }
}
function logError($message)
{
    $date = date("Y-m-d H:i:s");
    $log_message = "$date — $message\n";
    $log_file = "CrashLogUser.txt";
    file_put_contents($log_file, $log_message, FILE_APPEND);
}
set_error_handler(function($errno, $errstr)
{
    logError($errstr);
});
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<h2>Кількість переходів: </h2>
<p>На 1.php: <?= $page1_visits ?></p>
<p>На 2.php: <?= $page2_visits ?></p>
<a href="1.php">Перейти на 1.php</a><br>
<a href="2.php">Перейти на 2.php</a><br>
<form method="post" action="">
    <input type="text" name="folder" placeholder="Введіть назву папки">
    <button type="submit">Select Folder</button>
</form>
<form method="post" action="">
    <button type="submit" name="go_to_folder" <?= !$folder_selected ? 'disabled' : '' ?>>GoToFolder</button>
</form>
<form method="post" action="">
    <button type="submit" name="download">Download</button>
</form>
<?php
if (isset($_POST['folder']))
{
    $selected_folder = $_POST['folder'];
    setcookie('selected_folder', $selected_folder, time() + 3600);
    header("Location: index.php");
    exit;
}
if (isset($_POST['go_to_folder']) && $folder_selected)
{
    if (is_dir($folder_selected))
    {
        echo "<h2>Вміст папки $folder_selected:</h2>";
        $files = scandir($folder_selected);
        foreach ($files as $file)
        {
            if ($file !== '.' && $file !== '..')
            {
                echo "<p>$file</p>";
            }
        }
    }
    else
    {
        echo "<p>Папка не знайдена</p>";
    }
}
if ($folder_selected)
{
    echo '<form method="post" action="">
            <button type="submit" name="go_back">GoBack</button>
          </form>';
}
if (isset($_POST['go_back']))
{
    setcookie('selected_folder', '', time() - 3600);
    header("Location: index.php");
    exit;
}
?>
</body>
</html>