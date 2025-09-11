@extends('admin.layouts.master')

@section('main_section')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h4>शॉप ज़ोन टू जियो ज़ोन प्रबंधन</h4>
      <button class="btn btn-primary btn-sm" id="addBtn">नया जोड़ें</button>
    </div>
    <div class="card-body table-responsive">
      <table class="table table-bordered" id="geoTable">
        <thead>
          <tr>
            <th>#</th>
            <th>देश</th>
            <th>ज़ोन</th>
            <th>जियो ज़ोन</th> 
            <th>क्रिया</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="geoModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="geoForm" class="modal-content">
      @csrf
      <input type="hidden" id="record_id">
      <div class="modal-header">
        <h5 class="modal-title">जियो ज़ोन असाइन फॉर्म</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>देश <span class="text-danger">*</span></label>
          <select name="country_id" id="country_id" class="form-select">
            <option value="">चुनें</option>
            @foreach($countries as $country)
              <option value="{{ $country->country_id }}">{{ $country->country_name }}</option>
            @endforeach
          </select>
          <small class="text-danger error-country_id"></small>
        </div>

        <div class="mb-3">
          <label>ज़ोन <span class="text-danger">*</span></label>
          <select name="zone_id" id="zone_id" class="form-select">
            <option value="">चुनें</option>
            @foreach($zones as $zone)
              <option value="{{ $zone->zone_id }}">{{ $zone->zone_name }}</option>
            @endforeach
          </select>
          <small class="text-danger error-zone_id"></small>
        </div>

        <div class="mb-3">
          <label>जियो ज़ोन <span class="text-danger">*</span></label>
          <select name="geo_zone_id" id="geo_zone_id" class="form-select">
            <option value="">चुनें</option>
            @foreach($geoZones as $gz)
              <option value="{{ $gz->geo_zone_id }}">{{ $gz->geo_zone_name }}</option>
            @endforeach
          </select>
          <small class="text-danger error-geo_zone_id"></small>
        </div>

        <div id="formError" class="text-danger mt-2"></div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">सेव करें</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">रद्द करें</button>
      </div>
    </form>
  </div>
</div>

<!-- Scripts -->
<script>
  
$(function () {
  const geoModal = new bootstrap.Modal(document.getElementById('geoModal'));
  let table = $('#geoTable').DataTable({
    ajax: '{{ route("geo-zones.fetch") }}',
    columns: [
      { data: null, render: (d, t, r, m) => m.row + 1 },
      { data: 'country.country_name' },
      { data: 'zone.zone_name' },
      { data: 'geo_zone.geo_zone_name' },
      {
        data: null,
        render: row => `
          <button class="btn btn-sm btn-info editBtn" data-id="${row.association_id}">Edit</button>
          <button class="btn btn-sm btn-danger deleteBtn" data-id="${row.association_id}">Delete</button>
        `
      }
    ]
  });

  function clearForm() {
    $('#geoForm')[0].reset();
    $('#record_id').val('');
    $('.text-danger').text('');
    $('.form-select').removeClass('is-invalid');
    $('#formError').text('');
  }

  $('#addBtn').click(() => {
    clearForm();
    geoModal.show();
  });

  $('#geoForm').submit(function (e) {
    e.preventDefault();
    $('.text-danger').text('');
    $('.form-select').removeClass('is-invalid');

    const id = $('#record_id').val();
    const url = id ? `/admin/geo-zones/update/${id}` : `{{ route('geo-zones.store') }}`;

    $.ajax({
      url,
      method: 'POST',
      data: $(this).serialize(),
      success: res => {
        geoModal.hide();
        $('#geoTable').DataTable().ajax.reload();
        toastr.success(res.success);
      },
      error: xhr => {
        if (xhr.status === 422) {
          const errors = xhr.responseJSON.errors;
          $.each(errors, (key, val) => {
            $(`#${key}`).addClass('is-invalid');
            $(`.error-${key}`).text(val[0]);
          });
        } else {
          $('#formError').text('कुछ त्रुटि हुई है।');
          toastr.error('सेव नहीं किया जा सका');
        }
      }
    });
  });

  $(document).on('click', '.editBtn', function () {
    clearForm();
    const id = $(this).data('id');
    $.get(`/admin/geo-zones/show/${id}`, res => {
      $('#record_id').val(res.association_id);
      $('#country_id').val(res.country_id);
      $('#zone_id').val(res.zone_id);
      $('#geo_zone_id').val(res.geo_zone_id);
      geoModal.show();
    });
  });

  $(document).on('click', '.deleteBtn', function () {
    if (!confirm('क्या आप वाकई हटाना चाहते हैं?')) return;
    const id = $(this).data('id');
    $.ajax({
      url: `/admin/geo-zones/destroy/${id}`,
      method: 'DELETE',
      data: { _token: '{{ csrf_token() }}' },
      success: res => {
        $('#geoTable').DataTable().ajax.reload();
        toastr.success(res.success);
      },
      error: () => toastr.error('डिलीट नहीं किया जा सका')
    });
  });
});
</script>
@endsection
