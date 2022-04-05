<?php if (!defined('ROOT_PATH')) exit ?>

<?php
if ($this->loggedIn)
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
        <?php if ($this->loginErrorMessage): ?>
        <tr>
            <td colspan="2"><?=$this->loginErrorMessage?></td>
        </tr>
        <?php endif ?>
		<tr>
			<td colspan="2">
			<input type="submit" value="Enter"> 
			</td>
		</tr>
	</table>
</form>
