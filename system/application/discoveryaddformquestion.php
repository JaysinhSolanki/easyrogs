<?php
@session_start();

$totalrows = @$_GET['totalrows'] ?: 10;
for( $i = 1; $i<=$totalrows; $i++ ) {
	$rand_id = uniqid().rand().$i;
?>
	<tr id="q_<?= $rand_id ?>">
		<td style="vertical-align:middle; width:20%">
			<input type="hidden" name="new_questions[]" value="0" readonly="readonly">
			<input readonly="readonly" type="text"
				name="question_numbers[]"
				id="question_numbers<?= $rand_id ?>"
				placeholder="Question No." class="form-control m-b questionscls" />
		</td>
		<td style="vertical-align:middle">
			<textarea name="question_titles[]"
						id="question_titles<?= $rand_id ?>"
						placeholder="Enter Text Here"
						class="form-control m-b question_titlecls" ><?= "
			" ?></textarea>
		</td>
		<td style="vertical-align:middle;width:10%; text-align:center">
			<a href="javascript:;" title="Add Row Above."
				onclick="addrow('q_<?= $rand_id ?>')">
				<img src="<?= $_SESSION['upload_url'] ?>icons/table-row-up.png"
				style="width: 35px;margin-bottom: 10px;" /></i>
			</a>
			<a href="javascript:;"
				onclick="deletenewquestion('q_<?= $rand_id ?>')">
				<i class="fa fa-trash fa-2x" style="color:red" />
			</a>
		</td>
		</tr>
<?php
}
?>
