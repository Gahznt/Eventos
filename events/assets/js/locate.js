function getURL() {
    return new URL(window.location);
}

$( document ).ready(function() {

    $(document).on('click', '.btnLanguage', function (e) {
        e.preventDefault();
       let languageId = $(this).data('language');
       let path = getURL().pathname.replace(/\/es/g,"").replace(/\/en/g,"").replace(/\/pt_br/g,"");
       let url = getURL().origin;

       if (languageId !== "") {
           url = url.replace(url, `${url}/${languageId}${path}`);
       }else{
           url = url.replace(url, `${url}${path}`);
       }

        window.location.href = url;
    });

});

