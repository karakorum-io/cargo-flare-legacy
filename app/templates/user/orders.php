<? include_once("menu.php"); ?>
			@flash_message@
			<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
			    <tr class="grid-head">
			        <td class="grid-head-left"><?=$this->order->getTitle("product_id", "Product")?></td>
			        <td><?=$this->order->getTitle("cutoff_date", "Cutoff date")?></td>
			        <td class="grid-head-right">Actions</td>
			    </tr>
			    	<? if (count($this->data) > 0){?>
					    <?php foreach ($this->data as $i => $data): ?>
					    <tr class="grid-body<?php echo ($i == 0 ? " first-row" : ""); ?>" id="row-<?php echo $data['id']; ?>">
					        <td class="grid-body-left" valign="top">
            	                <strong>Тип услуги:</strong> <?=$data['product_name'];?><br />
								<strong>Количество:</strong> <?=$data['qty'];?><br />
								<strong>Видеоспикер:</strong> <?=$data['speaker_name'];?><br />
								<strong>Длительность ролика:</strong> <?=$data['duration_name'];?><br />
								<strong>Наценка за длительность:</strong> <?=number_format($data['duration_price']);?><br />
								<strong>Текст:</strong> <?=$data['is_own_text']==0?"Заказ написания":"";?><br />
								<?=$data['text']?>
					        </td>
					        <td valign="top">
					        	<strong>Срок размещения (до):</strong> <?=$data['cutoff_date'];?><br />
								<strong>Базовая стоимость:</strong> <?=number_format($data['product_price']);?><br />
								<strong>Наценка за текст:</strong> <?=number_format($data['text_price']);?><br />
								<strong>Наценка за размещение:</strong> <?=number_format($data['period_price']);?> (<?=$data['period_name'];?>)<br />
								<strong>Итоговая стоимость:</strong> <?=number_format($data['total']);?><br />
								<strong>Комментарии:</strong> <?=$data['comments']?>
					        </td>
					        <td class="grid-body-right" align="center">
						        <? $data['status'] = ($data['status']=='Active'?"Включен":"Выключен"); ?>
								<strong>Статус:</strong>
								<?=statusText(getLink("user", "orderstatus", "id", $data['id']), $data['status'])?>
								<br />
								<br />
								<? if (trim($data['flashcode']) !=""){?>
									<a href="<?=getLink("user","getcode","id",$data['id'])?>">Получить код</a>
								<?}?>
					        </td>
					    </tr>
					    <?php endforeach; ?>
				    <?}?>
			</table>
			@pager@