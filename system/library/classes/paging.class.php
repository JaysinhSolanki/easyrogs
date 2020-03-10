<?php
class PagedResults {
   /* These are defaults */

   
   public $TotalResults;
   private $CurrentPage = 1;
   public $PageVarName = "page";
   
   public $ResultsPerPage = 10;
   public $LinksPerPage = 5;
   private $ResultArray;
   private $TotalPages;
   function init()
   {
	 // $pagelimitsarray		=	  explode(",",$_SESSION['pagingoptions']);
	 // dump($pagelimitsarray);
	// echo  $this->ResultsPerPage = $pagelimitsarray[0];
      $this->TotalPages = $this->getTotalPages();
      $this->CurrentPage = $this->getCurrentPage();
      $this->ResultArray = array(
                           "PREV_PAGE" => $this->getPrevPage(),
                           "NEXT_PAGE" => $this->getNextPage(),
                           "CURRENT_PAGE" => $this->CurrentPage,
                           "TOTAL_PAGES" => $this->TotalPages,
                           "TOTAL_RESULTS" => $this->TotalResults,
                           "PAGE_NUMBERS" => $this->getNumbers(),
                           "MYSQL_LIMIT1" => $this->getStartOffset(),
                           "MYSQL_LIMIT2" => $this->ResultsPerPage,
                           "START_OFFSET" => $this->getStartOffset(),
                           "END_OFFSET" => $this->getEndOffset(),
                           "RESULTS_PER_PAGE" => $this->ResultsPerPage,
                           );
      // echo __FILE__ . ' ' . __LINE__ ;                    
      //var_dump(debug_backtrace());
   }
   /* Start information functions */
   function getTotalPages() {
      /* Make sure we don't devide by zero */
     // echo $this->TotalResults . ' ' . $this->ResultsPerPage . '<br>';
      if($this->TotalResults != 0 && $this->ResultsPerPage != 0) {
         $result = ceil($this->TotalResults / $this->ResultsPerPage);
      }
      /* If 0, make it 1 page */
      if(isset($result) && $result == 0) {
         return 1;
      } else {
         return $result;
      }
   }
   function getStartOffset() {
      $offset = $this->ResultsPerPage * ($this->CurrentPage - 1);
      if($offset != 0) { $offset++; }
      return $offset;
   }
   function getEndOffset() {
      if($this->getStartOffset() > ($this->TotalResults - $this->ResultsPerPage)) {
         $offset = $this->TotalResults;
      } elseif($this->getStartOffset() != 0) {
         $offset = $this->getStartOffset() + $this->ResultsPerPage - 1;
      } else {
         $offset = $this->ResultsPerPage;
      }
      return $offset;
   }
   function getCurrentPage() {
      if(isset($_GET[$this->PageVarName])) {
         return $_GET[$this->PageVarName];
      } else {
         return $this->CurrentPage;
      }
   }
   function getPrevPage() {
      if($this->CurrentPage > 1) {
         return $this->CurrentPage - 1;
      } else {
         return false;
      }
   }
   function getNextPage() {
      if($this->CurrentPage < $this->TotalPages) {
         return $this->CurrentPage + 1;
      } else {
         return false;
      }
   }
   function getStartNumber() {
      $links_per_page_half = $this->LinksPerPage / 2;
      /* See if curpage is less than half links per page */
      if($this->CurrentPage <= $links_per_page_half || $this->TotalPages <= $this->LinksPerPage) {
         return 1;
      /* See if curpage is greater than TotalPages minus Half links per page */
      } elseif($this->CurrentPage >= ($this->TotalPages - $links_per_page_half)) {
         return $this->TotalPages - $this->LinksPerPage + 1;
      } else {
         return $this->CurrentPage - $links_per_page_half;
      }
   }
   function getEndNumber() {
      if($this->TotalPages < $this->LinksPerPage) {
         return $this->TotalPages;
      } else {
         return $this->getStartNumber() + $this->LinksPerPage - 1;
      }
   }
   function getNumbers() {
      for($i=$this->getStartNumber(); $i<=$this->getEndNumber(); $i++) {
         $numbers[] = $i;
      }
      return $numbers;
   }

function pageHTML($qrysring='',$position='top')
{
	
   		$this->init();
   		$pgarray = explode("/", $_SERVER['SCRIPT_NAME']);
		$curpage = $pgarray[count($pgarray)-1];
		$curnumber = $this->ResultArray['CURRENT_PAGE'];
		$start = 1;
		$total   = $this->TotalPages;
		$start = (($curnumber - 2) > 0) ? ($curnumber-2) : 1;
		$end   = (($total - $curnumber) >= 2) ? ($curnumber+2) : $total;
		$ret   = '';
		if($qrysring=='')
		{
			if($_SERVER['QUERY_STRING'])
			{
				$qstring = preg_replace("/&?page=\d+/", '', $_SERVER['QUERY_STRING']);
				//echo "Query string is $qstring<br>";	
				if($qstring)
				{
					$url = $curpage . '?' . $qstring . "&page=";
				}
				else
				{ 
					$url = $curpage . "?page=";	
				}
			}
			else 
			{
				$url = $curpage . "?page=";
			}
		}
		else
		{
			$qrysring = preg_replace("/&?page=\d+/", '', $qrysring);
			$url=$qrysring;
		}
		//echo " Start is $start and end is $end<br>";
		for($i=$start; $i<=$end; $i++) 
		{
			if($qrysring)
			{
				$pageurl = str_replace('~~i~~',$i,$url);
			}
			if($this->ResultArray["CURRENT_PAGE"] == $i) 
      		{
         		$ret .=  "<li class=' paginate_button active'><a>$i</a></li>";
      		} 
      		else 
      		{
         		$ret .= "<li class='paginate_button'><a href='javascript: void(0)' onclick='$pageurl'>$i</a></li>";
      		}
   		}
		
   		$first = $last = $next = $previous = '';
   		if($this->ResultArray["CURRENT_PAGE"]!= 1) 
   		{
			if($qrysring)
		  	{
				$url2   = str_replace('~~i~~',1,$url);
				$first =  "<li class='paginate_button'><a href='javascript: void(0)' onclick='$url2'><<</a></li> ";
			}
			else
			{
				$first =  "<li class='paginate_button'><a href='$url" . "1'><<</a></li> ";
			}
   		} 
   		else 
   		{
      		$first = "<li class='paginate_button prev disabled'><a><<</a></li>";
   		}
	   // Print out our prev link 
	   if($this->ResultArray["PREV_PAGE"]) 
	   {
	      if($qrysring)
		  {
		  	$url3   = str_replace('~~i~~',$this->ResultArray["PREV_PAGE"],$url);
			$previous =  "<li  class='paginate_button' ><a href='javascript: void(0)' onclick='$url3'><</a></li>";
		  }else
		  {
		  	$previous =  "<li  class='paginate_button' ><a  href='$url" . $this->ResultArray["PREV_PAGE"] . "'><</a></li>";
	   	   }
	   } else 
	   {
	      $previous =  "<li class='previous disabled  paginate_button '><a><</a></li>";
	   }
   	   // Print out our next link 
	   if($this->ResultArray["NEXT_PAGE"]) {
	     if($qrysring)
		 {
		 	$url4   = str_replace('~~i~~',$this->ResultArray["NEXT_PAGE"],$url);
			$next =  "<li class='paginate_button'><a href='javascript: void(0)' onclick='$url4'>></a></li>";
		  }
		  else
		  {
		  	$next =  "<li class='paginate_button next'><a href='$url" . $this->ResultArray["NEXT_PAGE"] . "'>></a></li>";
		  }
	   }
	   else
	   {
	      $next =  "<li class='paginate_button next disabled'><a class=''>></a></li>";
	   }
	   // Print our last link 
	   if($this->ResultArray["CURRENT_PAGE"]!= $this->ResultArray["TOTAL_PAGES"]) 
	   {
	      if($qrysring)
		 {
		 	//echo $this->ResultArray["TOTAL_PAGES"];
			$url5   = str_replace('~~i~~',$this->ResultArray["TOTAL_PAGES"],$url);
			$last =  "<li  class='paginate_button' ><a href='javascript: void(0)' onclick='$url5'>>></a></li>";
		  }else
		  {
		  	$last =  "<li  class='paginate_button' ><a href='$url" . $this->ResultArray["TOTAL_PAGES"] . "'>>></a></li>";
		  }
	   } else 
	   {
	      $last =  "<li class='paginate_button next disabled'><a>>></a></li>";
	   }
	  /* if($_SESSION['pagelimit']==5)
	   {
		   $sel5	=	"selected=\"selected\"";
	   }
	  else  if($_SESSION['pagelimit']==10)
	   {
		   $sel10	=	"selected=\"selected\"";
	   }
	   else if($_SESSION['pagelimit']==15)
	   {
		   $sel15	=	"selected=\"selected\"";
	   }
	   else if($_SESSION['pagelimit']==30)
	   {
		   $sel30	=	"selected=\"selected\"";
	   }
	   else if($_SESSION['pagelimit']==50)
	   {
		   $sel50	=	"selected=\"selected\"";
	   }
	   else if($_SESSION['pagelimit']==100)
	   {
		   $sel100	=	"selected=\"selected\"";
	   }
	   else
	   {
		   $sel15	=	"selected=\"selected\"";
	   }*/
	   $purl = str_replace('~~i~~',1,$url);
	   
	   if($_SESSION['pagingoptions']=="")
	   {
			$pagelimitsarray	=	array(10,20,30,40, 50,100,200,300,400,500,1000);
	   }
	   else
	   {
			$pagelimitsarray	=	  explode(",",$_SESSION['pagingoptions']);
		}
		
		//$pagelimit	=	"Display records/page:&nbsp;&nbsp;<select style='width:50px; height:22px' name=\"pagelimit\"  id='pagelimit{$position}' onChange='$purl'><option value=\"5\" $sel5>5</option><option value=\"10\" $sel10>10</option><option value=\"15\" $sel15>15</option><option value=\"30\" $sel30>30</option><option value=\"50\" $sel50>50</option><option value=\"100\" $sel100>100</option></select>";
		//$pagelimit	=	"<select style='width: 75px; display: inline; height: 35px; margin-top: 3px;' class='form-control' name='pagelimit'  id='pagelimit{$position}' onChange='$purl'>";
		
		$pagelimit	=	"<select style='width: 75px; display: inline; height: 35px; margin-top: 3px;' class='form-control' name='pagelimit'  id='pagelimit{$position}' onChange='$purl'>";
	   	foreach($pagelimitsarray as $pagelimititem)
		{
			if($_SESSION['pagelimit'] == $pagelimititem)
			{
				$selected	=	' SELECTED =  "SELECTED" ';
			}
			else
			{
				$selected	= "";
			}
			$pagelimit	.=	"<option value='{$pagelimititem}' {$selected}>$pagelimititem</option>";
		}
						
		  $pagelimit	.="				</select>";
		
		$summary = "<div class='col-sm-5'>
                    <div class='dataTables_info' id='example2_info' role='status' aria-live='polite'>
						{$pagelimit}
					</div>
                </div>
			";
		if(!isset($_SESSION['pagelimit']))
		{
			$_SESSION['pagelimit']	=	$pagelimitsarray[0];
		}
		$showingnow_start	=	($_SESSION['pagelimit'])*($this->ResultArray['CURRENT_PAGE'] - 1) + 1;
		$showingnow_end		=	($_SESSION['pagelimit'])*($this->ResultArray['CURRENT_PAGE']);
		if($this->ResultArray['TOTAL_RESULTS'] < $showingnow_end)
		{
			$showingnow_end	=	$this->ResultArray['TOTAL_RESULTS'];
		}
		
		//echo "Page Limit: ".$_SESSION['pagelimit']." Current Page ..." .$this->ResultArray['CURRENT_PAGE']. "Show Now End...".$showingnow_end."<br>";
		
   		$ret = " <div class='col-sm-4 text-left'>
		 			<div class='dataTables_info' id='example2_info' role='status' aria-live='polite'  style='padding-top:14px;'>
						{$pagelimit}
					</div>
		</div>
		 <div class='col-sm-4 text-left'>
			 <div class='dataTables_info' id='example2_info' role='status' aria-live='polite' style='padding-top:18px;'>
				Showing $showingnow_start To $showingnow_end of {$this->ResultArray['TOTAL_RESULTS']}
			</div>
		</div>
		<div class='col-sm-4'>
                    <div class='dataTables_paginate paging_simple_numbers' id='example2_paginate'>
									 <ul class='pagination' style='margin:2px !important'>
							{$first}{$previous}{$ret}{$next}{$last}
						 </ul>
                    </div>
                </div>
			  ";
			  
		return "<div class='row'>{$ret}</div>";
   
}

}/*
	Usage
	// Instantiate the paging class! 
	$Paging = new PagedResults();
	// Select the count of total results  e.g. if you are showing active prospect
	$Paging->TotalResults = $db->fetch($sql);
	$Paging->ResultsPerPage = 10; //results per page limit
	$page  = $Paging->getCurrentPage();
	if($page > 1)
		$start = ($page-1) * $Paging->ResultsPerPage;
	else 
		$start = 1;
	$end   = $Paging->ResultsPerPage;
   if($db->query("Select field1, field2........ from prospects where status < 2 order by fieldname limit $start, $end
   {
   }
   // Paging is only required if results count is  greater than number of records we have
   	if($Paging->TotalResults > $paging->ResultsPerPage) 
    	$pagelinks = $Paging->pageHTML();
    // Assign to smarty		
    $smarty->assign('pagelinks', $pagelinks);
    // In Smarty
    {if $pagelinks}
    	<apply any formating necessary e.g. align right etc {$pagelinks}
    {/if}	
*/
?>