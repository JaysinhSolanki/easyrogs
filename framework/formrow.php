<?php
require_once("common.php");
$random			=	rand(100000,999999);

?>
<tr>
  <th><input type="checkbox" /></th>
  <th><textarea name="lable[]"></textarea></th>
  <th><textarea name="question[]"></textarea></th>
  <th><select name="fieldtype[]" class="input-small">
      <?php $fieldtypes		=	$AdminDAO->getrows("tblfieldtype","*");foreach($fieldtypes as $fieldtype){?>
      <option value="<?php echo $fieldtypes['pkfieldtypeid']; ?>"> <?php echo $fieldtype['fieldtypename']; ?></option>
      <?php } ?>
    </select></th>
  <th><input class="input-small" type="text" name="sortorder[]" /></th>
  <th><input class="input-small" type="text" name="other[]" /></th>
  <th><input class="input-small" type="text" name="isrequired[]" /></th>
</tr>
<tr id="<?php echo $random; ?>"></tr>
<a href="javascript:;" onclick="addanotherrow(<?php echo $random; ?>);">Add Anothere</a>