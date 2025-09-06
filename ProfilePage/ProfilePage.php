<!-- 🔹 1. HTML form for upload
<form action="uploadProfile.php" method="post" enctype="multipart/form-data">
    <label for="profilePic">Upload Profile Picture:</label>
    <input type="file" name="profilePic" id="profilePic" accept="image/*">
    <input type="submit" value="Upload">
</form>


enctype="multipart/form-data" is required for file uploads.

accept="image/*" limits file selection to images.

🔹 2. PHP backend (uploadProfile.php)
<?php
session_start();
include("../../database/database.php");

// Assume user is logged in and we stored UserID in session
$userID = $_SESSION['userInfo']['UserID'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profilePic"])) {
    $targetDir = "../../Profile/";  // folder where you store profile pics
    
    // Create folder if not exist
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Get file extension (safe)
    $fileExtension = pathinfo($_FILES["profilePic"]["name"], PATHINFO_EXTENSION);
    $fileName = $userID . "." . strtolower($fileExtension);
    $targetFile = $targetDir . $fileName;

    // Validate image
    $check = getimagesize($_FILES["profilePic"]["tmp_name"]);
    if ($check === false) {
        die("File is not an image.");
    }

    // Optional: restrict allowed extensions
    $allowed = ["jpg", "jpeg", "png", "gif"];
    if (!in_array(strtolower($fileExtension), $allowed)) {
        die("Only JPG, JPEG, PNG & GIF allowed.");
    }

    // Move uploaded file
    if (move_uploaded_file($_FILES["profilePic"]["tmp_name"], $targetFile)) {
        echo "Profile picture uploaded successfully.";

        // Update database (store path or filename)
        $sql = "UPDATE user SET PictureName = ?, PicturePath = ? WHERE UserID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $fileName, $targetDir, $userID);
        $stmt->execute();

    } else {
        echo "Error uploading file.";
    }
}
?>

🔹 3. Database update

You already have PictureName and PicturePath in your user table, so we just update them after upload.

Example stored in DB:

UserID = 5
PictureName = "5.jpg"
PicturePath = "../../Profile/"

🔹 4. Displaying profile picture
<?php
$userID = $_SESSION['userInfo']['UserID'];
$sql = "SELECT PictureName, PicturePath FROM user WHERE UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
?>

<img src="<?php echo $row['PicturePath'] . $row['PictureName']; ?>" alt="Profile Picture" width="150">


✅ This way:

Each user’s profile picture is saved as Profile/<UserID>.jpg (or png).

You update the DB with filename + path.

Easy to fetch later when displaying profile or comments. -->