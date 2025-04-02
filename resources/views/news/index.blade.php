<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Upload Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        h1 {
            color: #333;
        }
        .author-container {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .author-fields {
            margin-bottom: 10px;
        }
        .author-actions {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>News Upload Form</h1>

    <form action="http://127.0.0.1:8000/api/news" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div class="form-group">
            <label>Authors:</label>
            <div id="authors-container">
                <div class="author-container">
                    <div class="author-fields">
                        <input type="text" name="author[]" placeholder="Author name" required>
                    </div>
                </div>
            </div>
            <button type="button" onclick="addAuthor()">Add Another Author</button>
        </div>

        <div class="form-group">
            <label for="poster">Poster Image (optional):</label>
            <input type="file" id="poster" name="poster" accept="image/jpeg,image/png,image/jpg">
        </div>

        <div class="form-group">
            <label for="cover">Cover Image (required):</label>
            <input type="file" id="cover" name="cover" accept="image/jpeg,image/png,image/jpg" required>
        </div>

        <div class="form-group">
            <label for="link">Link:</label>
            <input type="text" id="link" name="link" required>
        </div>

        <div class="form-group">
            <label for="description">Description (optional):</label>
            <textarea id="description" name="description" rows="4"></textarea>
        </div>

        <div class="form-group">
            <button type="submit">Submit News</button>
        </div>
    </form>

    <script>
        function addAuthor() {
            const container = document.getElementById('authors-container');
            const newAuthorDiv = document.createElement('div');
            newAuthorDiv.className = 'author-container';

            newAuthorDiv.innerHTML = `
                <div class="author-fields">
                    <input type="text" name="author[]" placeholder="Author name" required>
                </div>
                <div class="author-actions">
                    <button type="button" onclick="removeAuthor(this)">Remove</button>
                </div>
            `;

            container.appendChild(newAuthorDiv);
        }

        function removeAuthor(button) {
            const authorDiv = button.closest('.author-container');
            authorDiv.remove();
        }
    </script>
</body>
</html>
