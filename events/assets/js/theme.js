$(function() {
  let step = parseInt($('#step').val());
  const form = $('#themeForm');

  $(`.stepsTab[data-step=${step}]`).show();
  $(`#themeSteps .step[data-step="${step}"]`).addClass('active');
  $('#btCadastroVoltar').toggleClass('d-none', [1, 4].includes(step));

  $('#btCadastro').toggleClass('d-none', step == 5);

  for(i=step;i>0;i--) {
    $(`#themeSteps .step[data-step="${i}"]`).addClass('complete');
  }

  $('#btCadastro').on('click', function() {
    $('#btCadastro, #btCadastroVoltar').addClass('disabled');
    $.ajax({
      type: 'POST',
      url: form.attr('action'),
      data: form.serialize(),
      dataType: 'JSON'
    }).done(function(data) {
      step = data.step;
      $('#step').val(step);
      $(`#themeSteps .step[data-step="${step-1}"]`).removeClass('active').addClass('complete');
      $(`.stepsTab`).hide();
      $(`#themeSteps .step[data-step="${step}"]`).addClass('active');
      $(`.stepsTab[data-step=${step}]`).show();
    }).fail(function(error) {
      console.log(error);
    }).always(function() {
      $('#btCadastroVoltar').removeClass('disabled d-none');
      if(step == 4) {
        $('#btCadastro, #btCadastroVoltar').hide();
        $(`#themeSteps .step[data-step="${step}"]`).addClass('complete');
      } else {
        $('#btCadastro').removeClass('disabled');
      }
    });
  });

  $('#btCadastroVoltar').on('click', function() {
    const ns = parseInt($('#step').val())-1;
    $('#themeSteps .step').removeClass('active');
    $('.stepsTab').hide();
    $('#step').val(ns);
    $(`#themeSteps .step[data-step="${ns}"]`).addClass('active');
    $(`.stepsTab[data-step=${ns}]`).show();
    $('#btCadastroVoltar').toggleClass('disabled d-none', ns == 1);
  });

  $('.card.theme').each(function() {
    const inputs = $(this).find('input, textarea');
    const header = $(this).find('.card-header');
    inputs.on('input change', function() {
      header.toggleClass('valid', inputs.filter(function() {
        return !$(this)[0].checkValidity();
      }).length == 0);
    });
  });

  $('#addBibliografia').on('click', function() {
    $('#bibliografias').append($('#bibliografias .bibliografia').first().clone());
    $('#delBibliografia').toggleClass('disabled', $('#bibliografias .bibliografia').length == 1);
  });

  $('#delBibliografia').on('click', function() {
    $('#bibliografias .bibliografia').last().remove();
    $(this).toggleClass('disabled', $('#bibliografias .bibliografia').length == 1);
  });

  $(document).on('click', '.btPesquisador .add', function() {
    $('#pesquisadores').append($('#pesquisadores .pesquisador').first().clone());
    cloneDivs('#pesquisadores .pesquisador');
  });

  $(document).on('click', '.btPesquisador .del', function() {
    $(this).closest('.pesquisador').remove();
    cloneDivs('#pesquisadores .pesquisador');
  });

  $(document).on('click', '.btRevisor .add', function() {
    $('#revisores').append($('#revisores .revisor').first().clone());
    cloneDivs('#revisores .revisor');
  });

  $(document).on('click', '.btRevisor .del', function() {
    $(this).closest('.revisor').remove();
    cloneDivs('#revisores .revisor');
  });

  cloneDivs('#pesquisadores .pesquisador');
  cloneDivs('#revisores .revisor');

  function cloneDivs(sel) {
    $(sel).not(':first').each(function(i) {
      $(this).find('input, select').each(function() {
        $(this).attr('id', `${$(this).attr('id').split('-')[0]}-${i+1}`);
      });
      $(this).find('label').each(function() {
        $(this).attr('for', `${$(this).attr('for').split('-')[0]}-${i+1}`);
      });
      $(this).find('.del').removeClass('disabled');
      const title = $(this).find('h1');
      title.text(title.data('title').replace('#', i+2));
    });
  }
});