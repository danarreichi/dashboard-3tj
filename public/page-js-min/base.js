function dateIndFormat(e){var a=new Date(e).toLocaleString("en-US",{timeZone:"Asia/Jakarta"});return new Intl.DateTimeFormat("en",{day:"2-digit",month:"short",year:"numeric"}).format(new Date(a))}function dateIndWithTimeFormat(e){var a=new Date(e).toLocaleString("en-US",{timeZone:"Asia/Jakarta"});return new Intl.DateTimeFormat("en",{day:"2-digit",month:"short",year:"numeric",hour:"2-digit",minute:"2-digit",second:"2-digit"}).format(new Date(a))}function clearForm(e,a){for(var t=document.getElementById(e),n=t.getElementsByTagName("input"),r=t.getElementsByTagName("select"),i=0;i<n.length;i++)n[i].value="";for(i=0;i<r.length;i++)r[i].selectedIndex=0}function clearInputErrors(){$(".form-control").removeClass("is-invalid"),$(".invalid-feedback").remove()}function addOrUpdateQueryParam(e,a){var t=window.location.href,n=new URL(t);n.searchParams.set(e,a);var r=n.toString();return window.history.replaceState({},document.title,r),r}function getQueryParamValue(e){var a=window.location.href;return new URL(a).searchParams.get(e)}var dataTablesIdLang={sEmptyTable:"Tidak ada data yang tersedia pada tabel ini",sProcessing:"Sedang memproses...",sLengthMenu:"Tampilkan _MENU_ entri",sZeroRecords:"Tidak ditemukan data yang sesuai",sInfo:"Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",sInfoEmpty:"Menampilkan 0 sampai 0 dari 0 entri",sInfoFiltered:"(disaring dari _MAX_ entri keseluruhan)",sInfoPostFix:"",sSearch:"Cari:",sUrl:"",oPaginate:{sFirst:"Pertama",sPrevious:"Sebelumnya",sNext:"Berikutnya",sLast:"Terakhir"}};
