require('dependent-dropdown');

$('#permission_committee_edition').depdrop({
  depends: ['permission_committee_event'],
  url: $('#permission_committee_edition').data('route'),
  loading: false,
  placeholder: $('#permission_committee_edition').data('select'),
});

$(document).on('change', '#permission_committee_edition', function (e) {
  loadPermissionCommittee(this.value, this.dataset.route_find_permissions)
});

$(document).on('submit', '.delete_permission_committee', function (e) {
  e.preventDefault();

  const row = $(this).closest('tr');

  $.ajax({
    url: this.dataset.route,
    type: 'DELETE',
    processData: false,
    contentType: false,
    beforeSend: () => {
      //
    },
    success: function (data, statusCode, xhr) {
      if (data.success) {
        row.remove();
      }
    },
    error: function (data) {
      //
    },
    complete: function () {
      //
    }
  });
});

$(document).on('submit', '#filter', function (e) {
  e.preventDefault();

  let $form = $(this);

  let dataForm = new FormData(this);

  const search = $('#search').val();

  if (search.length < 5) {
    $('#search-error').html('Preencha pelo menos 5 caracteres');
    $('#search-error').show();
    return;
  }

  $.ajax({
    url: $(this).data('route') + `?search=${search}`,
    type: 'GET',
    processData: false,
    contentType: false,
    data: dataForm,
    beforeSend: () => {
      $('#search-error').hide();
      $('#permission_committee_user').html('<option value="">Selecione</option>');

      $('#btnSearch').addClass('fa fa-spinner fa-spin');
    },
    success: function (data, statusCode, xhr) {
      $('#permission_committee_user').prop('disabled', false)
      data.forEach(user => {
        var newOption = new Option(user.name, user.id, false, false);
        $('#permission_committee_user').append(newOption).trigger('change');
      });
    },
    error: function (data) {
      $form.addClass('was-validated');

      $form.html($(data.responseText).html());

      $form.find('.card-header').addClass('valid');
      $form.find('.form-error').closest('.card').find('.card-header').removeClass('valid').addClass('invalid');

      $('.invalid-feedback').show();
    },
    complete: function () {
      $('#btnSearch').removeClass('fa fa-spinner fa-spin');
    }
  });
});

$(document).on('submit', 'form#permissionCommitteeForm', function (e) {
  e.preventDefault();

  let $form = $(this);

  let dataForm = new FormData(this);

  $.ajax({
    url: $(this).attr('action'),
    type: 'POST',
    processData: false,
    contentType: false,
    data: dataForm,
    beforeSend: () => {
      $form.find('.card-header').removeClass('invalid').removeClass('valid');
      $form.removeClass('was-validated');
      $('.invalid-feedback').html('');
      $('.invalid-feedback').hide();
      $('#btnLoad').addClass('fa fa-spinner fa-spin');
    },
    success: function (data, statusCode, xhr) {
      window.location = $('#manager-back-route').prop('href');
    },
    error: function (data) {
      $form.addClass('was-validated');

      $form.html($(data.responseText).html());

      $form.find('.card-header').addClass('valid');
      $form.find('.form-error').closest('.card').find('.card-header').removeClass('valid').addClass('invalid');

      $('.invalid-feedback').show();
    },
    complete: function () {
      $('#btnLoad').removeClass('fa fa-spinner fa-spin');
    }
  });
})

function loadPermissionCommittee (editionId, route) {
  const permissionCommitteeTable = $('#permission_committee_table');
  $.ajax({
    url: `${route}?editionId=${editionId}`,
    type: 'GET',
    processData: false,
    contentType: false,
    beforeSend: () => {
      permissionCommitteeTable.html('Carregando...');
    },
    success: function (data, statusCode, xhr) {
      permissionCommitteeTable.html('');

      if (data.length === 0) {
        permissionCommitteeTable.html(`
          <tr>
            <td colSpan="5">Nenhum item encontrado!</td>
          </tr>
        `);
      }

      data.forEach(user => {
        let newRow = `
        <tr>
            <td>${user.id}</td>
            <td>${user.userName}</td>
            <td>${user.editionName}</td>
            <td>${user.divisionName}</td>
            <td class="text-right">
                <form id="${user.id}" class="delete_permission_committee" data-route="/api/v1/permissions/${user.id}/committee">
                    <button type="submit" class="btn btn-sm btn-outline-primary"><i
                                class="fas fa-trash"></i></button>
                </form>
            </td>
        </tr>
        `
        permissionCommitteeTable.append(newRow);
      });
    },
    error: function (data) {
      permissionCommitteeTable.html(`
          <tr>
            <td colSpan="5">Nenhum item encontrado!</td>
          </tr>
        `);
    }
  });
}
