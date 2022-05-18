require('dependent-dropdown');

$('#user_simplified_city').depdrop({
    depends: ['user_simplified_state'],
    url: $('#user_simplified_city').data('route'),
    loading: false,
    placeholder: $('#user_simplified_city').data('select'),
});