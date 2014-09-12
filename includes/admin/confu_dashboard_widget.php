<table width="100%" style="margin-bottom:10px;padding-bottom:10px;border-bottom:1px solid #ddd;">
	<tr>
		<th align="left">Tilmeldinger</th>
		<td align="right"><?php $count = count_users(); echo $count["avail_roles"]["attendant"]; ?></td>
	</tr>
</table>
<table width="100%">
	<tr>
		<td><?php echo getSuccessBar(); ?></td>
	</tr>
</table>