<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TeamUsUp - Notifications</title>
    <link rel="stylesheet" href="/css/notificationStudent.css"> 
    
</head>  
<?php include "sidebar.php"?>
<body>
    <div class="container">
        <h2>Notifications For you</h2>
        <div class="noti1"> 
            <?php 
                if (isset($_GET["pageNo"]))
                {
                    $selectedPage = $_GET["pageNo"];
                    if ($selectedPage>1)
                    {
                        //echo '.  Im here 1  .';
                        notificationsToUser($_SESSION['userID'], $_GET["pageNo"]);    
                    }
                    else
                    {
                        //echo '.  Im here 2  .';
                        notificationsToUser($_SESSION['userID'], 1);    
                    } 
                } 
                else
                {
                    //echo '.  Im here 3  .';
                    notificationsToUser($_SESSION['userID'], 1);    
                }  
        ?>
        </div> 
    </div> 

    <div class="container">
        <h2>Notifications By you</h2>
        <div class="noti2"> 
            <?php 
                if (isset($_GET["pageNoBy"]))
                {
                    $selectedPage = $_GET["pageNoBy"];
                    if ($selectedPage>1)
                    {
                        //echo '.  Im here 1  .';
                        notificationsByUser($_SESSION['userID'], $_GET["pageNoBy"]);    
                    }
                    else
                    {
                        //echo '.  Im here 2  .';
                        notificationsByUser($_SESSION['userID'], 1);    
                    } 
                } 
                else
                {
                    //echo '.  Im here 3  .';
                    notificationsByUser($_SESSION['userID'], 1);    
                }  
        ?>
        </div> 
    </div> 
</body>
</html>

<?php
    //$_SESSION['userID']
    function notificationsToUser($userID, $pageNo)
    {
        include "../conn.php";
        $limit = 5;
        $offset = ($pageNo * $limit) - $limit;
        
        //echo $pageNo . ',  '. $offset;

        $sql = "SELECT A.AccountID, A.FirstName AS ReqfName, A.LastName AS ReqlName, N.NotID, N.Action, N.Message, N.SubjectID
                FROM teamusup.notification N INNER JOIN teamusup.account A ON N.RequesterID=A.AccountID 
                WHERE N.RecipientID='". $userID ."' ORDER BY N.NotID DESC
                LIMIT $offset, $limit";  
        //echo $sql;
        //$result = mysql_query($query);
        $result = $conn->query($sql);
        //echo "Query has been executed";   
        echo '<table class="noti-table">
            <thead>
                <tr>
                    <th>From</th>
                    <th>Action</th>
                    <th>Message</th> 
                    <th>Subject</th>
                </tr>
            </thead>
            <tbody>'; 
        if($result->num_rows > 0)
        { 
            while($row = $result->fetch_array(MYSQLI_ASSOC))
            {    
                echo '  <tr>
                        <td class="name">' . $row['ReqfName'] . ' ' . $row['ReqlName'] . '</td>
                        <td>' . $row['Action'] . '</td>
                        <td>' . $row['Message'] . '</td>';
                if ($row['SubjectID'] !== null)
                {
                    getSubjectInfo($row['NotID'], $row['Action'], $row['SubjectID']);
                }
                else
                {
                    echo '<td></td>';
                }
                echo '</tr>' ;
            }  
            echo '</tbody></table>';
        }
        else
        { 
            echo '</tbody></table>';
            echo '<p>No Notifications For You available.</p>';
        }  
        $conn->close(); //Make sure to close out the database connection

        pagination($userID, $limit, $pageNo);
    }

    function pagination($userID, $limit, $currPageNo)
    {
        $maxPages = 3;
        $adjacentPages = 1;
        include "../conn.php";
        $sql = "SELECT
                COUNT(NotID) AS NoOfResults
                FROM teamusup.notification N INNER JOIN teamusup.account A ON N.RequesterID=A.AccountID 
                WHERE N.RecipientID='". $userID ."'";
        
        $result = $conn->query($sql);
        $noOfRecords = $result->fetch_array(MYSQLI_ASSOC);
        echo '<div class="paging">'; 
        if ($noOfRecords['NoOfResults'] > 0)
        {
            $noOfPage = ceil($noOfRecords['NoOfResults'] / $limit); 
            //echo 'im heeeeeeeeeeeerrrrrrrrrrrrrrrrreeeeeeeeeeeeeeeeeeeeeeee ' . $noOfPage;
            if ($noOfPage == 1)
            {
                echo "<a href='notificationStudent.php?pageNoBy=1&pageNo=1' class='num on'>1</a>";
            }
            else if($noOfPage > 1)
            { 
                if($noOfPage > $maxPages)
                {
                    if($currPageNo != 1)
                    { 
                        echo"<a href='notificationStudent.php?pageNoBy=1&pageNo=1' class='btn'><<</a>";
                        echo"<a href='notificationStudent.php?pageNoBy=1&pageNo=" . $currPageNo-1 . "' class='btn'>Prev</a>";
                    } 
                    for($counter = $currPageNo-$adjacentPages+1; $counter < $currPageNo+$adjacentPages; $counter++)
                    {

                        echo "<a href='notificationStudent.php?pageNoBy=1&pageNo=$counter' class='num'>$counter</a>";
                    }
                    if($currPageNo != $noOfPage)
                    { 
                        echo"<a href='notificationStudent.php?pageNoBy=1&pageNo=" . $currPageNo+1 . "' class='btn'>Next</a>"; 
                        echo"<a href='notificationStudent.php?pageNoBy=1&pageNo=$noOfPage' class='btn'>>></a>";
                    }

                }
                else
                {
                    for($counter = 1; $counter < $noOfPage + 1; $counter++)
                    {
                        echo "<a href='notificationStudent.php?pageNoBy=1&pageNo=$counter' class='num'>$counter</a>";
                    }
                } 
            } 
        }      
        echo '</div>'; 
        $conn->close(); //Make sure to close out the database connection
    }

    function notificationsByUser($userID, $pageNo)
    {
        include "../conn.php";
        $limit = 5;
        $offset = ($pageNo * $limit) - $limit;
        
        //echo $pageNo . ',  '. $offset;

        $sql = "SELECT A.AccountID, A.FirstName AS ReqfName, A.LastName AS ReqlName, N.NotID, N.Action, N.Message, N.SubjectID
                FROM teamusup.notification N INNER JOIN teamusup.account A ON N.RecipientID=A.AccountID 
                WHERE N.RequesterID='". $userID ."' ORDER BY N.NotID DESC
                LIMIT $offset, $limit";  
        //echo $sql;
        //$result = mysql_query($query);
        $result = $conn->query($sql);
        //echo "Query has been executed";   
        echo '<table class="noti-table">
            <thead>
                <tr>
                    <th>To</th>
                    <th>Action</th>
                    <th>Message</th> 
                    <th>Subject</th>
                </tr>
            </thead>
            <tbody>'; 
        if($result->num_rows > 0)
        { 
            //echo "Im here 1"; 
            while($row = $result->fetch_array(MYSQLI_ASSOC))
            {    
                echo '  <tr>
                        <td class="name">' . $row['ReqfName'] . ' ' . $row['ReqlName'] . '</td>
                        <td>' . $row['Action'] . '</td>
                        <td>' . $row['Message'] . '</td>';
                if ($row['SubjectID'] !== null)
                {
                    getSubjectInfo($row['NotID'], $row['Action'], $row['SubjectID']);
                }
                else
                {
                    echo '<td></td>';
                }
                echo '</tr>' ;
            }  
            echo '</tbody></table>';
        }
        else
        { 
            echo '</tbody></table>';
            echo '<p>No Notifications By You available.</p>';
        }  
        $conn->close(); //Make sure to close out the database connection

        paginationBy($userID, $limit, $pageNo);
    }

    function paginationBy($userID, $limit, $currPageNo)
    {
        $maxPages = 3;
        $adjacentPages = 1;
        include "../conn.php";
        $sql = "SELECT
                COUNT(NotID) AS NoOfResults
                FROM teamusup.notification N INNER JOIN teamusup.account A ON N.RecipientID=A.AccountID 
                WHERE N.RequesterID='". $userID ."'";
        
        $result = $conn->query($sql);
        $noOfRecords = $result->fetch_array(MYSQLI_ASSOC);
        echo '<div class="paging">'; 
        if ($noOfRecords['NoOfResults'] > 0)
        {
            $noOfPage = ceil($noOfRecords['NoOfResults'] / $limit); 
            //echo 'im heeeeeeeeeeeerrrrrrrrrrrrrrrrreeeeeeeeeeeeeeeeeeeeeeee ' . $noOfPage;
            if ($noOfPage == 1)
            {
                echo "<a href='notificationStudent.php?pageNo=1&pageNoBy=1' class='num on'>1</a>";
            }
            else if($noOfPage > 1)
            { 
                if($noOfPage > $maxPages)
                {
                    if($currPageNo != 1)
                    { 
                        echo"<a href='notificationStudent.php?pageNo=1&pageNoBy=1' class='btn'><<</a>";
                        echo"<a href='notificationStudent.php?pageNo=1&pageNoBy=" . $currPageNo-1 . "' class='btn'>Prev</a>";
                    } 
                    for($counter = $currPageNo-$adjacentPages+1; $counter < $currPageNo+$adjacentPages; $counter++)
                    {

                        echo "<a href='notificationStudent.php?pageNo=1&pageNoBy=$counter' class='num'>$counter</a>";
                    }
                    if($currPageNo != $noOfPage)
                    { 
                        echo"<a href='notificationStudent.php?pageNo=1&pageNoBy=" . $currPageNo+1 . "' class='btn'>Next</a>"; 
                        echo"<a href='notificationStudent.php?pageNo=1&pageNoBy=$noOfPage' class='btn'>>></a>";
                    }

                }
                else
                {
                    for($counter = 1; $counter < $noOfPage + 1; $counter++)
                    {
                        echo "<a href='notificationStudent.php?pageNo=1&pageNoBy=$counter' class='num'>$counter</a>";
                    }
                } 
            } 
        }      
        echo '</div>'; 
        $conn->close(); //Make sure to close out the database connection
    }

    function getSubjectInfo($notID, $action, $subjID)
    {
        if(str_contains($action, 'Review'))
        {
            getRevInfo($notID, $subjID);
        }
        else if(str_contains($action, 'Group'))
        {
            getGroupInfo($notID, $subjID);
        } 
    }

    function getRevInfo($notID, $revID)
    {
        include "../conn.php"; 
        $sql = "SELECT concat_ws(' ', U.UnitID, U.Name) AS UnitName, A.Name AS AssName, G.Name AS GroupName, R.Comment
                FROM teamusup.notification N INNER JOIN (review R INNER JOIN (teamusup.group G INNER JOIN (assignment A INNER JOIN (offering O INNER JOIN unit U ON O.UnitID=U.UnitID)
                ON A.OffID=O.OffID)
                ON G.AssID=A.AssID)
                ON R.GroupID=G.GroupID) ON N.SubjectID=R.RevID 
                WHERE N.SubjectID='$revID' AND N.NotID='$notID'";  
         
        $result = $conn->query($sql); 
        if($result->num_rows == 1)
        {  
            $row = $result->fetch_array(MYSQLI_ASSOC);
            echo '  <td>'.$row['UnitName']. '</br>'.
                    $row['AssName']. '</br>'.
                    $row['GroupName']. '</br>'.
                    $row['Comment']. '</br>'. 
                    '</td>';
        }
        else
        {  
            echo '<td></td>';
        }  
        $conn->close(); //Make sure to close out the database connection 
    }

    function getGroupInfo($notID, $subjID)
    {
        include "../conn.php"; 
        $sql = "SELECT concat_ws(' ', U.UnitID, U.Name) AS UnitName, A.Name AS AssName, G.Name AS GroupName
                FROM teamusup.notification N INNER JOIN (teamusup.group G INNER JOIN (assignment A INNER JOIN (offering O INNER JOIN unit U 
                ON O.UnitID=U.UnitID) ON A.OffID=O.OffID) ON G.AssID=A.AssID) ON N.SubjectID=G.GroupID
                WHERE N.SubjectID='10' AND N.NotID='20'";  
         
        $result = $conn->query($sql); 
        if($result->num_rows == 1)
        {  
            $row = $result->fetch_array(MYSQLI_ASSOC);
            echo '  <td>'.$row['UnitName']. '</br>'.
                    $row['AssName']. '</br>'.
                    $row['GroupName']. '</br>'. 
                    '</td>'; 
        }
        else
        {   
            echo '<td></td>';         
        }  
        $conn->close(); //Make sure to close out the database connection 
    }
?>