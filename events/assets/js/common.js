$(function() {

  $('[data-toggle="tooltip"]').tooltip();

  const buscaForm = $('#buscaHeader form');
  $('#buscaHeader a').on('click', function() {
    if(buscaForm.is(':visible')) {
      if(buscaForm[0].checkValidity()) {
        buscaForm.submit();
      } else {
        buscaForm.hide();
      }
    } else {
      buscaForm.show();
    }
  });

  $.fn.customSelect2 = function (args) {
    return this.each(function() {
      const data = $(this).data();
      if($(this).attr('multiple')) {
        let placeholder = $(this).attr('select') ? $(this).attr('select') : $(this).parent().parent().attr('select');
        $(this).select2MultiCheckboxes({
          templateSelection: function(selected) {
            if(Array.isArray(selected) && selected.length > 0) {
              return `${selected.length} ${data.selected ? data.selected : 'selecionados'}`;
            } else {
              return `${data.select ? data.select : placeholder}`;
            }
          }
        });
      } else {
        // window.toSelect2($(this));
        $(this).select2(args);
      }
    });
  };

  $('select:not(.no-select2)').customSelect2();

  $('.setClipboard').on('click', function() {
    setClipboard($(this).data('value'));
  });

  const deleteWord = $('#modalDelete').data('word');
  const resetDelete = function() {
    $('#deleteInput').val('');
    $('#modalDelete').find('button, input').not('#deleteConfirm').removeAttr('disabled');
    $('#deleteConfirm').prop('disabled', 'disabled').find('span').addClass('d-none');
  }

  $('#modalDelete').modal({
    show: false,
    backdrop: 'static'
  });
  $(document).on('click', '.tdDel', function(e) {
    e.preventDefault();

    $('.tdDel').addClass('disabled');
    const data = $(this).data();
    const row = $(this).closest('tr');
    $('#modalDelete').find('.modal-body p').each(function() {
      $(this).html($(this).html().split('$type').join(data.type).split('$name').join(data.name).split('$word').join(deleteWord));
    });
    $('#modalDelete').on('hidden.bs.modal', function() {
      resetDelete();
      $(this).html($(this).html().split(data.type).join('$type').split(data.name).join('$name'));
    });
    $('#modalDelete').modal('show');
    $('#deleteInput').on('input', function() {
      $('#deleteConfirm').prop('disabled', $('#deleteInput').val().toLowerCase() != deleteWord.toLowerCase());
    });
    $('#deleteConfirm').on('click', function() {
      $('#modalDelete').find('button, input').prop('disabled', 'disabled');
      $('#deleteConfirm').find('span').removeClass('d-none');
      $.ajax({
        url: data.path,
        type: 'DELETE', // DELETE
        data: {
          _method: 'DELETE',
          _token: data.token
        }
      }).done(function() {
        if(data.dt) {
          // datatable
          row.closest('table').DataTable().row(row).remove().draw();
          $('#modalDelete').modal('hide');
        } else {
          // framework
          if(data.action == 'remove') {
            $('#modalDelete').modal('hide');
            row.remove();
          } else {
            location.reload();
          }
        }
      }).fail(function() {
        console.log('erro');
      }).always(function() {
        $('.tdDel').removeClass('disabled');
        resetDelete();
      });
    });
  });

  $(document).on('click', '.delForm', function() {
    const data = $(this).data();
    $('#modalDelete').find('.modal-body p').each(function() {
      $(this).html($(this).html().split('$type').join(data.type).split('$name').join(data.name).split('$word').join(deleteWord));
    });
    $('#modalDelete').on('hidden.bs.modal', function() {
      resetDelete();
      $(this).html($(this).html().split(data.type).join('$type').split(data.name).join('$name'));
    });
    $('#modalDelete').modal('show');
    $('#deleteInput').on('input', function() {
      $('#deleteConfirm').prop('disabled', $('#deleteInput').val().toLowerCase() != deleteWord.toLowerCase());
    });
    $('#deleteConfirm').on('click', function() {
      $('#modalDelete').find('button, input').prop('disabled', 'disabled');
      $('#deleteConfirm').find('span').removeClass('d-none');
      $.ajax({
        url: `${data.path}/${data.id}`,
        type: 'POST', // DELETE
        data: {
          _method: 'DELETE',
          _token: data.token
        }
      }).done(function() {
        $('#modalDelete').modal('hide');
        $('#itemDeleted').removeClass('d-none').find('.message').text(`${data.name} excluido`);
        $(`#${data.form}`).trigger('reset');
        $(`#${data.form} *[type="hidden"]`).val('');
        $(`#${data.form} .delForm`).val('');
        $(`#${data.form} .validCard > .card-header`).removeClass('valid');
        tinymce.triggerSave();
        $(`#${data.form}`).find('select').val('').change();
      }).fail(function() {
        console.log('erro');
      }).always(function() {
        $('.delForm').addClass('d-none').data('id', null);
        resetDelete();
      });
    });
  });

  $('#itemDeleted .close').on('click', function() {
    $('#itemDeleted').addClass('d-none');
  });

  tinymce.init({
    selector: 'textarea.htmleditor',
    language: 'pt_BR',
    height: 300,
    menubar: false,
    plugins: ['advlist autolink lists link image charmap print preview hr anchor pagebreak',
      'searchreplace wordcount visualblocks visualchars code fullscreen',
      'insertdatetime media nonbreaking save table directionality',
      'emoticons template paste textpattern imagetools code'
    ],
    toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor emoticons | code',
    image_advtab: true,
    init_instance_callback: function(editor) {
      editor.on('input', function(e) {
        $(`#${e.target.dataset.id}`).val(tinymce.get(e.target.dataset.id).getContent()).change();
      });
      editor.on('change', function(e) {
        $(`#${e.target.id}`).val(tinymce.get(e.target.id).getContent()).change();
      });
      editor.on('keydown',function(evt){
        if(evt.keyCode == 9) {
          editor.execCommand('mceInsertContent', false, '<span class="mce-nbsp">&emsp;&emsp;</span>');
          tinymce.dom.Event.cancel(evt);
          return;
        }
      });
    }
  });

  $('.validCard').each(function() {
    const inputs = $(this).find('input, textarea');
    const header = $(this).find('.card-header');
    inputs.on('input change', function() {
      header.toggleClass('valid', inputs.filter(function() {
        return !$(this)[0].checkValidity();
      }).length == 0);
    });
  });

  $('.switchCheckbox').each(function() {
    const input = $(this).find('input');
    const checked = $(this).find('.true');
    const unchecked = $(this).find('.false');
    input.on('change', function() {
      checked.toggleClass('text-success', $(this).is(':checked'), $(this).val(0));
      unchecked.toggleClass('text-danger', !$(this).is(':checked'), $(this).val(1));
    }).change();
  });

  $('.sidebarCollapse').on('click', function () {
    $('#sidebar').toggleClass('active');
  });

  $('.navsidebar li a').on('mouseenter', function() {
    $(this).parent().addClass('active');
  }).on('mouseout', function() {
    $(this).parent().toggleClass('active', $(this).is('.active'));
  }).on('shown.bs.tab', function(e) {
    $('.navsidebar li').removeClass('active');
    $(e.target).parent().addClass('active');
    $('#sidebarnav li').removeClass('active');
    $(`#sidebarnav li a[href="${$(e.target).attr('href')}"]`).parent().addClass('active');
  });

  $('#sidebarnav li a').on('click', function() {
    $('#sidebarnav li a').parent().removeClass('active');
    $(this).parent().addClass('active');
    $('#sidebar').toggleClass('active');
  }).on('shown.bs.tab', function(e) {
    $('.navsidebar li').removeClass('active');
    $(`.navsidebar li a[href="${$(e.target).attr('href')}"]`).parent().addClass('active');
  });

  $('.nobc').closest('.content').addClass('nobcm');
});

const setClipboard = function(value) {
  var tempInput = document.createElement("input");
  tempInput.style = "position: absolute; left: -2000px; top: -2000px";
  tempInput.value = value;
  document.body.appendChild(tempInput);
  tempInput.select();
  document.execCommand("copy");
  document.body.removeChild(tempInput);
}

window.toSelect2 = function(obj) {
  obj.select2({
    closeOnSelect: true,
    minimumResultsForSearch: $(this).is('.filterSelect') ? 0 : -1,
    tags: false
  });
}
