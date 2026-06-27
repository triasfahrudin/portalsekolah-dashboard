<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDy5ePPPOnm2Ix6_MU7SGsUX4QzrHfH1t4"></script>

<!-- <legend>Web Settings</legend> -->

<div class="panel panel-default">
  <!-- <div class="panel-heading">
      <h3 class="panel-title">
         Form Settings
      </h3>
   </div> -->
  <div class="panel-body">
    <?php if ($setting->num_rows() == 0) { ?>
      <div class="row"></div>
      <div class="alert alert-info">
        <strong>Data tidak ditemukan.</strong>
      </div>
    <?php } else { ?>
      <table width="100%" class="table table-condensed">
        <thead>
          <tr>
            <th style="text-align: left">Nama Setting</th>
            <th style="text-align: left">Nilai</th>
          </tr>
        </thead>
        <tbody>
          <?php $nomor = 0; ?>
          <?php foreach ($setting->result() as $r) { ?>
            <tr>
              <td><?php echo replaceStringWithArray($r->title, array('_' => "\r\n")); ?></td>
              <td>
                <?php if ($r->tipe === 'big-text') { ?>
                  <textarea id="<?php echo $r->title; ?>" class="settings_texteditor update_me" style="width: 100%;height: 100%"><?php echo $r->value; ?></textarea>
                <?php } elseif ($r->tipe === 'small-text') { ?>
                  <input id="<?php echo $r->title; ?>" class="update_me form-control" style="width: 100%;height: 100%" type="text" value="<?php echo $r->value; ?>">
                <?php } elseif ($r->tipe === 'image') { ?>

                  <a href="<?php echo $r->value; ?>" target="_blank">Lihat Gambar</a>
                  <?php echo form_open_multipart('superuser/settings/upload/' . $r->title); ?>
                  <input class="upload" name="img" onchange="this.form.submit()" multiple="" type="file">
                  <?php echo form_close(); ?>
                <?php } elseif ($r->tipe === 'map') { ?>
                  <script>
                    // global "map" variable
                    var map_<?php echo $r->title ?> = null;
                    var marker_<?php echo $r->title ?> = null;

                    var infowindow_<?php echo $r->title ?> = new google.maps.InfoWindow({
                      size: new google.maps.Size(150, 50)
                    });

                    // A function to create the marker and set up the event window function
                    function createMarker_<?php echo $r->title ?>(map, infowindow, latlng, name, html) {
                      var contentString = html;
                      var marker = new google.maps.Marker({
                        position: latlng,
                        map: map,
                        zIndex: Math.round(latlng.lat() * -100000) << 5
                      });

                      google.maps.event.addListener(marker, 'click', function() {
                        infowindow.setContent(contentString);
                        infowindow.open(map, marker);
                      });

                      google.maps.event.trigger(marker, 'click');
                      return marker;
                    }


                    function initialize_<?php echo $r->title ?>() {
                      <?php if ($r->value === "") { ?>

                        var myOptions = {
                          zoom: 4,
                          center: new google.maps.LatLng(-0.08789059053082422, 113.6865234375),
                          mapTypeControl: true,
                          mapTypeControlOptions: {
                            style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                          },
                          navigationControl: true,
                          mapTypeId: google.maps.MapTypeId.ROADMAP
                        }

                        map_<?php echo $r->title ?> = new google.maps.Map(document.getElementById("map_canvas_<?php echo $r->title ?>"), myOptions);

                        google.maps.event.addListener(map_<?php echo $r->title ?>, 'click', function() {
                          infowindow_<?php echo $r->title ?>.close();
                        });

                        google.maps.event.addListener(map_<?php echo $r->title ?>, 'click', function(event) {
                          //call function to create marker
                          if (marker_<?php echo $r->title ?>) {
                            marker_<?php echo $r->title ?>.setMap(null);
                            marker_<?php echo $r->title ?> = null;
                          }

                          marker_<?php echo $r->title ?> = createMarker_<?php echo $r->title ?>(map_<?php echo $r->title ?>, infowindow_<?php echo $r->title ?>, event.latLng, "name", "<b>Location</b><br>" + event.latLng);
                          //alert(event.latLng.lat());
                          //$('#lat').val(event.latLng.lat());
                          //$('#lng').val(event.latLng.lng());
                          $.post("<?php echo base_url() . 'superuser/settings/edt/' . $r->title; ?>", {
                            value: event.latLng.lat() + '|' + event.latLng.lng(),
                            csrf_test_name: Cookies.get('csrf_cookie_name')
                          });
                        });

                      <?php } else { ?>

                        <?php $latlng = explode('|', $r->value); ?>
                        var myLatLng = {
                          lat: <?php echo $latlng[0] ?>,
                          lng: <?php echo $latlng[1] ?>
                        };
                        // create the map
                        var myOptions = {
                          zoom: 15,
                          center: new google.maps.LatLng(<?php echo $latlng[0] ?>, <?php echo $latlng[1] ?>),
                          mapTypeControl: true,
                          mapTypeControlOptions: {
                            style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                          },
                          navigationControl: true,
                          mapTypeId: google.maps.MapTypeId.ROADMAP
                        }

                        map_<?php echo $r->title ?> = new google.maps.Map(document.getElementById("map_canvas_<?php echo $r->title ?>"), myOptions);

                        var marker_<?php echo $r->title ?> = new google.maps.Marker({
                          position: myLatLng,
                          map: map_<?php echo $r->title ?>,
                          title: 'Hello World!'
                        });


                        google.maps.event.addListener(map_<?php echo $r->title ?>, 'click', function() {
                          infowindow_<?php echo $r->title ?>.close();
                        });

                        google.maps.event.addListener(map_<?php echo $r->title ?>, 'click', function(event) {
                          //call function to create marker
                          if (marker_<?php echo $r->title ?>) {
                            marker_<?php echo $r->title ?>.setMap(null);
                            marker_<?php echo $r->title ?> = null;
                          }

                          marker_<?php echo $r->title ?> = createMarker_<?php echo $r->title ?>(map_<?php echo $r->title ?>, infowindow_<?php echo $r->title ?>, event.latLng, "name", "<b>Location</b><br>" + event.latLng);
                          //alert(event.latLng.lat());
                          //$('#lat').val(event.latLng.lat());
                          //$('#lng').val(event.latLng.lng());
                          $.post("<?php echo base_url() . 'superuser/settings/edt/' . $r->title; ?>", {
                            value: event.latLng.lat() + '|' + event.latLng.lng(),
                            csrf_test_name: Cookies.get('csrf_cookie_name')
                          });
                        });


                      <?php } ?>
                    }
                    //]]>

                    window.onload = initialize_<?php echo $r->title ?>;
                  </script>
                  <div id="map_canvas_<?php echo $r->title ?>" style="width:100%; height:350px;"></div>

                <?php } elseif ($r->tipe === 'options') {
                  $option_setting  = explode(';', $r->value);
                  $option_list     = explode(',', $option_setting[0]);
                  $option_selected = $option_setting[1];

                  echo '<select id="' . $r->title . '" name="' . $r->title . '" class="form-control">';

                  foreach ($option_list as $op) {
                    if ($op === $option_selected) {
                      echo '<option selected value="' . $op . '">' . $op . '</option>';
                    } else {
                      echo '<option value="' . $op . '">' . $op . '</option>';
                    }
                  }

                  echo '</select>';

                  if (isset($r->link) && trim($r->link) !== '') { 
                    echo '<span class="help-block"><a href="' . site_url($r->link) . '">Additional Setting</a></span>';
                 } 
       
                ?>

                 
                 
                  <script>
                    $('#<?php echo $r->title; ?>').on('change', function() {
                      var this_title = $(this).attr('id');
                      var options = $('#<?php echo $r->title; ?> option');
                      var values = $.map(options, function(option) {
                        return option.value;
                      });
                      var this_val = values + ';' + $(this).val();

                      $.post("<?php echo base_url() . 'superuser/settings/edt/'; ?>" + this_title, {
                        value: this_val,
                        csrf_test_name: Cookies.get('csrf_cookie_name')
                      });

                    })
                  </script>

                <?php }elseif($r->tipe === 'button'){ ?>
                  <a role="button" onclick="if (confirm('Apakah Anda yakin ingin menghapus data?')) { return true; } else { return false; }" href="<?php echo site_url($r->link); ?>" class="btn btn-danger">HAPUS !</a>
                <?php } ?>  

                

              </td>
            </tr>
          <?php $nomor++;
          } ?>
        </tbody>
      </table>
    <?php } ?>
  </div>
</div>

<script>
  $('.update_me').keyup(function() {
    var this_title = $(this).attr('id');
    var this_val = $(this).val();

    $.post("<?php echo base_url() . 'superuser/settings/edt/'; ?>" + this_title, {
      value: this_val,
      csrf_test_name: Cookies.get('csrf_cookie_name')
    });
  });

  $(document).ready(function() {
    $('.settings_texteditor').summernote({
      height: 300,
      maximumImageFileSize: 1048576,
      callbacks: {
        onImageUpload: function(files, editor, welEditable) {
          for (var i = files.length - 1; i >= 0; i--) {
            sendFile(files[i], this);
          }
        },
        onChange: function(contents, $editable) {

          var this_title = $(this).attr('id');
          var this_val = contents;

          // console.log('onChange:', contents, $editable);
          $.post("<?php echo base_url() . 'superuser/settings/edt/'; ?>" + this_title, {
            value: this_val
          });
        }
      }
    });
  });

  // function sendFile(file, el) {
  //     var form_data = new FormData();
  //     form_data.append('file', file);
  //     $.ajax({
  //       data: form_data,
  //       type: "POST",
  //       url: '<?php echo site_url('editor/upload') ?>',
  //       cache: false,
  //       contentType: false,
  //       processData: false,
  //       success: function(url) {
  //         $(el).summernote('editor.insertImage', url);
  //       }
  //     });
  //   }
</script>