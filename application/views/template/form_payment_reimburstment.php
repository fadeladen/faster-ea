<page>
	<style>
		*,
		p,
		td,
		th {
			font-size: 12px;
		}

		#table {
			border-collapse: collapse;
			width: 100%;
		}

		#table td,
		#table th {
			font-size: 9px;
			border: none;
			padding-top: 4px;
			padding-bottom: 4px;
			padding-left: 3px;
			padding-right: 3px;
		}

		table th {
			text-align: center;
		}

	</style>
	<img src="<?php echo FCPATH . 'assets/images/logos/FHI_360.png' ?>" style='width:120px;position:absolute;left:0px'>

	<br>
	<p style="font-weight: bold; text-align: center; font-size: 18px">EA PAYMENT REQUEST FORM</p>
	<p style="text-align: center; font-size: 14px">Payment are made within 5 working days of receipt of complete payment
		request.</p>
	<br>
	<table id="table" style="width: 100%; margin-left: -5px">
		<tr>
			<td style="width: 210px; padding: 5px; font-size: 14px">Amount of Payments (IDR)</td>
			<td style="width: 10px">:</td>
			<td style="width: 400px; padding: 5px; font-size: 14px">IDR
				<?= number_format($detail['total_destinations_cost'],2,',','.')  ?>
			</td>
		</tr>
		<tr>
			<td style="width: 180px; padding: 5px; font-size: 14px">Submitted to Finance</td>
			<td style="width: 10px">:</td>
			<td style="width: 400px; padding: 5px; font-size: 14px">
				<?= date('d F Y', strtotime($detail['created_at'])) ?></td>
		</tr>
	</table>
	<p style="font-size: 14px">Description of work done or material provided by vendor:</p>
	<table id="table" style="width: 100%; margin-left: -5px;">
		<tr style="background-color: #f7f7f7; font-weight: bold;">
			<td style="width: 10px; padding: 5px; font-size: 14px;">No</td>
			<td style="width: 210px; padding: 5px; font-size: 14px">Description</td>
			<td style="width: 10px"></td>
			<td style="width: 400px; padding: 5px; font-size: 14px">Amount</td>
		</tr>
        <?php $no = 1 ?>
		<?php foreach ($detail['destinations'] as $dest): ?>
		<tr>
			<td style="width: 10px; padding: 5px; font-size: 14px;"><?= $no++ ?></td>
			<td style="width: 210px; padding: 5px; font-size: 14px"><?= $dest['order'] ?> destination total cost</td>
			<td style="width: 10px">: </td>
			<td style="width: 400px; padding: 5px; font-size: 14px">IDR <?= $dest['d_total'] ?></td>
		</tr>
		<?php endforeach; ?>
        <tr>
			<td style="width: 10px; padding: 5px; font-size: 14px;"></td>
			<td style="width: 210px; padding: 5px; font-size: 14px">Total: </td>
			<td style="width: 10px">: </td>
			<td style="width: 400px; padding: 5px; font-size: 14px">IDR <?= number_format($detail['total_destinations_cost'],2,',','.')  ?></td>
		</tr>
	</table>
	<br><br><br><br><br>

	<table id="table" style="width: 100%; margin-left: -5px">

		<tr>
			<td style="width: 220px; padding: 5px;">
				<p style="font-size: 14px">Person Initiating Request:</p>
			</td>
			<td style="width: 220px">
				<div style="border-bottom: 1px solid">
					<div style="text-align: center">
						<img src="<?= $requestor_signature ?>"
							style='height: 60px; margin-bottom: -10px'>
					</div>
					<p style="font-size: 14px; text-align: center"><?= $requestor['username'] ?></p>
				</div>
				<p style="font-size: 14px; text-align: center; margin-top: 0px"><?= $requestor['purpose'] ?></p>
			</td>
			<td style="width: 120px; padding: 5px">
				<p style="font-size: 14px">Date: &nbsp;&nbsp;&nbsp;
					<?= date('d-m-Y', strtotime($detail['created_at'])) ?></p>
			</td>
		</tr>

		<tr>
			<td style="width: 220px; padding: 5px;">
				<p style="font-size: 14px">Approver:</p>
			</td>
			<td style="width: 220px">
				<div style="border-bottom: 1px solid">
					<div style="text-align: center">
					<img src="<?= $fco_signature ?>"
							style='height: 60px; margin-bottom: -10px'>
					</div>
					<p style="font-size: 14px; text-align: center"><?= $detail['fco_monitor_name'] ?></p>
				</div>
				<p style="font-size: 14px; text-align: center; margin-top: 0px"><?= $detail['fco_monitor_purpose'] ?>
				</p>
			</td>
			<td style="width: 120px; padding: 5px">
				<p style="font-size: 14px">Date: &nbsp;&nbsp;&nbsp; <?= date('d-m-Y', strtotime($detail['fco_signature_date'])) ?></p>
			</td>
		</tr>
	</table>
	<br>
	<p style="font-size: 14px">Please attach supporting document vendor’s invoice:</p>
	<div style="margin-left: 230px">
		<p style="font-size: 14px; margin: 0px"><input type="checkbox"> Invoice / Receipt</p>
		<p style="font-size: 14px; margin: 0px"><input type="checkbox"> Timecard (for Consultants/other temporary help)
		</p>
		<p style="font-size: 14px; margin: 0px"><input type="checkbox"> Consultant Tax ID (on file)</p>
		<p style="font-size: 14px; margin: 0px"><input type="checkbox"> Consultant Agreement</p>
		<p style="font-size: 14px; margin: 0px"><input type="checkbox"> Consultant Tracker</p>
		<p style="font-size: 14px; margin: 0px"><input type="checkbox"> Purchase Order </p>
		<p style="font-size: 14px; margin: 0px"><input type="checkbox"> PO Tracker</p>
		<p style="font-size: 14px; margin: 0px"><input type="checkbox"> Receipt of Goods (signed)</p>
		<p style="font-size: 14px; margin: 0px"><input type="checkbox"> Donor Approval (if required)</p>
		<p style="font-size: 14px; margin: 0px"><input type="checkbox"> Purchase Request</p>
		<p style="font-size: 14px; margin: 0px"><input type="checkbox"> Activity Budgets</p>
	</div>

	<br>
	<p style="font-size: 16px; font-weight: bold">FOR FINANCE ONLY</p>
	<div style="border: 1px solid; padding-left: 10px">
		<table id="table" style="width: 100%; margin-left: -5px">
			<tr>
				<td style="width: 220px; padding: 5px;">
					<p style="font-size: 14px">Received By:</p>
				</td>
				<td></td>
			</tr>
			<tr>
				<td style="width: 220px; padding: 5px;">
					<p style="font-size: 14px">Date Received:</p>
				</td>
				<td></td>
			</tr>
		</table>
	</div>
</page>
