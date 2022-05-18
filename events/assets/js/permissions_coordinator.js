require('dependent-dropdown');

$(document).on('change', '#permission_coordinator_event', function (e) {
  $('#permission_coordinator_edition').prop('disabled', false);
});

$(document).on('change', '#permission_coordinator_edition', function (e) {
  loadDivisionCoordinator(this.value, this.dataset.route_find_permissions)
});

$(document).on('submit', '.delete_division_coordinator', function (e) {
  e.preventDefault();

  const row = $(this).closest('tr');

  $.ajax({
    url: `${this.dataset.route}/${this.id}`,
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

$('#permission_coordinator_edition').depdrop({
  depends: ['permission_coordinator_event'],
  url: $('#permission_coordinator_edition').data('route'),
  loading: false,
  placeholder: $('#permission_coordinator_edition').data('select'),
});

$(document).on('submit', 'form#permissionCoordinatorForm', function (e) {
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
      $('#permission_coordinator_coordinator').html('<option value="">Selecione</option>');

      $('#btnSearch').addClass('fa fa-spinner fa-spin');
    },
    success: function (data, statusCode, xhr) {
      $('#permission_coordinator_coordinator').prop('disabled', false)
      data.forEach(user => {
        var newOption = new Option(user.name, user.id, false, false);
        $('#permission_coordinator_coordinator').append(newOption).trigger('change');
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

function loadDivisionCoordinator (editionId, route) {
  const divisionCoordinatorTable = $('#division_coordinator_table');
  $.ajax({
    url: `${route}?editionId=${editionId}`,
    type: 'GET',
    processData: false,
    contentType: false,
    beforeSend: () => {
      divisionCoordinatorTable.html('Carregando...');
    },
    success: function (data, statusCode, xhr) {
      divisionCoordinatorTable.html('');

      if (data.length === 0) {
        divisionCoordinatorTable.html(`
          <tr>
            <td colSpan="5">Nenhum item encontrado!</td>
          </tr>
        `);
      }

      data.forEach(coordinator => {
        let newRow = `
        <tr>
            <td>${coordinator.id}</td>
            <td>${coordinator.coordinatorName}</td>
            <td>${coordinator.editionName}</td>
            <td>${coordinator.divisionName}</td>
            <td class="text-right">
                <form id="${coordinator.id}" class="delete_division_coordinator" data-route="/api/v1/permissions">
                    <button type="submit" class="btn btn-sm btn-outline-primary"><i
                                class="fas fa-trash"></i></button>
                </form>
            </td>
        </tr>
        `
        divisionCoordinatorTable.append(newRow);
      });
    },
    error: function (data) {
      divisionCoordinatorTable.html(`
          <tr>
            <td colSpan="5">Nenhum item encontrado!</td>
          </tr>
        `);
    }
  });
}