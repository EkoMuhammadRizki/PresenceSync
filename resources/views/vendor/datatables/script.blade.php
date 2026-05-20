$(function(){window.{{ config('datatables-html.namespace', 'LaravelDataTables') }}=window.{{ config('datatables-html.namespace', 'LaravelDataTables') }}||{};window.{{ config('datatables-html.namespace', 'LaravelDataTables') }}["%1$s"]=$("#%1$s").DataTable(%2$s);

$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
{{ config('datatables-html.namespace', 'LaravelDataTables') }}["%1$s"].on('click', '[data-destroy]', function (e) {
e.preventDefault();
var $btn = $(this);
var destroyUrl = $btn.data('destroy');
var label = $btn.data('label') || 'data ini';

(window.konfirmasiHapus
    ? window.konfirmasiHapus({ text: 'Data "' + label + '" akan dihapus permanen!' })
    : Promise.resolve({ isConfirmed: confirm("{{ __('Are you sure to delete this record?') }}") })
).then(function(result) {
    if (!result.isConfirmed) return;
    axios.delete(destroyUrl, { '_method': 'DELETE' })
        .then(function (response) {
            {{ config('datatables-html.namespace', 'LaravelDataTables') }}["%1$s"].ajax.reload();
            if (window.SwalSuccess) {
                window.SwalSuccess.fire({ title: 'Berhasil!', text: 'Data berhasil dihapus.' });
            }
        })
        .catch(function (error) {
            if (window.SwalError) {
                window.SwalError.fire({ title: 'Gagal!', text: 'Terjadi kesalahan saat menghapus data.' });
            }
            console.error(error);
        });
});
});

});
