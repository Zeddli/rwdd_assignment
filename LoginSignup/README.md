Cookie 的名字：loginInfo <br>
Session for loginInfo: userInfo <br>
Example:  <br>
    session_start(); <br>
    echo "Username: ". $_SESSION["userInfo"]["username"]; <br>
    echo "Email: ". $_SESSION["userInfo"]["email"]; <br>
    echo "Picture Path: ". $_SESSION["userInfo"]["picturePath"]; <br>
    echo "Picture Name: ". $_SESSION["userInfo"]["pictureName"]; <br>
