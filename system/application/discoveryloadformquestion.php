<?php
require_once( __DIR__ . '/../bootstrap.php' );
require_once( "adminsecurity.php" );

$discovery_id = $_GET['id'];
$form_id      = $_GET['form_id'];
if( in_array( $form_id, array(Discovery::FORM_CA_FROGS, Discovery::FORM_CA_FROGSE, Discovery::FORM_CA_SROGS) ) ) {
    $s_titleQuestion = "Interrogatory";
    $p_titleQuestion = "Interrogatories";
} else {
    $s_titleQuestion = "Request";
    $p_titleQuestion = "Requests";
}
$forms     = $AdminDAO->getrows( 'forms', "*", "id	= :form_id", ['form_id' => $form_id] );
$questions = $AdminDAO->getrows( 'questions', "*", 
                                 "form_id	= :form_id AND 
                                  is_display = 1 AND 
                                  sub_part = '' 
                                  GROUP BY question_number ORDER BY display_order", 
                                  ['form_id' => $form_id] );

if( $discovery_id > 0 ) {
    $myquestion = $AdminDAO->getrows( 'discovery_questions', "*", 
                                      "discovery_id	= :discovery_id", 
                                      ['discovery_id' => $discovery_id] );
    $myquestion = array_column( $myquestion, 'question_id' );
} else {
    $myquestion = [];
}

/****************************************************************************/
if( count( $questions ) ) {
?>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-8">
            <h3 class="text-center">Select <?= $p_titleQuestion ?></h3>
            <table class="table table-bordered table-hover table-striped">
<?php
    if( !count( $questions ) ) {
?>
                    <tr>
                        <td>No <?= $s_titleQuestion ?> Found.</td>
                    </tr>
<?php
    }
    else {
?>
                    <tbody>
                    <tr>
                        <th style="text-align:center">
                            <label>
								<input	type="checkbox"
										onclick="toggleAll('label.q-checkbox > input', event.target);" />
                                Select
                            </label>
						</th>
                        <th style="text-align:center"><?= $s_titleQuestion ?></th>
                    </tr>
<?php
        foreach( $questions as $row ) {
            $uid                 = str_replace( '.', '-', $row['question_number'] );
            $id                  = $row['id'];
            $depends_on_question = $row['depends_on_question'];
            $is_depended_parent  = $row['is_depended_parent'];

            /**
             * Check dependent parent is checked or not
             **/
?>
                        <tr id="this_<?= $id ?>"
<?php
            if( $depends_on_question ) {
?>
                            class="row_<?= $depends_on_question ?>"
                            <?= !in_array( $depends_on_question, $myquestion ) ? ' style="display:none;" ' : '' ?>
<?php
            }
?>
                            >
                            <td style="vertical-align:middle; text-align:center">
                                <label class="q-checkbox">
                                    <input	type="checkbox"
                                            id="q_<?= $uid ?>"
                                           	onclick="checkquestion('q_<?= $uid ?>')
                                                     <?= $is_depended_parent ? ",showhidequestions('$id','$uid') " : '' ?>
                                                    "
                                           	<?= in_array( $id, $myquestion ) ? ' checked ' : '' ?>
                                           	<?= $depends_on_question ? " class='dependent_checked_".$depends_on_question."' " : '' ?>
											value="<?= $id ?>" />
                                </label>
                            </td>
                            <td style="vertical-align:middle; text-align:left" colspan="2">
                                <input type="hidden"
                                       class="q_<?= $uid ?>
											  <?= ( $depends_on_question ) ? "dependent_checked_val_". $depends_on_question : '' ?>"
                                       name="is_selected[]"
                                       value="<?= in_array( $id, $myquestion ) ? 1 : 0 ?>" />
                                <input type="hidden" name="questions[]" value="<?= $id ?>" />
<?php
                                echo "<b> No. ". $row['question_number'] ."</b>: ". $row['question_title'];
								$subparts = $AdminDAO->getrows( 'questions', "*",
																"question_number=:question_number  AND is_display = 1 AND sub_part != '' ORDER BY question_number  ASC, sub_part ASC",
																[ 'question_number' => $row['question_number'] ] );

                                foreach( $subparts as $key => $thisrow ) {
?>
                                    <input type="hidden"
                                           class="q_<?= $uid ?>"
                                           value="<?= ( $discovery_id && in_array( $thisrow['id'], $myquestion ) ) ? 1 : 0 ?>"
                                           name="is_selected[]" />
                                    <input type="hidden"
                                           value="<?= $thisrow['id'] ?>"
                                           name="questions[]" />
                                    <?= "(". $thisrow['sub_part'] .") ". $thisrow['question_title'] ?>
<?php
                                }
                                if( $row['has_extra_text'] ) {
									$extraTextData = $AdminDAO->getrows( 'discovery_questions', "*",
																		"discovery_id	= :discovery_id AND question_id = :question_id",
																		[ 'discovery_id' => $discovery_id, "question_id" => $id, ] );
                                    if( !empty( $extraTextData ) ) {
                                        $extraText = $extraTextData[0]['extra_text'];
                                    } else {
                                        $extraText = "";
                                    }
?>
                                    <div
                                        id="extraTextDivq_<?= $uid ?>" <?= ( !in_array( $id, $myquestion ) ) ? ' style="display:none;" ' : '' ?>>
                                        <textarea name="extra_text[<?= $id ?>]"
                                                  id="extra_text_<?= $uid ?>"
                                                  placeholder="Enter <?= $row['extra_text_field_label'] ?>"
												  class="form-control m-b"><?=
											$extraText
										?></textarea>
                                    </div>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                    <?php
                }
                ?>
            </table>
        </div>
    </div>
    <?php
}
$newquestions = [];
if( $discovery_id ) {
    $newquestions = $AdminDAO->getrows( 'questions', "*",
                                        "discovery_id = :discovery_id ORDER BY CAST(question_number as DECIMAL(10,2)) ASC",
                                        ['discovery_id' => $discovery_id] );
}
if( $forms[0]['allow_custom_questions'] ) {
?>
    <h3 class="text-center"><?= $p_titleQuestion ?></h3>
    <div class="form-group" style="margin-top:10px">
        <label class="col-sm-2 control-label"><span class="redstar" style="color:#F00" title="This field is compulsory"></span></label>
        <div class="col-sm-8">
            <div class="col-md-12" align="right">
                <a href="javascript:;" onclick="loadnewquestion()">
                    <i class="fa fa-plus" /> More <?= $p_titleQuestion ?>
                </a>
            </div>
            <table class="table table-bordered table-hover table-striped">
                <tbody id="addnewquestion">
                <tr>
                    <th style="text-align:center">No</th>
                    <th style="text-align:center"><?php echo $s_titleQuestion; ?></th>
                    <th style="text-align:center">Action</th>
                </tr>
<?php
                if( count( $newquestions ) ) {
                    foreach( $newquestions as $thisrow ) {
?>
                        <tr id="this_<?= $thisrow['id'] ?>">
                            <td style="vertical-align:middle; text-align:center">
                                <input	type="hidden" 
										name="new_questions[]" 
										value="<?= $thisrow['id'] ?>" />
                                <input	type="text" 
										name="question_numbers[]" 
										id="question_numbers<?= $thisrow['id'] ?>"
                                       	readonly="readonly"
                                       	placeholder="Question No."
                                       	class="form-control m-b questionscls"
                                       	value="<?= $thisrow['question_number'] ?>" />
                            </td>
                            <td>
                                <textarea	name="question_titles[]" 
											id="question_titles<?= $thisrow['question_number'] ?>"
                                          	placeholder="Question No."
                                          	class="form-control m-b question_titlecls"><?=
                                    $thisrow['question_title']
                                ?></textarea>
                            </td>
                            <td style="vertical-align:middle; text-align:center">
                                <a href="javascript:;" 	title="Add Row Above."
                                   						onclick="addrow('this_<?= $thisrow['id'] ?>')" >
                                    <img
                                        src="<?= $_SESSION['upload_url'] ?>icons/table-row-up.png"
                                        style="width: 35px;margin-bottom: 10px;" />
                                </a>
                                <a href="javascript:;"	onclick="deletequestion('<?= $thisrow['id'] ?>')">
                                    <i class="fa fa-trash fa-2x" style="color:red" />
                                </a>
                            </td>
                        </tr>
<?php
                    }
                }
?>
                </tbody>
            </table>
            <div class="col-md-12" align="right">
                <a href="javascript:;" onclick="loadnewquestion()">
                    <i class="fa fa-plus" /> More <?= $p_titleQuestion; ?>
                </a>
            </div>
        </div>
    </div>
<?php
}
?>
<script>
function showhidequestions( parentid, uid ) {
    if( $( "#q_" + uid ).is( ":checked" ) ) {
        $( ".row_" + parentid ).show();
        setTimeout( _ => {
        $( ".dependent_checked_" + parentid ).prop( "checked", true );
        $( ".dependent_checked_val_" + parentid ).val( 1 );
        $( ".row_" + parentid ).find( "input[name=\"is_selected[]\"]" ).val( 1 );
        }, 500 )
    }
    else {
        $( ".row_" + parentid ).hide()
        setTimeout( _ => {
        $( ".dependent_checked_" + parentid ).prop( "checked", false );
        $( ".dependent_checked_val_" + parentid ).val( 0 );
        $( ".row_" + parentid ).find( "input[name=\"is_selected[]\"]" ).val( 0 );
        }, 500 )
    }
}

function checkquestion( uid ) {
    if( $( "#" + uid ).is( ":checked" ) ) {
        $( "." + uid ).val( "1" );
        $( "#extraTextDiv" + uid ).show();
    }
    else {
        $( "#extraTextDiv" + uid ).hide();
        $( "." + uid ).val( "0" );
    }
}
</script>
