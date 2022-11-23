<?php
	$agents = $this->daffny->agents;
	$availAgents = $this->daffny->availAgents;
?>

<link rel="stylesheet" href="<?php echo SITE_IN ?>styles/wallboard.css">
<link rel="stylesheet" href="<?php echo SITE_IN ?>styles/jkanban.min.css" />
<script src="<?php echo SITE_IN ?>jscripts/wallboard.js"></script>
<script src="<?php echo SITE_IN ?>jscripts/md5.js"></script>
<script src="<?php echo SITE_IN ?>jscripts/jkanban.min.js"></script>

<style type="text/css">
	#myagents {
        overflow-x: auto;
        padding: 20px 0;
	}

	.success {
        background: #00b961;
		color:#fff;
	}

	.danger  {
        background: #ff0000;
		color:#fff;
	}

	.error {
        background: #fb7d44;
	}
</style>

<?php
	$id = explode("/", $_GET['url']);
	$ID = $id[2];

	if ($ID == null) {
		$action = getLink("wallboards", "add_edit");
	} else {
		$action = getLink("wallboards", "add_edit", $ID);
	}
?>

<form id="add_edit_wallboard_form" action="<?php echo $action; ?>" method="post">
    
	<?php
		if ($ID > 0) {
			$formTitle = "Edit Wallboard";
		} else {
			$formTitle = "Add Wallboard";
		}
		echo formBoxStart($formTitle);
	?>
  	<div class="row alert alert-light alert-elevate mt-4">
		<div class="col-12">
			<div class="row mt-4">
				<div class="col-6">
					<div class="row">
						<div class="col-6">
            				@title@
            			</div>
            			<div class="col-6">
               				@hash@
            				<div class="hash_pass mt-2 text-right">
            					<input onclick="generateHash()" type="button" class="btn-sm btn-dark" id="hash-button" value="Generate Hash">
            				</div>
            			</div>
            		</div>
               		<div class="row">
            			<div class="col-6">
           					@forward_email@
							<input type="hidden" name="agentList[]" id="agentList">
							<input type="hidden" name="agentName[]" id="agentName">
            			</div>
            			<div class="col-6">
							@status@
            			</div>
            		</div>
            	</div>
             	<div class="col-6">
					<div id="myagents"></div>
				</div>
        	</div>
 			<?php echo formBoxEnd() ?>
    		<br />
    		<?php echo submitButtons(getLink("wallboards")) ?> &nbsp;
    	</div>
	</div>
</form>

<?php
	if($this->daffny->isEdit){
		$availables = "[";
		foreach ($availAgents as $i => $value) {
			if ($value['contactname'] != '') {
				$availables .= '{
					id: '.$value['id'].',
					title: "'.$value['contactname'].'",
				},';
			}
		}	
		$availables .= "]";
	
		$assigneds = "[";
		for ($i = 0; $i < count($agents); $i++) {
			if ($agents[$i]['agent_name'] != '') {
				$assigneds .= '{
					id: '.$agents[$i]['agent_id'].',
					title: "'.$agents[$i]['agent_name'].'",
				},';
			}
		}
		$assigneds .= "]";
	} else {
		$availables = "[";
		for ($i = 0; $i < count($availAgents); $i++) {
			if ($availAgents[$i]['contactname'] != '') {
				$availables .= '{
					id: '.$availAgents[$i]['id'].',
					title: "'.$availAgents[$i]['contactname'].'",
				},';
			}
		}
	
		$availables .= "]";
	
		$assigneds = "[";
		for ($i = 0; $i < count($agents); $i++) {
			if ($agents[$i]['contactname'] != '') {
				$assigneds .= '{
					id: '.$agents[$i]['id'].',
					title: "'.$agents[$i]['contactname'].'",
				},';
			}
		}
		$assigneds .= "]";
	}
	
?>
<script>

	$(document).ready( function (){
		$(".form-box-white-content").children("h4").addClass("h4");
	});

	var KanbanTest = new jKanban({
		element: "#myagents",
		gutter: "15px",
		widthBoard: "auto",
		context: function(el, e) {
			// event triggers on right click
		},
		dropEl: function(el, target, source, sibling){
			// when dropped
		},
		
		boards: [{
			id: "availableAgents",
			title: "Available Agents",
			class: "danger,good",
			dragTo: ["assignedAgents"],
			item: <?php echo $availables;?>
		},
		{
			id: "assignedAgents",
			title: "Assigned Agents",
			class: "success",
			item: <?php echo $assigneds;?>
		}]
	});

	$("#add_edit_wallboard_form").on('submit', ()=>{

		var allEle = KanbanTest.getBoardElements("assignedAgents");
		let agentList = [];
		let agentName = [];

		allEle.forEach(function(item, index) {
			agentName.push(item.innerHTML);
			agentList.push(item.getAttribute('data-eid'));
		});
		
		document.getElementById('agentList').value = agentList;
		document.getElementById('agentName').value = agentName;
	});
</script>