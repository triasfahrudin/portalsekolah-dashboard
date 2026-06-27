<html>
   <head>
      <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
      <style type="text/css">
         /*!
         Pure v0.6.0
         Copyright 2014 Yahoo! Inc. All rights reserved.
         Licensed under the BSD License.
         https://github.com/yahoo/pure/blob/master/LICENSE.md
         */
         .pure-table {
         /* Remove spacing between table cells (from Normalize.css) */
         border-collapse: collapse;
         border-spacing: 0;
         empty-cells: show;
         border: 1px solid #cbcbcb;
         width: 100%;
         }
         .pure-table caption {
         color: #000;
         font: italic 85%/1 arial, sans-serif;
         padding: 1em 0;
         text-align: center;
         }
         .pure-table td,
         .pure-table th {
         border-left: 1px solid #cbcbcb;
         /*  inner column border */
         border-width: 0 0 0 1px;
         font-size: inherit;
         margin: 0;
         overflow: visible;
         /*to make ths where the title is really long work*/
         padding: 0.5em 1em;
         /* cell padding */
         }
         /* Consider removing this next declaration block, as it causes problems when
         there's a rowspan on the first cell. Case added to the tests. issue#432 */
         .pure-table td:first-child,
         .pure-table th:first-child {
         border-left-width: 0;
         }
         .pure-table thead {
         background-color: #e0e0e0;
         color: #000;
         text-align: left;
         vertical-align: bottom;
         }
         /*
         striping:
         even - #fff (white)
         odd  - #f2f2f2 (light gray)
         */
         .pure-table td {
         background-color: transparent;
         }
         .pure-table-odd td {
         background-color: #f2f2f2;
         }
         /* nth-child selector for modern browsers */
         .pure-table-striped tr:nth-child(2n-1) td {
         background-color: #f2f2f2;
         }
         /* BORDERED TABLES */
         .pure-table-bordered td {
         border-bottom: 1px solid #cbcbcb;
         }
         .pure-table-bordered tbody>tr:last-child>td {
         border-bottom-width: 0;
         }
         /* HORIZONTAL BORDERED TABLES */
         .pure-table-horizontal td,
         .pure-table-horizontal th {
         border-width: 0 0 1px 0;
         border-bottom: 1px solid #cbcbcb;
         }
         .pure-table-horizontal tbody>tr:last-child>td {
         	border-bottom-width: 0;
         }

			tr.border_bottom td {
 			 border-bottom: 1px solid black;
			}
      </style>
   </head>
   <body>
      <table cellspacing=0 cellpadding=0 style='border-collapse:collapse;mso-border-bottom-alt:solid windowtext 3.0pt; mso-yfti-tbllook:1184;mso-padding-alt:0cm 5.4pt 0cm 5.4pt;mso-border-insideh: none;mso-border-insidev:none'>
         <tr class="border_bottom">
            <td width=132 style='width:99.0pt;border-bottom:solid windowtext 3.0pt; padding:0cm 5.4pt 0cm 5.4pt'>
               <p class=MsoNormal align=center style='margin-bottom:0cm;text-align:center;line-height:normal'>
                  <span style='mso-no-proof:yes'>
                  <img width=86 height=110 src="<?php echo './uploads/' . $data['logo'];?>">
                  </span>
               </p>
            </td>
            <td width=488 valign=top style='vertical-align: top; text-align: center;width:366.05pt;border:none;border-bottom: solid windowtext 3.0pt;padding:0cm 5.4pt 0cm 5.4pt'>
               <p class=MsoNormal align=center style='margin-bottom:0cm;text-align:center;
                  line-height:150%'>
                  <b>
                     <span lang=EN-US style='font-size:14.0pt;line-height: 150%;font-family:"Times New Roman",serif;mso-ansi-language:EN-US'>
                        PEMERINTAH <?php echo $data['kabupaten'];?>
                        <o:p></o:p>
                     </span>
                  </b>
               </p>
               <p class=MsoNormal align=center style='margin-bottom:0cm;text-align:center;line-height:150%'>
                  <b>
                     <span lang=EN-US style='font-size:18.0pt;line-height:150%;font-family:"Times New Roman",serif;mso-ansi-language:EN-US'>
                        DINAS PENDIDIKAN
                        <o:p></o:p>
                     </span>
                  </b>
               </p>
               <p class=MsoNormal align=center style='margin-bottom:0cm;text-align:center; line-height:normal'>
                  <b>
                     <span lang=EN-US style='font-size:13.0pt;font-family:"Times New Roman",serif;mso-ansi-language:EN-US'>
                        <?php echo $data['nama_sekolah']?>
                        <o:p></o:p>
                     </span>
                  </b>
               </p>
               <p class=MsoNormal align=center style='margin-bottom:0cm;text-align:center; line-height:normal'>
                  <b><span lang=EN-US style='font-size:13.0pt;font-family:"Times New Roman",serif;mso-ansi-language:EN-US'>KECAMATAN&nbsp;<?php echo $data['kecamatan']?></span></b>
                  <b>
                     <span
                        lang=EN-US style='font-family:"Times New Roman",serif;mso-ansi-language:EN-US'>
                        <o:p></o:p>
                     </span>
                  </b>
               </p>
            </td>
         </tr>
      </table>
		<br>	
		<img width="100%" src="<?php echo './uploads/black-line.png';?>">

      <p class=MsoNormal align=center style='text-align:center'>
         <b>
            <span lang=EN-US  style='font-size:12.0pt;line-height:107%;font-family:"Times New Roman",serif; mso-ansi-language:EN-US'>
               <?php echo $data['header']?>
               <o:p></o:p>
            </span>
         </b>
      </p>
      <b>
         <span lang="EN-US" style="font-size:12.0pt;line-height:107%;font-family:&quot;Times New Roman&quot;,serif;mso-ansi-language:EN-US">
            NAMA<span style="mso-tab-count:1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span>: <?php echo $data['nama_pegawai'];?>
            <o:p></o:p>
         </span>
      </b>
      <br>
      <b>
         <span lang="EN-US" style="font-size:12.0pt;line-height:107%;font-family:&quot;Times New Roman&quot;,serif;mso-ansi-language:EN-US">
            BULAN<span style="mso-tab-count:1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>: <?php echo $bulan;?>
            <o:p></o:p>
         </span>
      </b>
      <br>
      <b>
         <span lang="EN-US" style="font-size:12.0pt;line-height:107%;font-family:&quot;Times New Roman&quot;,serif;mso-ansi-language:EN-US">
            TAHUN<span style="mso-tab-count:1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span>: <?php echo $tahun;?>
            <o:p></o:p>
         </span>
      </b>
      <br><br>
      <table class="pure-table pure-table-bordered">
         <thead>
            <?php $fields = $rs->list_fields(); ?>
            <tr>
               <?php foreach ($fields as $field) { ?>
               <th><?php echo strtoupper(str_replace('_', ' ', $field))?></th>
               <?php } ?>
            </tr>
         </thead>
         <tbody>
            <?php foreach ($rs->result() as $d) { ?>
            <tr>
               <?php foreach ($fields as $field) { ?>
               <td><?php echo nl2br($d->$field)?></td>
               <?php } ?>
            </tr>
            <?php } ?>
         </tbody>
      </table>

		<br>
		<br>						

	
		<table style="text-align: left; width: 203px; height: 116px; margin-left: auto; margin-right: 0px;" border="0" cellpadding="2" cellspacing="2">
			<tbody>
				<tr>
					<td style="vertical-align: top; text-align: center;">KEPALA SEKOLAH<br>
					<br>
						<span
							style="font-family: times new roman,times; font-size:12.0pt;" data-mce-style="font-family: times new roman,times; font-size: small;">
							<?php echo $data['kepsek_nama'];?>
							<br>
						</span>
						<br>
						<br>
						<br>
						<br>
						<br>
						<span  style="font-family: times new roman,times; font-size:12.0pt;"  data-mce-style="font-family: times new roman,times; font-size: small;">
							NUPTK <?php echo $data['kepsek_nuptk'];?>
						</span><br>
					</td>
				</tr>
			</tbody>
		</table>

   </body>

</html>
