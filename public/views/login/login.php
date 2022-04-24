<?php

defined('ROOT_PATH') OR exit();

if (UserSession::getUser()->isLoggedIn())
    echo '<p>Already logged in!</p>';

?>

<form method="post">
	<table>
		<tr>
			<td>User</td> 
			<td><input type="text" name="user-data[username]"></td>
		</tr>
		<tr>
			<td>Password</td>
			<td><input type="password" name="user-data[password]"></td>
		</tr>
        <?php if ($this->userSession->loginErrorMessage): ?>
        <tr>
            <td colspan="2"><?=$this->userSession->loginErrorMessage?></td>
        </tr>
        <?php endif ?>
		<tr>
			<td colspan="2">
			<input type="submit" value="Enter" name="log-in"> 
			</td>
		</tr>
	</table>
</form>
