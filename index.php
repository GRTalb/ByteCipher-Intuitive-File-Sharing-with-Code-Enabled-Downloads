<?php
function generateCode($length = 6) {
    return strtoupper(substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, $length));
}

$uploadDirectory = 'uploads/';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $code = generateCode();
    $filename = $uploadDirectory . $code . '_' . basename($_FILES['file']['name']);
    
    if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    move_uploaded_file($_FILES['file']['tmp_name'], $filename);

    echo "Your file has been uploaded. Your code is: $code";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download_code'])) {
    $download_code = $_POST['download_code'];
    $file_to_download = glob($uploadDirectory . $download_code . '_*')[0] ?? null;

    if ($file_to_download !== null) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_to_download) . '"');
        readfile($file_to_download);
        exit();
    } else {
        echo "Invalid code. Please check the code and try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload and Download</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 50px;
        }

        .container {
            max-width: 400px;
            margin: auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        input[type="text"],
        input[type="file"],
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 16px;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        #progress-bar {
            width: 100%;
            height: 20px;
            background-color: #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
            overflow: hidden;
        }

        #upload-progress {
            height: 100%;
            width: 0;
            background-color: #4caf50;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>File Upload and Download</h2>

        <form id="upload-form" enctype="multipart/form-data">
            <input type="file" name="file" id="file" required>
            <div id="progress-bar">
                <div id="upload-progress"></div>
            </div>
            <input type="submit" value="Upload">
        </form>

        <hr>

        <form action="" method="post">
            <input type="text" name="download_code" placeholder="Enter Code" required>
            <input type="submit" value="Download">
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#upload-form").submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: 'index.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function() {
                        var xhr = new XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function(event) {
                            if (event.lengthComputable) {
                                var percent = (event.loaded / event.total) * 100;
                                $('#upload-progress').css('width', percent + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    success: function(response) {
                        alert(response);
                        $('#upload-progress').css('width', '0%');
                    }
                });
            });
        });
    </script>
</body>
</html>
