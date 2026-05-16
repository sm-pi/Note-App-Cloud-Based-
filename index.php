<?php

$students = [
    ["id" => 1, "name" => "Alice", "grade" => "A"],
    ["id" => 2, "name" => "Bob", "grade" => "B"],
    ["id" => 3, "name" => "Charlie", "grade" => "A+"]
];

?>

<!DOCTYPE html>
<html>
<head>
    <title>PHP List and Table Example</title>
    <style>
        table {
            border-collapse: collapse;
            width: 50%;
        }

        table, th, td {
            border: 1px solid black;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h2>Student List</h2>
<ul>
    <?php foreach ($students as $student): ?>
        <li>
            <?php echo $student['name']; ?> - Grade: <?php echo $student['grade']; ?>
        </li>
    <?php endforeach; ?>
</ul>

<h2>Student Table</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Grade</th>
    </tr>

    <?php foreach ($students as $student): ?>
    <tr>
        <td><?php echo $student['id']; ?></td>
        <td><?php echo $student['name']; ?></td>
        <td><?php echo $student['grade']; ?></td>
    </tr>
    <?php endforeach; ?>

</table>

</body>
</html>
