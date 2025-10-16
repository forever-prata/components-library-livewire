import '@govbr-ds/core/dist/core-init.js';
import '@govbr-ds/core/dist/core.min.js';
import 'materialize-css/dist/js/materialize.min.js';

document.addEventListener('DOMContentLoaded', function() {
    if (window.designSystem === 'materialize') {
        var elems = document.querySelectorAll('select');
        var instances = M.FormSelect.init(elems, {});
    }
});
