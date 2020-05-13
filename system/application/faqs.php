<?php
require_once __DIR__ . '/../bootstrap.php';
//require_once("adminsecurity.php");
$faq_areaDetails		=	$AdminDAO->getrows("faq_area","*", "", array());
?>
<style>
.register-container
{
	max-width:100% !important;	
}
p {
    line-height: 20px;
    text-align: justify;
}
.faq-question
{
	font-size:16px !important;
}
#faq_modal {
	z-index: 9999; position: absolute;
}
#load_faq_modal_content{
	overflow: hidden;
}
</style>

<!--<div class="color-line"></div>-->

    <div class="row1" style="margin-top: -60px;" >
        <div class="col-md-8col-md-offset-2">
            <div class="hpanel">
                <div class="panel-body43">
                	<div class="col-lg-12">
	                	<?php 
		                	
		                	if(!empty($faq_areaDetails)){
			                	$counter = 1;
			                	foreach($faq_areaDetails as $faqarea){ ?>
							<div class="hpanel panel-group" id="accordion" role="tablist" aria-multiselectable="true" style="margin-top:10px !important;">
	                            <div class="panel-body">
	                                <h4 class="m-t-none m-b-none"><?php echo $faqarea['area_title']; ?></h4>
	                                <small class="text-muted"></small>
	                            </div>
	                            <?php
		                        	$faqsDetails		=	$AdminDAO->getrows("faqs","*", "area_id = :area_id", array(":area_id"=>$faqarea['id']));
		                        	if(!empty($faqsDetails)){
									foreach($faqsDetails as $key => $faqs){ ?>
											<div class="panel-body">
				                                <a data-toggle="collapse" data-parent="#accordion" href="#question-<?php echo $counter; ?>" aria-expanded="false" class="collapsed faq-question">
				                                    <i class="fa fa-chevron-down pull-right text-muted"></i>
				                                    <?php echo $faqs['question']; ?>
				                                </a>
				                                <div id="question-<?php echo $counter; ?>" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
				                                    <hr>
				                                    <?php 
					                                    $answer = convertYoutube($faqs['answer']);
					                                    $answer = preg_replace_callback("#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#",  'replaceUrls',$answer);
					                                    
					                                    echo nl2br($answer); ?> 
				                                </div>
				                            </div>
									<?php $counter++; }
								}
		                        ?>
	                        </div>
                        <?php 	}
		                	} ?>
                        <br />
                        <br />
					</div>	
            	</div>
            </div>
        </div>
    </div>

<?php
//require_once("../jsinclude.php");
?>
