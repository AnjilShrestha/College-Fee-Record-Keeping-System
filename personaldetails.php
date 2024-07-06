<?php
require_once 'private/autoload.php';
$in=$_SESSION['id'];
$_SESSION['url']=$_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Personal Details</title>
    <link rel="stylesheet" href="css/table.css">
    <style>
        table {
            width: 50%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        th:first-child,
        td:first-child {
            text-transform: uppercase;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        td {
            border-bottom: 1px solid #ddd;
        }
        tr:first-child {
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
    <?php include_once 'studentmenu.php';
    ?>
    <div class='container'>
        <div class='header'>
            <div class='header-right'>
                <div class='detail'>Personal Information </div>
            </div>
        </div>
        <div class='content'>
            <?php getmessage();?>
            <div class='content-center'>
               <table>
                <tr>
                    <th colspan='2'>Personal Information</th>
                </tr>
                <?php 
                $sql="SELECT * FROM student_tb AS s
                INNER JOIN batch_tb AS b ON b.batch_id = s.batch_id
                INNER JOIN course_tb AS c ON c.course_id=b.course_id
                WHERE s.student_id = $in";
                $result = $connection->query($sql);
                while($row=$result->fetch_assoc()){?>
                <tr>
                    <th>Name</th>
                    <td><?php echo $row['name'];?></td>
                </tr>
                <tr>
                    <th>Course</th>
                    <td><?php echo $row['course_name'];?></td>
                </tr>
                <tr>
                    <th>Batch</th>
                    <td><?php echo $row['batch_name'];?></td>
                </tr>
                <tr>
                    <th>Roll no:</th>
                    <td><?php echo $row['roll_no'];?></td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td><?php echo $row['phone_number'];?></td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td><?php echo $row['address'];?></td>
                </tr>
                <tr>
                    <th>Gender</th>
                    <td><?php echo $row['gender'];?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo $row['email'];?></td>
                    <?php }?>
                </tr>
                <tr>
                    <th colspan="2"><a href="editpersonaldetails.php"><button class='btn-edit'>Edit</button></a></th>
                </tr>
               </table>
            </div>
        </div>
    </div>       
</body>
</html>
