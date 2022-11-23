<?php
     $data = $this->daffny->tpl->data;
     $weekDates = $this->daffny->tpl->weekDates;
     $assignedAgentsName = $this->daffny->tpl->assignedAgents;
     $allAgentData = $this->daffny->tpl->agentnameAndId;     
    
     $id = explode("/",$_GET['url']);
     $ID =$id[3];
?>
<p>Last Updated : <span id="last-udpated">X</span> seconds ago</p>
<p>Week date range: <?php echo $this->daffny->tpl->fromDate;?> to <?php echo $this->daffny->tpl->toDate;?></p>
<input type="button" id="export-table" class ="export_button btn btn-primary" value="Export Table" />
<br/><br/>
<table id="detail-table" class="table table-bordered table-striped">
    <thead>
        <tr class="grid-head">
            <td>Agent</td>
            <td>Sunday</td>
            <td>Monday</td>
            <td>Tuesday</td>
            <td>Wednesday</td>
            <td>Thursday</td>
            <td>Friday</td>
            <td>Saturday</td>
            <td>Total</td>
        </tr>
    </thead>
    <tbody id='wallboard-table-body'>
        <?php
            $sql = "CALL wallboardData('".$assignedAgentsName."','".$weekDates."');";
            /**
                * increased execution time for procedure
                */

            ini_set('max_execution_time', 300);

            $resultz = $this->daffny->DB->hardQuery($sql);
            $dataArray = array();

            $agentCount = 1;
            $dateCount = 0;
            $weekCount = 1;
            $id = 0;
            $weektotal = 0;
            $weekCountTotal = 0;
            
            while($row = mysqli_fetch_assoc($resultz)){
                if($agentCount == 1){ 
                    $dataArray[]['agent_id'] = $row['agent_id'];
                    $agentCount = 2;
                }

                if($dateCount <= 6){
                    $dataArray[$id]['deposit'][$dateCount]['amount'] = $row['deposite'];
                    $dataArray[$id]['deposit'][$dateCount]['count'] = $row['orderCount'];
                    $weektotal = $weektotal + $row['deposite'];
                    $weekCountTotal = $weekCountTotal + $row['orderCount'];
                    $dateCount++;                    
                    if($dateCount == 7){
                        $dataArray[$id]['weekTotal'] = $weektotal;
                        $dataArray[$id]['weekCountTotal'] = $weekCountTotal;
                        $weektotal = 0;
                        $weekCountTotal = 0;
                        $agentCount = 1;
                        $dateCount = 0;
                        $id++;
                    }
                }
            }

            $grandTotal = 0;
            $grandCountTotal = 0;

            for($j=0;$j<count($dataArray);$j++){

                echo "<tr>";
                echo "<td style='text-align:left;'>".agentName($allAgentData,$dataArray[$j]['agent_id'])."</td>";

                for($k=0;$k<7;$k++){
                    $grandTotal = $grandTotal + $dataArray[$j]['deposit'][$k]['amount'];
                    if($dataArray[$j]['deposit'][$k]['count'] == 0){
                        $background = "style='background:#F3CCC4;text-align:right;'";
                    } else {
                        $background = "style='text-align:right;'";
                    }

                    echo "<td ".$background.">".$dataArray[$j]['deposit'][$k]['amount']." (".$dataArray[$j]['deposit'][$k]['count'].")</td>";
                }

                $grandCountTotal = $grandCountTotal + $dataArray[$j]['weekCountTotal'];

                echo "<td style='text-align:right;'>".$dataArray[$j]['weekTotal']."(".$dataArray[$j]['weekCountTotal'].")</td>";
                echo "</tr>";
            }

            echo "<tr>"
            . "<td colspan='8' style='text-align:right;'>Grand Total: </td>"
            . "<td style='text-align:right;'><b>".$grandTotal."(".$grandCountTotal.")</b></td>"
            . "</tr>";
        ?>
    </tbody>
</table>
<?php

    function agentName($agents,$id){
        $agentname = "";
        for($i=0;$i<count($agents);$i++){
            if($agents[$i]['id']==$id){
                $agentname = $agents[$i]['name'];
            }
        }
        return $agentname;
        
    }
?>
<script>
    $("#export-table").click(function () {
        $("#detail-table").table2excel({
            filename: "Wallboard-<?php echo date('Y-m-d h:i:s')?>"
        });
    });

    let seconds = 0;
    setInterval(()=>{
        $("#last-udpated").html(seconds);
        seconds++;

        if(seconds > 10){
            location.reload();
        }
    },1000);
</script>