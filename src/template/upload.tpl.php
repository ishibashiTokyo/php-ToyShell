<script>
  // D&D upload form
  document.addEventListener("DOMContentLoaded", function () {
    var fileArea = document.getElementById('drag-drop-area');
    var fileInput = document.getElementById('fileInput');

    fileArea.addEventListener('dragover', function (e) {
      e.preventDefault();
      fileArea.classList.add('dragover');
      this.style.background = '#e1e7f0';
    });

    fileArea.addEventListener('dragleave', function (e) {
      e.preventDefault();
      fileArea.classList.remove('dragover');
      this.style.background = '#282c34';
    });
    fileArea.addEventListener('drop', function (e) {
      e.preventDefault();
      this.style.background = '#282c34';
      fileArea.classList.remove('dragenter');
      var files = e.dataTransfer.files;
      fileInput.files = files;
    });
  }, false);
</script>
<style>
  #drag-drop-area {
    padding: 10px;
  }
  .drag-drop-inside {
    padding: 10px;
    text-align: center;
    border: 3px dashed #666;
  }
</style>
<form action="?upload" method="post" enctype="multipart/form-data">
  <div id="drag-drop-area">
    <div class="drag-drop-inside">
      <p class="drag-drop-info">ここにファイルをドロップ</p>
      <p>または</p>
      <p class="drag-drop-buttons"><input id="fileInput" type="file" value="ファイルを選択" name="upload_file[]" multiple></p>
      <input type="submit" value="アップロード実行">
    </div>
  </div>
</form>