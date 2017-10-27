<?php
include('header.php');
?>
<form method="POST" action="process.php?update">
<input type='hidden' name='id' value='<?php echo $item->id;?>'/>

<textarea cols=45 name="text" ><?php echo $item->text ?></textarea></p>
<input name="" type="submit" value="update">
<script src='dist/autosize.js'></script>

<script>
autosize(document.querySelectorAll('textarea'));
</script>	

</form>
<br>
<form method="POST" action="process.php?delete">
<input type='hidden' name='id' value='<?php echo $item->id;?>'/>
<input type="submit" value="Delete"/>
</form>
