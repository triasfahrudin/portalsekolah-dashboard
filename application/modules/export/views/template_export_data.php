<caption style="text-align: center; font-weight: bold; font-size: 25px; padding: 10px;"><?php echo $header; ?></caption>
<table aria-describedby="" style="width: 100%; border-collapse: collapse;">
    <thead>
        <?php $fields = $recordset->list_fields(); ?>
        <tr>
            <th style="border: 1px solid black; padding: 5px;">No.</th>
            <?php foreach ($fields as $field) { ?>
                <th style="border: 1px solid black; padding: 5px;"><?php echo strtoupper(str_replace('_', ' ', $field)) ?></th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php if ($recordset->num_rows() == 0) { ?>
            <tr>
                <td style="font-size: 20px; border: 1px solid black; padding: 5px;text-align: center;" colspan="<?php echo count($fields) + 1; ?>">Belum ada ada</td>
            </tr>
        <?php } else { ?>
            <?php $i = 1; ?>
            <?php foreach ($recordset->result() as $data) { ?>
                <tr>
                    <td style="border: 1px solid black; padding: 5px;" align="center"><?php echo $i; ?></td>
                    <?php foreach ($fields as $field) { ?>
                        <td style="border: 1px solid black; padding: 5px;"><?php echo $data->$field !== null ? nl2br($data->$field) : ''; ?></td>
                    <?php } ?>
                    <?php $i++; ?>
                </tr>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>