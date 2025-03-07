<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "csy338";
$dbname = "rental_service";

// Подключение к базе данных
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Установка кодировки UTF-8
$conn->set_charset("utf8mb4");

// Создание таблицы, если её нет
$conn->query("CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    last_name VARCHAR(50) NOT NULL,
    passport_series VARCHAR(10) NOT NULL,
    passport_number VARCHAR(20) NOT NULL,
    item_name VARCHAR(100) NOT NULL,
    rental_date DATE NOT NULL,
    return_date DATE NOT NULL,
    rental_price DECIMAL(10,2) NOT NULL
)");

// Добавление записи в базу данных, если отправлена форма
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $last_name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');

    $_SESSION['email'] = $email;
    $_SESSION['message'] = $message;

    // Сохранение данных в файл
    file_put_contents("bio.txt", "Имя: $last_name\nСообщение: $message");

   
    $stmt = $conn->prepare("INSERT INTO clients (last_name, passport_series, passport_number, item_name, rental_date, return_date, rental_price) 
                            VALUES (?, 'AB', '123456', 'Телевизор', '2025-03-01', '2025-03-05', 500.00)");
    $stmt->bind_param("s", $last_name);
    $stmt->execute();
    
    echo "<p class='success'>Данные успешно отправлены и добавлены в базу!</p>";
}

$conn->query("DELETE FROM clients WHERE item_name = 'ХОЛОДИЛЬНИК'");


$result = $conn->query("SELECT * FROM clients");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 350px;
            text-align: center;
            margin-bottom: 20px;
        }
        h2 {
            margin-bottom: 15px;
            color: #333;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #6a11cb;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background: #2575fc;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .table-container {
            width: 70%;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #6a11cb;
            color: white;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Свяжитесь с нами</h2>
        <form method="POST" action="">
            <input type="text" name="name" placeholder="Введите ваше имя" required>
            <input type="email" name="email" placeholder="Введите ваш Email" required>
            <textarea name="message" placeholder="Введите ваше сообщение" required></textarea>
            <button type="submit">Отправить</button>
        </form>
    </div>

    <div class="table-container">
        <h2>Содержимое БД после удаления</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Фамилия</th>
                <th>Серия паспорта</th>
                <th>Номер паспорта</th>
                <th>Наименование</th>
                <th>Дата выдачи</th>
                <th>Дата возврата</th>
                <th>Цена</th>
            </tr>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['passport_series']) ?></td>
                        <td><?= htmlspecialchars($row['passport_number']) ?></td>
                        <td><?= htmlspecialchars($row['item_name']) ?></td>
                        <td><?= htmlspecialchars($row['rental_date']) ?></td>
                        <td><?= htmlspecialchars($row['return_date']) ?></td>
                        <td><?= htmlspecialchars($row['rental_price']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8">Нет записей в базе данных</td></tr>
            <?php endif; ?>
        </table>
    </div>

</body>
</html>

<?php
$conn->close();
?>
