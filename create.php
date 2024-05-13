<?php
session_start();
require_once "db.php";

$loggedIn = isset($_SESSION['username']);
$user = "";

if ($loggedIn) {
    // Get user details
    $username = $_SESSION['username'];
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);
    $user = $result->fetch(PDO::FETCH_ASSOC);

    // Get user's novels
    $novels = [];
    $sql = "SELECT * FROM stories WHERE username='$username'";
    $result = $conn->query($sql);
    $novelCount = 0;
    $firstNovel = '';
    if ($result->rowCount() > 0) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $novels[] = $row;
            $novelCount++;
            $firstNovel = $row;
        }
    }
}
else{
    header("Location:login.html");
}

// Logout logic
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

//header('Content-Type: application/json');



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Novel</title>
    <link rel="stylesheet" href="create.css">
</head>
<body>
<div class="navbar">
    <div class="navbar-left">
        <a href="index.php" class="logo-container">
            <a href="index.php"><img src="profile_pictures/logo.png" alt="" style="width: 60px; height: 60px"></a>
        </a>
    </div>
    <div class="site-name">Writer's Nest</div>
    <div class="navbar-right">
        <ul class="nav-links">
            <li><a href="about_us.php">About Us</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="?logout=true">Log Out</a></li>
        </ul>
    </div>
</div>
<div class="container">
    <div class="novel-list">
        <h2>Your Novels</h2>
        <ul>
            <?php foreach ($novels as $novel): ?>
                <li class="novel-list-item" data-id="<?php echo $novel['id']; ?>">
                    <span class="novel-title"><?php echo $novel['title']; ?></span>
                    <div class="novel-list-shadow"></div>
                </li>
            <?php endforeach; ?>
        </ul>

        <button class="create-btn">Create</button>
    </div>

    <div class="novel-details">
        <div class="default-novel-details">
            <?php if($novelCount == 0): ?>
                <div class="no_novels">
                    <h3>Such emptiness..</h3>
                    <h4>Click on Create to write your first novel</h4>
                </div>
            <?php else: ?>
            <div class='edit-novel'>
                <h2><?php echo $firstNovel['title'] ?></h2>
                <p><?php echo $firstNovel['content'] ?><p>
<!--                <button type="button" id="deleteBtn">Delete Novel</button>-->
            </div>
            <div class="selectedNovel">
                <button class="deleteBtn">Delete Novel</button>
            </div>
            <?php endif; ?>
        </div>



        <form id="novelForm" method="post" action="save_novel.php">
            <div class="createandbuttons">
            <input type="text" id="titleInput" name="titleInput" placeholder="Title">
                <div class="btn-group">
                    <div class="publis-buttons">
                        <button type="button" id="cancelBtn">Cancel</button>
                        <button type="submit" id="saveBtn">Save</button>
                    </div>
                </div>
            </div>
            <textarea id="contentInput" name="contentInput" placeholder="Novel content"></textarea>
        </form>

        <div class="editNovelForm">
            <div class="edittitleandbuttons">
                <input type="text" id="editTitleInput" name="new_title" placeholder="Title">
                <div class="btn-group">
                    <div class="edit-buttons">
                        <button type="button" id="editCancelBtn">Cancel</button>
                        <button type="button" class="editSaveBtn">Save</button>
                    </div>
                </div>
            </div>

            <textarea id="editContentInput" name="new_content" placeholder="Novel content"></textarea>

        </div>
    </div>
</div>
<script>
    const createBtn = document.querySelector('.create-btn');
    const novelList = document.querySelector('.novel-list');
    const novelDetails = document.querySelector('.novel-details');
    const defaultNovelDetails = document.querySelector('.default-novel-details');
    const novelForm = document.querySelector('#novelForm');
    const editNovelForm = document.querySelector('.editNovelForm');
    const editNovel = document.querySelector('.edit-novel');
    const selectedNovel = document.querySelector('.selectedNovel');
    const titleInput = document.getElementById('titleInput');
    const contentInput = document.getElementById('contentInput');
    const editTitleInput = document.getElementById('editTitleInput');
    const editContentInput = document.getElementById('editContentInput');
    const cancelBtn = document.getElementById('cancelBtn');
    const editCancelBtn = document.getElementById("editCancelBtn");
    const editSaveButton = document.querySelector('.editSaveBtn');
    deleteBtn = document.querySelector('.deleteBtn');
    const saveBtn = document.getElementById('saveBtn');
    var selectedNovelID = 0;

    // alert(deleteBtn.length);
    // Hide novel details by default
    // novelDetails.style.display = 'none';

    // Show novel details when Create button is clicked
    createBtn.addEventListener('click', () => {
        defaultNovelDetails.style.display = 'none';
        editNovelForm.style.display = 'none';
        novelForm.style.display = 'block';
        titleInput.value = '';
        contentInput.value = '';
    });

    // Show novel details when a title is clicked
    const novelTitles = document.querySelectorAll('.novel-list li');
    novelTitles.forEach(title => {
        title.addEventListener('click', () => {
            const novelId = title.dataset.id; // Assuming you have a data-id attribute on each title li element containing the novel ID
            fetch(`get_novel.php?id=${novelId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    titleInput.value = data.title;
                    contentInput.value = data.content;
                    novelForm.style.display = 'none';
                    editNovelForm.style.display = 'none';
                    selectedNovel.style.display = 'block';
                    defaultNovelDetails.style.display = 'block';
                    selectedNovel.innerHTML = "<div class='selectedNovelandTitle'><h2>" + data.title + "</h2><div class='selectedNovelandButtons'><button class='editNovelBtn'>Edit</button><button class=\"deleteBtn\">Delete Novel</button></div></div>  <br> <div class='novelContentInspect'>" + data.content
                        + "</div>"; //Everyhting that is inside .selectedNovel has to be copied here
                    deleteBtn = document.querySelector('.deleteBtn');
                    deleteBtn.addEventListener('click', function (){
                        deleteNovel(novelId);
                    })

                    var editBtn = document.querySelector('.editNovelBtn');
                    editBtn.addEventListener('click', () => {
                        defaultNovelDetails.style.display = 'none';
                        selectedNovel.style.display = 'none';
                        novelForm.style.display = 'none';
                        editNovelForm.style.display = 'block';
                        selectedNovelID = novelId;

                        editTitleInput.value = data.title;
                        editContentInput.value = data.content;
                    });

                    console.log(deleteBtn.innerHTML);
                    selectedNovel.style.display = 'block';
                    editNovel.style.display = 'none';
                    novelDetails.style.display = 'block';
                })
                .catch(error => {
                    console.error('There was a problem with your fetch operation:');
                });
        });
    });

    // Cancel button click handler
    cancelBtn.addEventListener('click', () => {
        cancel();
    });
    editCancelBtn.addEventListener('click', () => {
        cancel();
    })

    function cancel(){
        titleInput.value = '';
        contentInput.value = '';
        novelForm.style.display = 'none';
        editNovelForm.style.display = 'none';
        defaultNovelDetails.style.display = 'block';
        selectedNovel.style.display = 'block';
    }

    editSaveButton.addEventListener('click', () =>{
        editNovelPatch(selectedNovelID, editTitleInput.value, editContentInput.value);
    })

    function  editNovelPatch(novelID, newTitle, newContent){
        fetch('edit_novel.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `novel_id=${novelID}&new_title=${newTitle}&new_content=${newContent}`
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                location.reload();
                // return response.json();
            })
            .catch(error => {
                console.error('There was a problem with your fetch operation:', error);
            });
    }
    function deleteNovel(novelID){
        if (confirm('Are you sure you want to delete this novel?')) {
            fetch('delete_novel.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `novel_id=${novelID}`
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    location.reload();
                    return response.json();
                })
                .catch(error => {
                    console.error('There was a problem with your fetch operation');
                });
        }
    }


</script>
</body>
</html>
