$(function() {
    $("#example_subDependentExample").depdrop({
        depends: ['example_dependentExample'],
        url: route
    });
});