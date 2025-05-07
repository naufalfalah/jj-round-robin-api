(function ($) {
    var fileUploadCount = 0;

    $.fn.fileUpload = function () {
        return this.each(function () {
            var fileUploadDiv = $(this);
            var fileUploadId = `fileUpload-${++fileUploadCount}`;

            // Creates HTML content for the file upload area.
            var fileDivContent = `
                <label for="${fileUploadId}" class="file-upload">
                    <div>
                        <i class="fa-solid fa-file-arrow-up"></i>
                        <p>Drag & Drop file here or <u><b>Choose File</b></u></p>
                    </div>
                    <input type="file" id="${fileUploadId}" class="file-upload-input" name='upload_file' accept=".xlsx, .xls, .csv" hidden />
                </label>
            `;

            fileUploadDiv.html(fileDivContent).addClass("file-container");

            var table = null;
            var tableBody = null;
            // Creates a table containing file information.
            function createTable() {
                table = $(`
                    <table class="table" id="csvfileupload">
                        <thead>
                            <tr>
                                <th style="width: 30%;">File Name</th>
                                <th style="width: 20%;">Size</th>
                                <th>Type</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                `);

                tableBody = table.find("tbody");
                fileUploadDiv.append(table);
            }

            // Adds the information of uploaded files to table.
            function handleFiles(files) {
                if (!table) {
                    createTable();
                }

                tableBody.empty();
                if (files.length > 0) {
                    $.each(files, function (index, file) {
                        var fileName = file.name;
                        var fileSize = (file.size / 1024).toFixed(2) + " KB";
                        var fileType = file.type;
                        var preview = fileType.startsWith("image")
                            ? `<img src="${URL.createObjectURL(file)}" alt="${fileName}" height="30">`
                            : `<i class="material-icons-outlined">visibility_off</i>`;

                        tableBody.append(`
                            <tr>
                                <td>${fileName}</td>
                                <td>${fileSize}</td>
                                <td>${fileType}</td>
                                <td><button type="button" class="deleteBtn"><i class="fa fa-trash"></i></button></td>
                            </tr>
                        `);
                    });

                    tableBody.find(".deleteBtn").click(function () {
                        $(this).closest("tr").remove();

                        if (tableBody.find("tr").length === 0) {
                            tableBody.append('<tr><td colspan="6" class="no-file">No files selected!</td></tr>');
                        }
                    });
                }
            }

            // Events triggered after dragging files.
            fileUploadDiv.on({
                dragover: function (e) {
                    // alert('drag');
                    e.preventDefault();
                    fileUploadDiv.toggleClass("dragover", e.type === "dragover");
                },
                drop: function (e) {
                    // alert('drop');
                    e.preventDefault();
                    fileUploadDiv.removeClass("dragover");
                    handleFiles(e.originalEvent.dataTransfer.files);
                    var fileInput = document.querySelector(".file-upload-input");
                    fileInput.files = e.originalEvent.dataTransfer.files;
                },
            });

            // Event triggered when file is selected.
            fileUploadDiv.find(`#${fileUploadId}`).change(function () {
                handleFiles(this.files);
            });
        });
    };
})(jQuery);
