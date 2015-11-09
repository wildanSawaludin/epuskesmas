<script type="text/javascript">
    $(function(){
    
        $('#code_mst_inv_barang').change(function(){
          var code = $(this).val();
          $.ajax({
            url : '<?php echo site_url('inventory/permohonanbarang/get_nama') ?>',
            type : 'POST',
            data : 'code=' + code,
            success : function(data) {
              $('#nama_barang').html(data);
            }
          });

          return false;
        });
        
        $('#form-ss').submit(function(){
            var data = new FormData();
            $('.notice').html('<div class="alert">Mohon tunggu, proses simpan data....</div>');
            $.each($('#file')[0].files, function(i, file){
                data.append('userfile', file);
            });

            data.append('keterangan', $('input[name="keterangan"]').val());
            data.append('jumlah', $('input[name="jumlah"]').val());
            data.append('nama_barang', $('input[name="nama_barang"]').val());
            data.append('id_inv_permohonan_barang', $('input[name="id_dokumen"]').val());
            data.append('code_mst_inv_barang', $('input[name="code_mst_inv_barang"]').val());

            $.ajax({
                cache : false,
                contentType : false,
                processData : false,
                type : 'POST',
                url : '<?php echo current_url() ?>',
                data : data,
                success : function(data){
                var obj = $.parseJSON(data);
                
                $('.notice').html('<div class="alert">' + obj.notice + '</div>');

                if(obj.error == 0) {
                	alert(obj.notice);
                	$("#popup_document").jqxWindow('close');
                    $("#jqxgrid_document").jqxGrid('updatebounddata', 'cells');
                }
            }
            });

            return false;
        });
     /*   var sites = "<?php echo site_url();?>";
        $('.autocomplete').autocomplete({
                // serviceUrl berisi URL ke controller/fungsi yang menangani request kita
                serviceUrl: sites+'/inventory/permohonanbarang/search',
                // fungsi ini akan dijalankan ketika user memilih salah satu hasil request
                onSelect: function (suggestion) {
                    $('#v_nama').val(''+suggestion.nim); // membuat id 'v_nim' untuk ditampilkan\
                }
            });*/
    });
</script>
<div class="notice"></div>
<input type="text" name="id_dokumen" value="<?php echo $id_dokumen ?>" />
<input type="text" name="id_dokumen_file" value="<?php echo $id_dokumen_file ?>" />
	<div class="row">
    <?php echo form_open(current_url(), 'id="form-ss"') ?>
           
          <div class="box-body">
            <div class="form-group">
              <label>Kode Barang</label>
               <!-- <input placeholder=" kodebarang" name="kodebarang" size="20" class="input ac_input"  type="text" class='autocomplete nama' id="autocomplete1"> -->
                <select  name="code_mst_inv_barang" id="code_mst_inv_barang" class="form-control">
                  <option value=""
                  </option>
                  <?php foreach($kodebarang as $barang) : ?>
                    <?php $select = $barang->code == set_value('kodebarang') ? 'selected' : '' ?>
                    <option value="<?php echo $barang->code ?>" <?php echo $select ?>><?php echo $barang->code ?></option>
                  <?php endforeach ?>
              </select>
            </div>
            <div class="form-group">
              <label>Nama Baranga</label>
              <select name="nama_barang" id="nama_barang"  class="form-control">
                  <option value="">Pilih Nama Barang</option>
              </select>
            </div>
            <div class="form-group">
              <label>Jumlah</label>
              <input type="text" class="form-control" name="jumlah" placeholder="Nama" value="<?php 
                if(set_value('value')=="" && isset($value)){
                  echo $value;
                }else{
                  echo  set_value('value');
                }
                ?>">
            </div>
            <div class="form-group">
              <label>Keterangan</label>
              <textarea class="form-control" name="keterangan" placeholder="Keterangan"><?php 
                  if(set_value('keterangan')=="" && isset($keterangan)){
                    echo $keterangan;
                  }else{
                    echo  set_value('keterangan');
                  }
                  ?></textarea>
            </div>
        </div>
        <div class="box-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <button type="submit" class="btn btn-warning">Kembali</button>
        </div>
    </div>
</form>