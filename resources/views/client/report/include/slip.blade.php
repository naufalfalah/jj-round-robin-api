<td>
    <a href="javascript:void(0);" onclick="generatePDF('{{ route('user.report.slip', $data->hashid) }}')" class="text-success view_lead_detail" data-bs-toggle="tooltip"
        data-bs-placement="bottom" title="Download Invoice"
        data-bs-original-title="Download Invoice" aria-label="Download Invoice"><i class="fa-solid fa-download"></i></a>
    <a href="javascript:void(0);" onclick="viewPDF('{{ route('user.report.slip', $data->hashid) }}')" class="text-info view_lead_detail" data-bs-toggle="tooltip"
        data-bs-placement="bottom" title="View Invoice"
        data-bs-original-title="View Invoice" aria-label="View Invoice"><i class="fa-solid fa-eye"></i></a>
</td>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script>
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    })
    
    if (typeof generatePDF === 'undefined') {
        function generatePDF(url) {
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    html2pdf(html, {
                        margin: 1,
                        filename: 'invoice.pdf',
                        html2canvas: { scale: 2 },
                        jsPDF: { format: 'a4', orientation: 'portrait' }
                    }).then(pdf => {
                        pdf.output('dataurlnewwindow');
                    });
                });
        }
    }

    if (typeof viewPDF === 'undefined') {
        function viewPDF(url) {
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    html2pdf().from(html).output('blob')
                        .then(blob => {
                            const pdfUrl = URL.createObjectURL(blob);
                            window.open(pdfUrl, '_blank');
                        });
                });
        }
    }
</script>
